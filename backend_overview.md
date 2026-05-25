# Backend Overview

## Application Type
- PHP backend using procedural PHP scripts.
- MySQL database accessed via `mysqli` in `includes/db.php`.
- `config.php` contains database credentials and app configuration.

## Main PHP Routes / Pages

| File | URL | Purpose |
|---|---|---|
| `index.php` | `/` | Redirects users to login or dashboard based on session. |
| `login.php` | `/login.php` | Displays login form and authenticates users. |
| `register.php` | `/register.php` | Displays registration form and creates new user accounts. |
| `logout.php` | `/logout.php` | Logs users out and destroys session data. |
| `dashboard.php` | `/dashboard.php` | Shows user stats, charts, and summary data. |
| `movies.php` | `/movies.php` | Browses movies, searches and filters by genre or title. |
| `movie.php` | `/movie.php?id=<id>` | Displays movie details, ratings, reviews, and watchlist actions. |
| `add_movie.php` | `/add_movie.php` | Adds a new movie to the database. |
| `watchlist.php` | `/watchlist.php` | Shows the current user's watchlist with status filters. |
| `watchlist_add.php` | `/watchlist_add.php` | Adds a movie to the current user's watchlist. |
| `watchlist_update.php` | `/watchlist_update.php` | Updates watchlist status (watched, want_to_watch, dropped). |
| `watchlist_remove.php` | `/watchlist_remove.php` | Removes a movie from the user's watchlist. |
| `rate.php` | `/rate.php` | Saves or updates a movie rating for the user. |
| `review.php` | `/review.php` | Saves a review for a movie by the current user. |
| `test_db.php` | `/test_db.php` | Test page to verify DB connection and list tables. |

## Database Tables

The project uses the `movie_watchlist` database and these tables:

- `Users`
  - `user_id`, `username`, `email`, `password_hash`, `created_at`
- `Movies`
  - `movie_id`, `title`, `release_year`, `duration_mins`, `language`, `poster_url`, `created_at`
- `Genres`
  - `genre_id`, `genre_name`
- `MovieGenres`
  - `movie_id`, `genre_id`
- `Watchlist`
  - `wl_id`, `user_id`, `movie_id`, `status`, `added_at`
- `Ratings`
  - `rating_id`, `user_id`, `movie_id`, `score`, `rated_at`
- `Reviews`
  - `review_id`, `user_id`, `movie_id`, `review_text`, `created_at`
- `WatchHistory`
  - `history_id`, `user_id`, `movie_id`, `watched_on`, `watch_duration_mins`

## Database Logic

- `config.php` defines `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS`.
- `includes/db.php` handles connection, prepared queries, and result fetching.
- All pages include `config.php`, so they share the same session setup and database connection.

## Important SQL Objects

- `schema.sql`
  - Creates database, tables, foreign keys, constraints, and triggers.
- `seed_data.sql`
  - Populates sample genres, movies, movie-genre links, and demo users.
- `trg_watchlist_to_history`
  - Trigger that inserts into `WatchHistory` when watchlist status changes to `watched`.

## How Each Page Works

- `login.php` / `register.php`
  - Read posted credentials, validate input, and update `Users`.
  - Save successful login state into `$_SESSION['user_id']` and `$_SESSION['username']`.

- `dashboard.php`
  - Queries user stats and watch history.
  - Displays totals, averages, and charts for the currently logged-in user.

- `movies.php`
  - Fetches movies from the database based on search or filters.
  - Displays movie cards and links to details.

- `movie.php`
  - Fetches a single movie and its related details.
  - Displays forms to rate, review, and add the movie to watchlist.

- `watchlist*.php` actions
  - `watchlist_add.php` inserts a new watchlist entry.
  - `watchlist_update.php` updates a watchlist entry and may trigger history insertion.
  - `watchlist_remove.php` deletes the watchlist entry.

- `rate.php` and `review.php`
  - Save user-specific rating and review records for the selected movie.

## Notes

- The backend is procedural PHP rather than MVC framework-based.
- `includes/helpers.php` provides session helpers, flash messaging, and redirect functions.
- The database schema includes referential integrity and UNIQUE constraints for duplicate prevention.
