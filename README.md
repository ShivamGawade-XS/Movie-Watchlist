# CineLog — Movie Watchlist & Rating Tracker
### Project 1 of 10 DBMS Projects

**Stack:** PHP · MySQL 8.0+ · HTML · Tailwind CSS · Alpine.js · Chart.js

---

## Setup

### 1. MySQL — Create the database

```bash
mysql -u root -p < schema.sql
mysql -u root -p < seed_data.sql
```

### 2. Configure PHP MySQL credentials

Edit `config.php` and update the database settings:

```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'movie_watchlist');
define('DB_USER', 'root');
define('DB_PASS', 'password');
```

### 3. Run the PHP app

```bash
php -S localhost:8000
```

Open `http://127.0.0.1:8000` in your browser.

---

## Project Structure

```
movie_watchlist/
├── config.php             # PHP DB + session setup
├── index.php              # Root redirect to login/dashboard
├── login.php              # Sign in page
├── register.php           # Sign up page
├── logout.php             # Sign out endpoint
├── dashboard.php          # User stats + charts
├── movies.php             # Browse/search page
├── movie.php              # Movie detail page
├── add_movie.php          # Add a new movie
├── watchlist.php          # User watchlist page
├── leaderboard.php        # Top-rated leaderboard
├── watchlist_add.php      # Watchlist add/update action
├── watchlist_update.php   # Watchlist status update action
├── watchlist_remove.php   # Remove from watchlist action
├── rate.php               # Save user rating
├── review.php             # Post review
├── includes/
│   ├── db.php             # MySQL helper functions
│   └── helpers.php        # auth + flash helpers
├── partials/
│   ├── header.php         # Shared page header/layout
   └── footer.php         # Shared page footer
├── schema.sql            # Database schema
├── seed_data.sql         # Sample movie and genre data
├── .env.example          # Example MySQL env vars
├── templates/            # Original Flask templates
├── routes/               # Original Flask routes
└── app.py                # Original Flask entrypoint
```

---

## PHP App Pages

| Route | Description |
|---|---|
| `/` | Redirects to dashboard or login |
| `/login.php` | Sign in |
| `/register.php` | Create account |
| `/dashboard.php` | Stat cards + charts |
| `/movies.php` | Browse with search + genre filter |
| `/movie.php?id=<id>` | Film detail — rate, review, add to watchlist |
| `/add_movie.php` | Add a new film |
| `/watchlist.php` | My watchlist — filter by status |
| `/leaderboard.php` | Top films by community rating |

---

## Project Structure

```
movie_watchlist/
├── app.py                  # Flask app + DB helper
├── config.py               # DB config + secret key
├── requirements.txt
├── schema.sql              # All tables, trigger, view, procedure
├── seed_data.sql           # Sample movies and genres
├── .env.example
├── routes/
│   ├── auth.py             # /register /login /logout
│   ├── movies.py           # /dashboard /movies /leaderboard /rate /review
│   └── watchlist.py        # /watchlist + add/update/remove
└── templates/
    ├── base.html           # Sidebar + topbar layout
    ├── login.html
    ├── register.html
    ├── dashboard.html      # Stat cards + Chart.js
    ├── movies.html         # Browse with search + genre filter
    ├── movie_detail.html   # Rating + review + watchlist
    ├── watchlist.html      # Tabbed status view
    ├── leaderboard.html    # RANK() window function
    └── add_movie.html
```

---

## DB Concepts Used

| Concept | Where |
|---|---|
| Trigger | `trg_watchlist_to_history` — auto-inserts WatchHistory on status→'watched' |
| View | `user_stats` — total watched, avg rating, fav genre per user |
| Stored Procedure | `get_recommendations(user_id)` — genre-based recs |
| RANK() Window Function | Leaderboard query in `/leaderboard` |
| UNIQUE constraint | One rating and one watchlist entry per user/movie pair |
| JOINs (4+ tables) | Movie detail, dashboard, watchlist queries |
| GROUP BY + AVG | Rating summaries, genre breakdown |
| Subquery | Favourite genre detection in `user_stats` view |

---

## Pages

| Route | Description |
|---|---|
| `/` | Redirects to dashboard or login |
| `/login` | Sign in |
| `/register` | Create account |
| `/dashboard` | Stat cards + charts |
| `/movies` | Browse with search + genre filter |
| `/movies/<id>` | Film detail — rate, review, add to watchlist |
| `/movies/add` | Add a new film |
| `/watchlist` | My watchlist — tabbed by status |
| `/leaderboard` | Top 20 films by avg community rating |
