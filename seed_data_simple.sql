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

-- Insert sample movies with real poster images
INSERT INTO Movies (title, release_year, duration_mins, language, poster_url) VALUES
('Inception', 2010, 148, 'English', 'https://image.tmdb.org/t/p/w342/9gk7adHYeDMPS6QW4UJcstCA26_.jpg'),
('The Matrix', 1999, 136, 'English', 'https://image.tmdb.org/t/p/w342/f89U3ADr1oMo21adPzmCgYi3V4t.jpg'),
('Interstellar', 2014, 169, 'English', 'https://image.tmdb.org/t/p/w342/rAiY959THWwlBmYOeAete6YXP4O.jpg'),
('The Shawshank Redemption', 1994, 142, 'English', 'https://image.tmdb.org/t/p/w342/lyQBXzA31QHaYkXWB6hnIX7K63s.jpg'),
('Pulp Fiction', 1994, 154, 'English', 'https://image.tmdb.org/t/p/w342/dM2w4PZqPHG1efcccqAY9M89aZZ.jpg'),
('Forrest Gump', 1994, 142, 'English', 'https://image.tmdb.org/t/p/w342/clnyhPqj1SNgpAdeSS6CmAERo4G.jpg'),
('The Dark Knight', 2008, 152, 'English', 'https://image.tmdb.org/t/p/w342/1hqwGsEchVmkD98THg2i0E90kK9.jpg'),
('Titanic', 1997, 194, 'English', 'https://image.tmdb.org/t/p/w342/9xjZS2ow8KwQwgeRv8JCkLMSXaG.jpg'),
('Avatar', 2009, 162, 'English', 'https://image.tmdb.org/t/p/w342/jRXYZsGMSLsllc32SCGT7ARAH4d.jpg'),
('Gladiator', 2000, 155, 'English', 'https://image.tmdb.org/t/p/w342/owS8yM8r66aMwj/BiWMmonsLlz.jpg'),
('The Godfather', 1972, 175, 'English', 'https://image.tmdb.org/t/p/w342/3bhkrj58Vtu7enYsU5A1jVWUXwC.jpg'),
('The Lord of the Rings: The Fellowship of the Ring', 2001, 178, 'English', 'https://image.tmdb.org/t/p/w342/6oom5QYQ2IQrmJL225aA4GEYricext.jpg'),
('Avengers: Endgame', 2019, 181, 'English', 'https://image.tmdb.org/t/p/w342/or06FQrPadWVM7DmHeOn88mtjDP.jpg'),
('Oppenheimer', 2023, 180, 'English', 'https://image.tmdb.org/t/p/w342/8Gxv8gSFCU0XGDykEGCPoIuVDAu.jpg'),
('Dune', 2021, 156, 'English', 'https://image.tmdb.org/t/p/w342/n6bUvigpRFqLEDg1PyIWmRHBZpo.jpg'),
('Parasite', 2019, 132, 'Korean', 'https://image.tmdb.org/t/p/w342/7IizwVrtBWq5cVl7QwDmRedUVqQ.jpg'),
('Once Upon a Time in Hollywood', 2019, 161, 'English', 'https://image.tmdb.org/t/p/w342/ELy8eGXEd8e1rat7kUCWToKi89A.jpg'),
('Joker', 2019, 122, 'English', 'https://image.tmdb.org/t/p/w342/udDclMPFLuhnbPpF8p2JpjrHjj5.jpg'),
('The Shining', 1980, 146, 'English', 'https://image.tmdb.org/t/p/w342/4zNcs5vkaf2jIvtisкъдето/2B8Oowsc0JH2AJINvJBVjp.jpg'),
('Fight Club', 1999, 139, 'English', 'https://image.tmdb.org/t/p/w342/pB8BM7pdSp6B6Ih7QZ4DrQ3PmJK.jpg');

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
(10, 1), (10, 2), -- Gladiator: Action, Drama
(11, 2),        -- The Godfather: Drama
(12, 1), (12, 2), -- LOTR: Action, Drama
(13, 1), (13, 5), -- Avengers: Action, Sci-Fi
(14, 2),        -- Oppenheimer: Drama
(15, 5),        -- Dune: Sci-Fi
(16, 2), (16, 4), -- Parasite: Drama, Thriller
(17, 2), (17, 7), -- Once Upon a Time: Drama, Romance
(18, 4), (18, 2), -- Joker: Thriller, Drama
(19, 6),        -- The Shining: Horror
(20, 4), (20, 2); -- Fight Club: Thriller, Drama
