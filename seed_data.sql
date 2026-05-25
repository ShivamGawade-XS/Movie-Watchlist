USE movie_watchlist;

-- Genres
INSERT INTO Genres (genre_name) VALUES
('Action'), ('Drama'), ('Comedy'), ('Thriller'), ('Sci-Fi'),
('Horror'), ('Romance'), ('Animation'), ('Crime'), ('Documentary');

-- Movies
INSERT INTO Movies (title, release_year, duration_mins, language, poster_url) VALUES
('Inception',           2010, 148, 'English', 'https://via.placeholder.com/200x300/1a1a2e/f59e0b?text=Inception'),
('The Dark Knight',     2008, 152, 'English', 'https://via.placeholder.com/200x300/16213e/f59e0b?text=Dark+Knight'),
('Interstellar',        2014, 169, 'English', 'https://via.placeholder.com/200x300/0f3460/f59e0b?text=Interstellar'),
('Parasite',            2019, 132, 'Korean',  'https://via.placeholder.com/200x300/1a1a2e/e11d48?text=Parasite'),
('The Shawshank Redemption', 1994, 142, 'English', 'https://via.placeholder.com/200x300/16213e/10b981?text=Shawshank'),
('Pulp Fiction',        1994, 154, 'English', 'https://via.placeholder.com/200x300/0f3460/f59e0b?text=Pulp+Fiction'),
('The Matrix',          1999, 136, 'English', 'https://via.placeholder.com/200x300/1a1a2e/10b981?text=The+Matrix'),
('Spirited Away',       2001, 125, 'Japanese','https://via.placeholder.com/200x300/16213e/f59e0b?text=Spirited+Away'),
('The Godfather',       1972, 175, 'English', 'https://via.placeholder.com/200x300/0f3460/e11d48?text=Godfather'),
('Schindler\'s List',   1993, 195, 'English', 'https://via.placeholder.com/200x300/1a1a2e/f59e0b?text=Schindler'),
('Get Out',             2017, 104, 'English', 'https://via.placeholder.com/200x300/16213e/e11d48?text=Get+Out'),
('La La Land',          2016, 128, 'English', 'https://via.placeholder.com/200x300/0f3460/f59e0b?text=La+La+Land');

-- MovieGenres
INSERT INTO MovieGenres (movie_id, genre_id) VALUES
(1,5),(1,1),(1,4),   -- Inception: Sci-Fi, Action, Thriller
(2,1),(2,4),(2,9),   -- Dark Knight: Action, Thriller, Crime
(3,5),(3,2),(3,1),   -- Interstellar: Sci-Fi, Drama, Action
(4,4),(4,2),(4,9),   -- Parasite: Thriller, Drama, Crime
(5,2),(5,9),         -- Shawshank: Drama, Crime
(6,4),(6,9),(6,2),   -- Pulp Fiction: Thriller, Crime, Drama
(7,5),(7,1),(7,4),   -- Matrix: Sci-Fi, Action, Thriller
(8,8),(8,2),         -- Spirited Away: Animation, Drama
(9,2),(9,9),         -- Godfather: Drama, Crime
(10,2),(10,9),       -- Schindler: Drama, Crime
(11,6),(11,4),       -- Get Out: Horror, Thriller
(12,2),(12,6);       -- La La Land: Drama, Romance (genre_id 7)

-- Sample users (passwords are hashed 'password123' via werkzeug)
INSERT INTO Users (username, email, password_hash) VALUES
('cinephile',  'cinephile@demo.com',  'pbkdf2:sha256:260000$abc$demo_hash_1'),
('filmfan',    'filmfan@demo.com',    'pbkdf2:sha256:260000$def$demo_hash_2');

-- Note: Use /register to create real accounts with proper password hashing.
