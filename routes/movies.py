from flask import Blueprint, render_template, request, redirect, url_for, session, flash, current_app, jsonify
from functools import wraps
import mysql.connector
from config import DB_CONFIG

movies_bp = Blueprint("movies", __name__)


# ------------------------------------------------------------------
# Helpers
# ------------------------------------------------------------------

def login_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if "user_id" not in session:
            return redirect(url_for("auth.login"))
        return f(*args, **kwargs)
    return decorated


def db_query(sql, args=(), one=False):
    return current_app.config["query"](sql, args, one)

def db_exec(sql, args=()):
    return current_app.config["execute"](sql, args)


# ------------------------------------------------------------------
# Dashboard
# ------------------------------------------------------------------

@movies_bp.route("/dashboard")
@login_required
def dashboard():
    uid = session["user_id"]

    # stat cards
    stats = db_query(
        "SELECT total_watched, avg_rating, total_hours, fav_genre "
        "FROM user_stats WHERE user_id=%s", (uid,), one=True
    )
    if not stats:
        stats = {"total_watched": 0, "avg_rating": 0, "total_hours": 0, "fav_genre": "—"}

    # recent watchlist activity
    recent = db_query(
        """SELECT w.status, w.added_at, m.title, m.poster_url, m.movie_id
           FROM Watchlist w JOIN Movies m ON m.movie_id=w.movie_id
           WHERE w.user_id=%s ORDER BY w.added_at DESC LIMIT 5""",
        (uid,)
    )

    # genre distribution for pie chart
    genre_data = db_query(
        """SELECT g.genre_name, COUNT(*) as cnt
           FROM WatchHistory wh
           JOIN MovieGenres mg ON mg.movie_id=wh.movie_id
           JOIN Genres g       ON g.genre_id=mg.genre_id
           WHERE wh.user_id=%s
           GROUP BY g.genre_name ORDER BY cnt DESC LIMIT 6""",
        (uid,)
    )

    # monthly watch count for bar chart
    monthly = db_query(
        """SELECT DATE_FORMAT(watched_on,'%b %Y') as month_label,
                  YEAR(watched_on) as yr, MONTH(watched_on) as mo,
                  COUNT(*) as cnt
           FROM WatchHistory WHERE user_id=%s
           GROUP BY yr, mo ORDER BY yr DESC, mo DESC LIMIT 6""",
        (uid,)
    )
    monthly = list(reversed(monthly))

    return render_template("dashboard.html",
        stats=stats,
        recent=recent,
        genre_data=genre_data,
        monthly=monthly
    )


# ------------------------------------------------------------------
# Browse & Search
# ------------------------------------------------------------------

@movies_bp.route("/movies")
@login_required
def browse():
    uid     = session["user_id"]
    q       = request.args.get("q", "").strip()
    genre   = request.args.get("genre", "")

    sql = """
        SELECT DISTINCT m.movie_id, m.title, m.release_year, m.duration_mins,
               m.language, m.poster_url,
               ROUND(AVG(r.score),1) AS avg_score,
               COUNT(DISTINCT r.rating_id) AS rating_count,
               w.status AS my_status
        FROM Movies m
        LEFT JOIN Ratings r     ON r.movie_id = m.movie_id
        LEFT JOIN Watchlist w   ON w.movie_id = m.movie_id AND w.user_id = %s
        LEFT JOIN MovieGenres mg ON mg.movie_id = m.movie_id
        LEFT JOIN Genres g      ON g.genre_id = mg.genre_id
        WHERE 1=1
    """
    params = [uid]

    if q:
        sql += " AND m.title LIKE %s"
        params.append(f"%{q}%")
    if genre:
        sql += " AND g.genre_name = %s"
        params.append(genre)

    sql += " GROUP BY m.movie_id ORDER BY m.title"
    movies = db_query(sql, params)

    genres = db_query("SELECT genre_name FROM Genres ORDER BY genre_name")
    return render_template("movies.html", movies=movies, genres=genres, q=q, selected_genre=genre)


# ------------------------------------------------------------------
# Movie detail
# ------------------------------------------------------------------

