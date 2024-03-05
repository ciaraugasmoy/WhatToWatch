#!/bin/bash

# MySQL credentials
DB_ROOT_USER="root"
DB_ROOT_PASSWORD="your_mysql_root_password"  # Replace with your desired MySQL root password

DB_CLUSTER_USER="cluster_user"
DB_CLUSTER_PASSWORD="cluster_user_password"

DB_APP_USER="app_user"
DB_APP_PASSWORD="app_user_password"
DB_APP_DATABASE="your_application_database"  # Replace with your application database name

# SQL script file
CREATE_USERS_FILE="create_users.sql"

# SQL commands to create users
CREATE_USERS_COMMANDS="
-- Create root user with limited access
CREATE USER '$DB_ROOT_USER'@'localhost' IDENTIFIED BY '$DB_ROOT_PASSWORD';
GRANT RELOAD, SHUTDOWN, PROCESS, SHOW DATABASES, SUPER, REPLICATION SLAVE, REPLICATION CLIENT ON *.* TO '$DB_ROOT_USER'@'localhost' WITH GRANT OPTION;

-- Create cluster management user
CREATE USER '$DB_CLUSTER_USER'@'%' IDENTIFIED BY '$DB_CLUSTER_PASSWORD';
GRANT ALL PRIVILEGES ON *.* TO '$DB_CLUSTER_USER'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;

-- Create regular application user
CREATE USER '$DB_APP_USER'@'%' IDENTIFIED BY '$DB_APP_PASSWORD';
GRANT SELECT, INSERT, UPDATE, DELETE ON $DB_APP_DATABASE.* TO '$DB_APP_USER'@'%';
FLUSH PRIVILEGES;
"

# Create SQL script for creating users
echo "$CREATE_USERS_COMMANDS" > "$CREATE_USERS_FILE"

# Run MySQL commands
mysql -u "$DB_ROOT_USER" -p"$DB_ROOT_PASSWORD" < "$CREATE_USERS_FILE"

# Remove temporary SQL script file
rm -f "$CREATE_USERS_FILE"
