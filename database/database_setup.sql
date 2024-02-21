-- Create the database
CREATE DATABASE IF NOT EXISTS what2watch;

-- Switch to the created database
USE what2watch;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS private_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    private_key VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create a user and grant limited permissions
CREATE USER IF NOT EXISTS 'what2watchadmin'@'localhost' IDENTIFIED BY 'what2watchpassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON what2watch.* TO 'what2watchadmin'@'localhost';
-- Add additional GRANT statements for other tables if needed
FLUSH PRIVILEGES;
