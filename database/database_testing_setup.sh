#!/bin/bash

# MySQL credentials
DB_USER="what2watchadmin"
DB_PASSWORD="what2watchpassword"
DB_NAME="what2watch"

# SQL script file
SQL_FILE="insert_users.sql"

# SQL commands to insert users
INSERT_COMMANDS="INSERT INTO users (username, password) VALUES ('steve', '12345');\
INSERT INTO users (username, password) VALUES ('alice', '54321');"

# Create SQL script
echo "$INSERT_COMMANDS" > "$SQL_FILE"

# Run MySQL command with what2watchadmin user
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < "$SQL_FILE"

# Remove temporary SQL script file
rm -f "$SQL_FILE"
