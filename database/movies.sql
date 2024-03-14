USE what2watch;
DROP TABLE movies;
CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT UNIQUE,
    title VARCHAR(255) NOT NULL,
    overview TEXT,
    release_date DATE,
    poster_path VARCHAR(255),
    backdrop_path VARCHAR(255),
    adult BOOLEAN
);
