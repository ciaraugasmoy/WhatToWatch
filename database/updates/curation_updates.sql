USE what2watch;

-- updateds all info for selected curation ids
UPDATE curated_watch_providers AS curated
JOIN watch_providers AS original
ON curated.provider_id = original.provider_id
SET 
    curated.provider_name = original.provider_name,
    curated.logo_path = original.logo_path,
    curated.display_priority = original.display_priority;
