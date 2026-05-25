from flask import Blueprint, render_template, request, redirect, url_for, session, flash, current_app
from werkzeug.security import generate_password_hash, check_password_hash

auth_bp = Blueprint("auth", __name__)


def db_query(sql, args=(), one=False):
    return current_app.config["query"](sql, args, one)

def db_exec(sql, args=()):
    return current_app.config["execute"](sql, args)


@auth_bp.route("/register", methods=["GET", "POST"])
def register():
    if request.method == "POST":
        username = request.form["username"].strip()
        email    = request.form["email"].strip()
        password = request.form["password"]

        # check duplicate
        existing = db_query(
            "SELECT user_id FROM Users WHERE email=%s OR username=%s",
            (email, username), one=True
        )
        if existing:
            flash("Username or email already taken.", "error")
            return render_template("register.html")

        hashed = generate_password_hash(password)
        db_exec(
            "INSERT INTO Users (username, email, password_hash) VALUES (%s,%s,%s)",
            (username, email, hashed)
        )
        flash("Account created! Please log in.", "success")
        return redirect(url_for("auth.login"))

    return render_template("register.html")


@auth_bp.route("/login", methods=["GET", "POST"])
def login():
    if request.method == "POST":
        email    = request.form["email"].strip()
        password = request.form["password"]

        user = db_query(
            "SELECT * FROM Users WHERE email=%s", (email,), one=True
        )
        if user and check_password_hash(user["password_hash"], password):
            session["user_id"]  = user["user_id"]
            session["username"] = user["username"]
            return redirect(url_for("movies.dashboard"))

        flash("Invalid credentials.", "error")

    return render_template("login.html")


@auth_bp.route("/logout")
def logout():
    session.clear()
    return redirect(url_for("auth.login"))
