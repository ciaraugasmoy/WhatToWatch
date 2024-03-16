#!/bin/bash

# MySQL credentials
DB_USER="what2watchadmin"
DB_PASSWORD="what2watchpassword"
DB_NAME="what2watch"

# SQL script file
SQL_FILE="insert_users.sql"

# Users to be deleted
USERS_TO_DELETE=("steve" "alice")

# Delete users if they exist
for USER in "${USERS_TO_DELETE[@]}"; do
    mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -e "DELETE FROM users WHERE username='$USER';"
done

# SQL commands to insert users with hashed passwords, email, and dob
INSERT_COMMANDS=""

# Generate 30 users with generic names and emails
for ((i=1; i<=30; i++)); do
    USERNAME="user$i"
    EMAIL="user$i@example.com"
    HASHED_PASSWORD=$(php -r "echo password_hash('12345', PASSWORD_BCRYPT);")
    INSERT_COMMANDS+="INSERT INTO users (username, password, email, dob) VALUES ('$USERNAME', '$HASHED_PASSWORD', '$EMAIL', '1990-01-01');"
done

# Create SQL script
echo "$INSERT_COMMANDS" > "$SQL_FILE"

# Run MySQL command with what2watchadmin user
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < "$SQL_FILE"

# Remove temporary SQL script file
rm -f "$SQL_FILE"