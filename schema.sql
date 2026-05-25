-- ============================================================
-- Movie Watchlist & Rating Tracker — Schema
-- MySQL 8.0+
-- ============================================================

CREATE DATABASE IF NOT EXISTS movie_watchlist;
USE movie_watchlist;

-- ------------------------------------------------------------
-- TABLES
-- ------------------------------------------------------------

CREATE TABLE Users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    email       VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(256) NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Movies (
    movie_id        INT AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(200) NOT NULL,
    release_year    YEAR,
    duration_mins   INT,
    language        VARCHAR(50),
    poster_url      VARCHAR(500),
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Genres (
    genre_id    INT AUTO_INCREMENT PRIMARY KEY,
    genre_name  VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE MovieGenres (
    movie_id    INT NOT NULL,
    genre_id    INT NOT NULL,
    PRIMARY KEY (movie_id, genre_id),
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES Genres(genre_id) ON DELETE CASCADE
);

CREATE TABLE Watchlist (
    wl_id       INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    movie_id    INT NOT NULL,
    status      ENUM('watched','want_to_watch','dropped') NOT NULL DEFAULT 'want_to_watch',
    added_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_movie (user_id, movie_id),
    FOREIGN KEY (user_id)  REFERENCES Users(user_id)  ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id) ON DELETE CASCADE
);

CREATE TABLE Ratings (
    rating_id   INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    movie_id    INT NOT NULL,
    score       TINYINT NOT NULL CHECK (score BETWEEN 1 AND 10),
    rated_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_rating (user_id, movie_id),
    FOREIGN KEY (user_id)  REFERENCES Users(user_id)  ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id) ON DELETE CASCADE
);

CREATE TABLE Reviews (
    review_id   INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    movie_id    INT NOT NULL,
    review_text TEXT NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)  REFERENCES Users(user_id)  ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id) ON DELETE CASCADE
);

CREATE TABLE WatchHistory (
    history_id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id             INT NOT NULL,
    movie_id            INT NOT NULL,
    watched_on          DATETIME DEFAULT CURRENT_TIMESTAMP,
    watch_duration_mins INT,
    FOREIGN KEY (user_id)  REFERENCES Users(user_id)  ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- TRIGGER: auto-add to WatchHistory when status set to 'watched'
-- ------------------------------------------------------------

DELIMITER $$

CREATE TRIGGER trg_watchlist_to_history
AFTER UPDATE ON Watchlist
FOR EACH ROW
BEGIN
    IF NEW.status = 'watched' AND OLD.status != 'watched' THEN
        INSERT INTO WatchHistory (user_id, movie_id, watch_duration_mins)
        SELECT NEW.user_id, NEW.movie_id, m.duration_mins
        FROM Movies m WHERE m.movie_id = NEW.movie_id;
    END IF;
END$$

DELIMITER ;

-- ------------------------------------------------------------
-- VIEW: user_stats — total watched, avg rating, fav genre
-- ------------------------------------------------------------

CREATE VIEW user_stats AS
SELECT
    u.user_id,
    u.username,
    COUNT(DISTINCT wh.history_id)                    AS total_watched,
    ROUND(AVG(r.score), 1)                           AS avg_rating,
    COALESCE(SUM(m.duration_mins) / 60, 0)           AS total_hours,
    (
        SELECT g.genre_name
        FROM WatchHistory wh2
        JOIN MovieGenres mg ON mg.movie_id = wh2.movie_id
        JOIN Genres g       ON g.genre_id  = mg.genre_id
        WHERE wh2.user_id = u.user_id
        GROUP BY g.genre_id
        ORDER BY COUNT(*) DESC
        LIMIT 1
    ) AS fav_genre
FROM Users u
LEFT JOIN WatchHistory wh ON wh.user_id = u.user_id
LEFT JOIN Movies m        ON m.movie_id  = wh.movie_id
LEFT JOIN Ratings r       ON r.user_id   = u.user_id
GROUP BY u.user_id, u.username;

-- ------------------------------------------------------------
-- STORED PROCEDURE: get_recommendations(user_id)
-- Returns movies in the user's top genres that they haven't watched
-- ------------------------------------------------------------

DELIMITER $$

CREATE PROCEDURE get_recommendations(IN p_user_id INT)
BEGIN
    -- find top genre of the user
    DECLARE top_genre INT;

    SELECT mg.genre_id INTO top_genre
    FROM WatchHistory wh
    JOIN MovieGenres mg ON mg.movie_id = wh.movie_id
    WHERE wh.user_id = p_user_id
    GROUP BY mg.genre_id
    ORDER BY COUNT(*) DESC
    LIMIT 1;

    -- return movies of that genre not yet in their watchlist
    SELECT DISTINCT m.movie_id, m.title, m.release_year, m.poster_url,
           ROUND(AVG(r.score), 1) AS avg_score
    FROM Movies m
    JOIN MovieGenres mg ON mg.movie_id = m.movie_id
    LEFT JOIN Ratings r ON r.movie_id  = m.movie_id
    WHERE mg.genre_id = top_genre
      AND m.movie_id NOT IN (
            SELECT movie_id FROM Watchlist WHERE user_id = p_user_id
      )
    GROUP BY m.movie_id
    ORDER BY avg_score DESC
    LIMIT 10;
END$$

DELIMITER ;
