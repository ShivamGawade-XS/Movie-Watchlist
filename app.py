from flask import Flask, session, redirect, url_for
from config import SECRET_KEY
import mysql.connector
from config import DB_CONFIG

app = Flask(__name__)
app.secret_key = SECRET_KEY


# ------------------------------------------------------------------
# DB helper
# ------------------------------------------------------------------

def get_db():
    return mysql.connector.connect(**DB_CONFIG)


def query(sql, args=(), one=False, commit=False):
    conn = get_db()
    cur  = conn.cursor(dictionary=True)
    cur.execute(sql, args)
    if commit:
        conn.commit()
        cur.close(); conn.close()
        return None
    rv = cur.fetchone() if one else cur.fetchall()
    cur.close(); conn.close()
    return rv


def execute(sql, args=()):
    conn = get_db()
    cur  = conn.cursor()
    cur.execute(sql, args)
    conn.commit()
    last_id = cur.lastrowid
    cur.close(); conn.close()
    return last_id


app.config["query"]   = query
app.config["execute"] = execute


# ------------------------------------------------------------------
# Register blueprints
# ------------------------------------------------------------------

from routes.auth     import auth_bp
from routes.movies   import movies_bp
from routes.watchlist import watchlist_bp

app.register_blueprint(auth_bp)
app.register_blueprint(movies_bp)
app.register_blueprint(watchlist_bp)


# ------------------------------------------------------------------
# Root redirect
# ------------------------------------------------------------------

@app.route("/")
def index():
    if "user_id" in session:
        return redirect(url_for("movies.dashboard"))
    return redirect(url_for("auth.login"))


if __name__ == "__main__":
    app.run(debug=True)
