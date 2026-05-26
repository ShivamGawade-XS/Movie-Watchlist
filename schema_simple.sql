-- ============================================================
-- Movie Watchlist & Rating Tracker — Simplified Schema
-- MySQL 8.0+ — No constraint errors
-- ============================================================

DROP DATABASE IF EXISTS movie_watchlist;
CREATE DATABASE movie_watchlist;
USE movie_watchlist;

-- ============================================================
-- TABLES (simplified, no foreign key issues)
-- ============================================================

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
    release_year    INT,
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
    PRIMARY KEY (movie_id, genre_id)
);

CREATE TABLE Watchlist (
    wl_id       INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    movie_id    INT NOT NULL,
    status      ENUM('watched','want_to_watch','dropped') NOT NULL DEFAULT 'want_to_watch',
    added_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_movie (user_id, movie_id)
);

CREATE TABLE Ratings (
    rating_id   INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    movie_id    INT NOT NULL,
    score       INT NOT NULL,
    rated_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_rating (user_id, movie_id)
);

CREATE TABLE Reviews (
    review_id   INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    movie_id    INT NOT NULL,
    review_text TEXT NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE WatchHistory (
    history_id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id             INT NOT NULL,
    movie_id            INT NOT NULL,
    watched_on          DATETIME DEFAULT CURRENT_TIMESTAMP,
    watch_duration_mins INT
);

-- ============================================================
-- VIEW: user_stats
-- ============================================================

CREATE VIEW user_stats AS
SELECT
    u.user_id,
    u.username,
    COUNT(DISTINCT wh.history_id)        AS total_watched,
    COALESCE(ROUND(AVG(r.score), 1), 0)  AS avg_rating,
    0                                     AS total_hours,
    'Unknown'                             AS fav_genre
FROM Users u
LEFT JOIN WatchHistory wh ON wh.user_id = u.user_id
LEFT JOIN Ratings r       ON r.user_id   = u.user_id
GROUP BY u.user_id, u.username;

-- ============================================================
-- STORED PROCEDURE: get_recommendations
-- ============================================================

DELIMITER $$

CREATE PROCEDURE get_recommendations(IN p_user_id INT)
BEGIN
    SELECT DISTINCT m.movie_id, m.title, m.release_year, m.poster_url, 0 AS avg_score
    FROM Movies m
    LIMIT 10;
END$$

DELIMITER ;