@movies_bp.route("/movies/<int:mid>")
@login_required
def movie_detail(mid):
    uid = session["user_id"]

    movie = db_query(
        """SELECT m.*, ROUND(AVG(r.score),1) AS avg_score,
                  COUNT(DISTINCT r.rating_id) AS rating_count
           FROM Movies m LEFT JOIN Ratings r ON r.movie_id=m.movie_id
           WHERE m.movie_id=%s GROUP BY m.movie_id""",
        (mid,), one=True
    )
    if not movie:
        flash("Movie not found.", "error")
        return redirect(url_for("movies.browse"))

    genres = db_query(
        "SELECT g.genre_name FROM Genres g JOIN MovieGenres mg ON mg.genre_id=g.genre_id WHERE mg.movie_id=%s",
        (mid,)
    )
    reviews = db_query(
        """SELECT rv.review_text, rv.created_at, u.username
           FROM Reviews rv JOIN Users u ON u.user_id=rv.user_id
           WHERE rv.movie_id=%s ORDER BY rv.created_at DESC""",
        (mid,)
    )
    my_rating = db_query(
        "SELECT score FROM Ratings WHERE user_id=%s AND movie_id=%s",
        (uid, mid), one=True
    )
    my_status = db_query(
        "SELECT status FROM Watchlist WHERE user_id=%s AND movie_id=%s",
        (uid, mid), one=True
    )

    return render_template("movie_detail.html",
        movie=movie, genres=genres, reviews=reviews,
        my_rating=my_rating, my_status=my_status
    )


# ------------------------------------------------------------------
# Rate a movie
# ------------------------------------------------------------------

@movies_bp.route("/rate", methods=["POST"])
@login_required
def rate():
    uid   = session["user_id"]
    mid   = int(request.form["movie_id"])
    score = int(request.form["score"])

    existing = db_query(
        "SELECT rating_id FROM Ratings WHERE user_id=%s AND movie_id=%s",
        (uid, mid), one=True
    )
    if existing:
        db_exec("UPDATE Ratings SET score=%s, rated_at=NOW() WHERE user_id=%s AND movie_id=%s",
                (score, uid, mid))
    else:
        db_exec("INSERT INTO Ratings (user_id, movie_id, score) VALUES (%s,%s,%s)",
                (uid, mid, score))

    flash("Rating saved!", "success")
    return redirect(url_for("movies.movie_detail", mid=mid))


# ------------------------------------------------------------------
# Write a review
# ------------------------------------------------------------------

@movies_bp.route("/review", methods=["POST"])
@login_required
def review():
    uid  = session["user_id"]
    mid  = int(request.form["movie_id"])
    text = request.form["review_text"].strip()

    if text:
        db_exec(
            "INSERT INTO Reviews (user_id, movie_id, review_text) VALUES (%s,%s,%s)",
            (uid, mid, text)
        )
        flash("Review posted!", "success")

    return redirect(url_for("movies.movie_detail", mid=mid))


# ------------------------------------------------------------------
# Leaderboard
# ------------------------------------------------------------------

@movies_bp.route("/leaderboard")
@login_required
def leaderboard():
    rows = db_query(
        """SELECT m.movie_id, m.title, m.release_year, m.poster_url,
                  ROUND(AVG(r.score),2) AS avg_score,
                  COUNT(r.rating_id) AS votes,
                  RANK() OVER (ORDER BY AVG(r.score) DESC) AS rnk
           FROM Movies m
           JOIN Ratings r ON r.movie_id = m.movie_id
           GROUP BY m.movie_id
           HAVING votes >= 1
           ORDER BY avg_score DESC
           LIMIT 20"""
    )
    return render_template("leaderboard.html", movies=rows)


# ------------------------------------------------------------------
# Add movie (admin-style simple form)
# ------------------------------------------------------------------

@movies_bp.route("/movies/add", methods=["GET", "POST"])
@login_required
def add_movie():
    genres = db_query("SELECT * FROM Genres ORDER BY genre_name")

    if request.method == "POST":
        title    = request.form["title"].strip()
        year     = request.form.get("release_year") or None
        duration = request.form.get("duration_mins") or None
        language = request.form.get("language", "").strip()
        poster   = request.form.get("poster_url", "").strip()
        genre_ids = request.form.getlist("genres")

        mid = db_exec(
            "INSERT INTO Movies (title, release_year, duration_mins, language, poster_url) "
            "VALUES (%s,%s,%s,%s,%s)",
            (title, year, duration, language, poster or None)
        )
        for gid in genre_ids:
            db_exec("INSERT IGNORE INTO MovieGenres (movie_id, genre_id) VALUES (%s,%s)", (mid, gid))

        flash(f'"{title}" added!', "success")
        return redirect(url_for("movies.browse"))

    return render_template("add_movie.html", genres=genres)
