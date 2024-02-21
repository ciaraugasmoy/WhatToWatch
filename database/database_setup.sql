-- Create the database
CREATE DATABASE IF NOT EXISTS what2watch;

-- Switch to the created database
USE what2watch;

-- Create the 'users' table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
-- table for public keys to validate users
CREATE TABLE IF NOT EXISTS public_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    public_key TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create a user and grant limited permissions
CREATE USER IF NOT EXISTS 'what2watchadmin'@'localhost' IDENTIFIED BY 'what2watchpassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON what2watch.users TO 'what2watchadmin'@'localhost';
-- Add additional GRANT statements for other tables if needed
FLUSH PRIVILEGES;
