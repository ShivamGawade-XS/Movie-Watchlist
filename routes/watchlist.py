from flask import Blueprint, render_template, request, redirect, url_for, session, flash, current_app
from functools import wraps

watchlist_bp = Blueprint("watchlist", __name__)


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
# My Watchlist page
# ------------------------------------------------------------------

@watchlist_bp.route("/watchlist")
@login_required
def my_watchlist():
    uid    = session["user_id"]
    status = request.args.get("status", "all")

    sql = """
        SELECT w.wl_id, w.status, w.added_at,
               m.movie_id, m.title, m.release_year, m.poster_url, m.duration_mins,
               r.score AS my_score
        FROM Watchlist w
        JOIN Movies m  ON m.movie_id = w.movie_id
        LEFT JOIN Ratings r ON r.movie_id = w.movie_id AND r.user_id = w.user_id
        WHERE w.user_id = %s
    """
    params = [uid]
    if status != "all":
        sql += " AND w.status = %s"
        params.append(status)
    sql += " ORDER BY w.added_at DESC"

    items = db_query(sql, params)

    counts = db_query(
        """SELECT status, COUNT(*) as cnt FROM Watchlist
           WHERE user_id=%s GROUP BY status""", (uid,)
    )
    count_map = {row["status"]: row["cnt"] for row in counts}

    return render_template("watchlist.html", items=items, status=status, count_map=count_map)


# ------------------------------------------------------------------
# Add to watchlist
# ------------------------------------------------------------------

@watchlist_bp.route("/watchlist/add", methods=["POST"])
@login_required
def add():
    uid = session["user_id"]
    mid = int(request.form["movie_id"])
    st  = request.form.get("status", "want_to_watch")

    existing = db_query(
        "SELECT wl_id FROM Watchlist WHERE user_id=%s AND movie_id=%s",
        (uid, mid), one=True
    )
    if existing:
        db_exec(
            "UPDATE Watchlist SET status=%s WHERE user_id=%s AND movie_id=%s",
            (st, uid, mid)
        )
        flash("Watchlist updated!", "success")
    else:
        db_exec(
            "INSERT INTO Watchlist (user_id, movie_id, status) VALUES (%s,%s,%s)",
            (uid, mid, st)
        )
        flash("Added to watchlist!", "success")

    return redirect(request.referrer or url_for("movies.browse"))


# ------------------------------------------------------------------
# Update status (triggers WatchHistory insert if -> watched)
# ------------------------------------------------------------------

@watchlist_bp.route("/watchlist/update-status", methods=["POST"])
@login_required
def update_status():
    uid = session["user_id"]
    mid = int(request.form["movie_id"])
    st  = request.form["status"]

    db_exec(
        "UPDATE Watchlist SET status=%s WHERE user_id=%s AND movie_id=%s",
        (st, uid, mid)
    )
    flash(f"Status updated to '{st}'.", "success")
    return redirect(request.referrer or url_for("watchlist.my_watchlist"))


# ------------------------------------------------------------------
# Remove from watchlist
# ------------------------------------------------------------------

@watchlist_bp.route("/watchlist/remove/<int:wl_id>", methods=["POST"])
@login_required
def remove(wl_id):
    uid = session["user_id"]
    db_exec("DELETE FROM Watchlist WHERE wl_id=%s AND user_id=%s", (wl_id, uid))
    flash("Removed from watchlist.", "success")
    return redirect(url_for("watchlist.my_watchlist"))
