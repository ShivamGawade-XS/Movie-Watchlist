-- ============================================================
-- Sample Movie Watchlist Data
-- ============================================================

USE movie_watchlist;

-- Insert sample genres
INSERT INTO Genres (genre_name) VALUES
('Action'),
('Drama'),
('Comedy'),
('Thriller'),
('Sci-Fi'),
('Horror'),
('Romance'),
('Animation');

-- Insert sample movies
INSERT INTO Movies (title, release_year, duration_mins, language, poster_url) VALUES
('Inception', 2010, 148, 'English', 'https://via.placeholder.com/300x450?text=Inception'),
('The Matrix', 1999, 136, 'English', 'https://via.placeholder.com/300x450?text=The+Matrix'),
('Interstellar', 2014, 169, 'English', 'https://via.placeholder.com/300x450?text=Interstellar'),
('The Shawshank Redemption', 1994, 142, 'English', 'https://via.placeholder.com/300x450?text=Shawshank'),
('Pulp Fiction', 1994, 154, 'English', 'https://via.placeholder.com/300x450?text=Pulp+Fiction'),
('Forrest Gump', 1994, 142, 'English', 'https://via.placeholder.com/300x450?text=Forrest+Gump'),
('The Dark Knight', 2008, 152, 'English', 'https://via.placeholder.com/300x450?text=Dark+Knight'),
('Titanic', 1997, 194, 'English', 'https://via.placeholder.com/300x450?text=Titanic'),
('Avatar', 2009, 162, 'English', 'https://via.placeholder.com/300x450?text=Avatar'),
('Gladiator', 2000, 155, 'English', 'https://via.placeholder.com/300x450?text=Gladiator');

-- Link movies to genres (MovieGenres)
INSERT INTO MovieGenres (movie_id, genre_id) VALUES
(1, 1), (1, 5), -- Inception: Action, Sci-Fi
(2, 1), (2, 5), -- The Matrix: Action, Sci-Fi
(3, 5),         -- Interstellar: Sci-Fi
(4, 2),         -- Shawshank: Drama
(5, 2), (5, 4), -- Pulp Fiction: Drama, Thriller
(6, 2), (6, 3), -- Forrest Gump: Drama, Comedy
(7, 1), (7, 4), -- Dark Knight: Action, Thriller
(8, 7),         -- Titanic: Romance
(9, 1), (9, 5), -- Avatar: Action, Sci-Fi
(10, 1), (10, 2); -- Gladiator: Action, Drama
