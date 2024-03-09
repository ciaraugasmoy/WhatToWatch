USE what2watch;

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

