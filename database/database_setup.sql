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

CREATE TABLE IF NOT EXISTS watch_providers (
    provider_id INT PRIMARY KEY,
    provider_name VARCHAR(255) NOT NULL,
    logo_path VARCHAR(255),
    display_priority INT,
    UNIQUE(provider_id)
);

CREATE TABLE IF NOT EXISTS user_watch_providers (
    user_id INT NOT NULL,
    provider_id INT NOT NULL,
    PRIMARY KEY (user_id, provider_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES watch_providers(provider_id) ON DELETE CASCADE
);
