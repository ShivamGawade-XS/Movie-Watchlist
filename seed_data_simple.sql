-- ============================================================
-- MOVIE WATCHLIST - CLEAN SEED DATA
-- ============================================================

USE movie_watchlist;

-- ---------- Genres ----------
INSERT INTO Genres (genre_name) VALUES
('Action'),
('Drama'),
('Comedy'),
('Thriller'),
('Sci-Fi'),
('Horror'),
('Romance'),
('Animation');

-- ---------- Movies ----------
INSERT INTO Movies
(movie_id, title, release_year, duration_mins, language, poster_url)
VALUES

(1,'Inception',2010,148,'English',
'https://image.tmdb.org/t/p/w500/8IB2e4r4oVhHnANbnm7O3Tj6tF8.jpg'),

(2,'The Matrix',1999,136,'English',
'https://image.tmdb.org/t/p/w500/f89U3ADr1oiB1s9GkdPOEpXUk5H.jpg'),

(3,'Interstellar',2014,169,'English',
'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg'),

(4,'The Shawshank Redemption',1994,142,'English',
'https://image.tmdb.org/t/p/w500/q6y0Go1tsGEsmtFryDOJo3dEmqu.jpg'),

(5,'Pulp Fiction',1994,154,'English',
'https://image.tmdb.org/t/p/w500/d5iIlFn5s0ImszYzBPb8JPIfbXD.jpg'),

(6,'Forrest Gump',1994,142,'English',
'https://image.tmdb.org/t/p/w500/arw2vcBveWOVZr6pxd9XTd1TdQa.jpg'),

(7,'The Dark Knight',2008,152,'English',
'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg'),

(8,'Titanic',1997,194,'English',
'https://image.tmdb.org/t/p/w500/9xjZS2rlVxm8SFx8kPC3aIGCOYQ.jpg'),

(9,'Avatar',2009,162,'English',
'https://image.tmdb.org/t/p/w500/kyeqWdyUXW608qlYkRqosgbbJyK.jpg'),

(10,'Gladiator',2000,155,'English',
'https://image.tmdb.org/t/p/w500/ty8TGRuvJLPUmAR1H1nRIsgwvim.jpg'),

(11,'The Godfather',1972,175,'English',
'https://image.tmdb.org/t/p/w500/3bhkrj58Vtu7enYsRolD1fZdja1.jpg'),

(12,'The Lord of the Rings: The Fellowship of the Ring',2001,178,'English',
'https://image.tmdb.org/t/p/w500/6oom5QYQ2yQTMJIbnvbkBL9cHo6.jpg'),

(13,'Avengers: Endgame',2019,181,'English',
'https://image.tmdb.org/t/p/w500/or06FN3Dka5tukK1e9sl16pB3iy.jpg'),

(14,'Oppenheimer',2023,180,'English',
'https://image.tmdb.org/t/p/w500/ptpr0kGAckfQkJeJIt8st5dglvd.jpg'),

(15,'Dune',2021,156,'English',
'https://image.tmdb.org/t/p/w500/d5NXSklXo0qyIYkgV94XAgMIckC.jpg'),

(16,'Parasite',2019,132,'Korean',
'https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg'),

(17,'Once Upon a Time in Hollywood',2019,161,'English',
'https://image.tmdb.org/t/p/w500/8j58iEBw9pOXFD2L0nt0ZXeHviB.jpg'),

(18,'Joker',2019,122,'English',
'https://image.tmdb.org/t/p/w500/udDclJoHjfjb8Ekgsd4FDteOkCU.jpg'),

(19,'The Shining',1980,146,'English',
'https://image.tmdb.org/t/p/w500/xazWoLealQwEgqZ89MLZklLZD3k.jpg'),

(20,'Fight Club',1999,139,'English',
'https://image.tmdb.org/t/p/w500/bptfVGEQuv6vDTIMVCHjJ9Dz8PX.jpg');

-- ---------- MovieGenres ----------
INSERT INTO MovieGenres (movie_id, genre_id) VALUES
(1,1),(1,5),
(2,1),(2,5),
(3,5),
(4,2),
(5,2),(5,4),
(6,2),(6,3),
(7,1),(7,4),
(8,7),
(9,1),(9,5),
(10,1),(10,2),
(11,2),
(12,1),(12,2),
(13,1),(13,5),
(14,2),
(15,5),
(16,2),(16,4),
(17,2),(17,7),
(18,4),(18,2),
(19,6),
(20,4),(20,2);