-- Create the database
CREATE DATABASE IF NOT EXISTS what2watch;

-- Switch to the created database
USE what2watch;

-- Drop tables if they exist so we can overwrite future changes completely
DROP TABLE IF EXISTS private_keys;
DROP TABLE IF EXISTS users;

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

-- user with grant limited permissions
CREATE USER IF NOT EXISTS 'what2watchadmin'@'localhost' IDENTIFIED BY 'what2watchpassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON what2watch.* TO 'what2watchadmin'@'localhost';
FLUSH PRIVILEGES;
