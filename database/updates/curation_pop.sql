USE what2watch;
-- USE THIS TO ADD SPECIALLY SELECTED WP
INSERT INTO curated_watch_providers (provider_id, provider_name, logo_path, display_priority)
SELECT 
    provider_id,
    provider_name,
    logo_path,
    display_priority
FROM watch_providers
WHERE provider_name IN ('Netflix','Hulu','Apple TV','YouTube Premium','Disney Plus')
ON DUPLICATE KEY UPDATE
    provider_name = VALUES(provider_name),
    logo_path = VALUES(logo_path),
    display_priority = VALUES(display_priority);
