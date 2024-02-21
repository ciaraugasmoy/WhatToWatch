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

# Passwords to be hashed
PASSWORD1="12345"
PASSWORD2="54321"

# Hash passwords using PHP and bcrypt
HASHED_PASSWORD1=$(php -r "echo password_hash('$PASSWORD1', PASSWORD_BCRYPT);")
HASHED_PASSWORD2=$(php -r "echo password_hash('$PASSWORD2', PASSWORD_BCRYPT);")

# SQL commands to insert users with hashed passwords
INSERT_COMMANDS="INSERT INTO users (username, password) VALUES ('steve', '$HASHED_PASSWORD1');\
INSERT INTO users (username, password) VALUES ('alice', '$HASHED_PASSWORD2');"

# Create SQL script
echo "$INSERT_COMMANDS" > "$SQL_FILE"

# Run MySQL command with what2watchadmin user
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < "$SQL_FILE"

# Remove temporary SQL script file
rm -f "$SQL_FILE"
