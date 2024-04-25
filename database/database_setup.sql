-- Create the database
CREATE DATABASE IF NOT EXISTS what2watch;

-- Switch to the created database
USE what2watch;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(64) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    dob DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS private_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    private_key VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- watch providers
CREATE TABLE IF NOT EXISTS watch_providers (
    provider_id INT PRIMARY KEY,
    provider_name VARCHAR(255) NOT NULL,
    logo_path VARCHAR(255),
    display_priority INT,
    UNIQUE(provider_id)
);

-- subset of watch_providers
CREATE TABLE IF NOT EXISTS curated_watch_providers (
    provider_id INT PRIMARY KEY,
    provider_name VARCHAR(255) NOT NULL,
    logo_path VARCHAR(255),
    display_priority INT,
    FOREIGN KEY (provider_id) REFERENCES watch_providers(provider_id)
);

CREATE TABLE IF NOT EXISTS user_watch_providers (
    user_id INT NOT NULL,
    provider_id INT NOT NULL,
    PRIMARY KEY (user_id, provider_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES watch_providers(provider_id) ON DELETE CASCADE
);
-- friends
CREATE TABLE IF NOT EXISTS friends (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id),
    UNIQUE KEY unique_friendship (sender_id, receiver_id)
);
-- movies
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

CREATE TABLE IF NOT EXISTS movie_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL,
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_movie (movie_id, user_id), -- Unique constraint for user and movie combination
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS threads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FULLTEXT(title, body), -- for better search queries
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id)
);


CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    thread_id INT NOT NULL,
    body TEXT NOT NULL,
    posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (thread_id) REFERENCES threads(id)
);

CREATE TABLE IF NOT EXISTS watchlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_movie (user_id, movie_id), -- Ensure each movie is added to a user's watchlist only once
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS thread_votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    thread_id INT NOT NULL,
    user_id INT NOT NULL,
    vote_type ENUM('upvote', 'downvote') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    thread_id INT NOT NULL,
    UNIQUE KEY unique_user_thread (user_id, thread_id), -- Ensure each user can subscribe to a thread only once
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE CASCADE
);
-- Add controversial_score column to the threads table
ALTER TABLE threads
ADD COLUMN controversial_score FLOAT DEFAULT 0.0;
-- Create a trigger that updates the controversial_score of the specific thread
-- when votes are inserted, updated, or deleted in the thread_votes table

DELIMITER $$

CREATE TRIGGER update_specific_thread_controversial_score
AFTER INSERT ON thread_votes
FOR EACH ROW
BEGIN
    DECLARE upvotes INT;
    DECLARE downvotes INT;
    DECLARE total_votes INT;
    DECLARE thread_id INT;

    -- Get the thread_id of the affected thread
    SET thread_id = NEW.thread_id;

    -- Calculate the total upvotes and downvotes for the specific thread
    SELECT
        COALESCE(SUM(CASE WHEN vote_type = 'upvote' THEN 1 ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN vote_type = 'downvote' THEN 1 ELSE 0 END), 0),
        COUNT(*) AS total_votes
    INTO
        upvotes,
        downvotes,
        total_votes
    FROM
        thread_votes
    WHERE
        thread_id = thread_id; -- Use the thread_id from NEW

    -- Calculate controversial score using Wilson score interval
    SET @n = total_votes;
    SET @z = 1.96; -- Z-score for 95% confidence (standard for Wilson score interval)

    SET @p = upvotes / total_votes;
    SET @controversial_score = (@p + @z * @z / (2 * @n) - @z * SQRT((@p * (1 - @p) + @z * @z / (4 * @n)) / @n)) / (1 + @z * @z / @n);

    -- Update the controversial_score of the specific thread
    UPDATE threads
    SET controversial_score = @controversial_score
    WHERE id = thread_id; -- Update the thread with the specific thread_id
END$$

DELIMITER ;