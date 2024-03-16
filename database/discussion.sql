USE what2watch;
DROP TABLE IF EXISTS discussion_posts;
CREATE TABLE IF NOT EXISTS discussion_posts (
	message_id INT AUTO_INCREMENT PRIMARY KEY,
	movie_id INT NOT NULL,
	user_id INT NOT NULL,
	parent_message_id INT,
	message VARCHAR(255) NOT NULL,
	FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE,
	FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
