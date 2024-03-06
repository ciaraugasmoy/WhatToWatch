USE what2watch;
DROP TABLE IF EXISTS watch_providers;
CREATE TABLE watch_providers (
    provider_id INT PRIMARY KEY,
    provider_name VARCHAR(255) NOT NULL,
    logo_path VARCHAR(255),
    display_priority INT,
    UNIQUE(provider_id)
);
