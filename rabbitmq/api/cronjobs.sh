#!/bin/bash

# Define the path to the PHP script
SCRIPT_PATH="watchProviderHandler.php"

# Define the cron job command
CRON_COMMAND="0 0 1 * * php $SCRIPT_PATH"

# Write the cron job command to the crontab
echo "$CRON_COMMAND" | crontab -
