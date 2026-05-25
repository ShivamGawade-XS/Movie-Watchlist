import os
from dotenv import load_dotenv

load_dotenv()

DB_CONFIG = {
    "host":     os.getenv("DB_HOST", "localhost"),
    "user":     os.getenv("DB_USER", "root"),
    "password": os.getenv("DB_PASSWORD", ""),
    "database": os.getenv("DB_NAME", "movie_watchlist"),
    "autocommit": True,
}

SECRET_KEY = os.getenv("SECRET_KEY", "dev-secret-change-in-prod")
