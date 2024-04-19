#!/bin/bash

# VM credentials - REPLACE
REPLICA_VM_NAME="The user you have on your vm"
SOURCE_IP="127.0.0.1"
REPLICA_IP="127.0.0.1"

# MySQL credentials
MYSQL_USER="what2watchadmin"
MYSQL_PASSWORD="what2watchpassword"
DB_NAME="what2watch"
TABLE_NAME="replication"

# Precautionary, should be done already
apt-get update
apt install openssh-server
ufw allow from $REPLICA_IP to any port 3306
ufw allow from $REPLICA_IP to any port 22

# Updating source MySQL conf file
echo "
#
# The MySQL database server configuration file.
#
# One can use all long options that the program supports.
# Run program with --help to get a list of available options and with
# --print-defaults to see which it would actually understand and use.
#
# For explanations see
# http://dev.mysql.com/doc/mysql/en/server-system-variables.html

# Here is entries for some specific programs
# The following values assume you have at least 32M ram

[mysqld]
#
# * Basic Settings
#
user		= mysql
# pid-file	= /var/run/mysqld/mysqld.pid
# socket	= /var/run/mysqld/mysqld.sock
# port		= 3306
# datadir	= /var/lib/mysql


# If MySQL is running as a replication slave, this should be
# changed. Ref https://dev.mysql.com/doc/refman/8.0/en/server-system-variables.html#sysvar_tmpdir
# tmpdir		= /tmp
#
# Instead of skip-networking the default is now to listen only on
# localhost which is more compatible and is not less secure.
bind-address		= $SOURCE_IP
mysqlx-bind-address	= 127.0.0.1
#
# * Fine Tuning
#
key_buffer_size		= 16M
max_allowed_packet	= 64M
# thread_stack		= 256K

# thread_cache_size       = -1

# This replaces the startup script and checks MyISAM tables if needed
# the first time they are touched
myisam-recover-options  = BACKUP

# max_connections        = 151

# table_open_cache       = 4000

#
# * Logging and Replication
#
# Both location gets rotated by the cronjob.
#
# Log all queries
# Be aware that this log type is a performance killer.
# general_log_file        = /var/log/mysql/query.log
# general_log             = 1
#
# Error log - should be very few entries.
#
log_error = /var/log/mysql/error.log
#
# Here you can see queries with especially long duration
# slow_query_log		= 1
# slow_query_log_file	= /var/log/mysql/mysql-slow.log
# long_query_time = 2
# log-queries-not-using-indexes
#
# The following can be used as easy to replay backup logs or for replication.
# note: if you are setting up a replication slave, see README.Debian about
#       other settings you may need to change.
server-id	= 1
log_bin		= /var/log/mysql/mysql-bin.log
# binlog_expire_logs_seconds	= 2592000
max_binlog_size   = 100M
binlog_do_db	= what2watch
# binlog_ignore_db	= include_database_name
" > /etc/mysql/mysql.conf.d/mysqld.cnf
systemctl restart mysql

# Setting up replication user
mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -e "CREATE USER 'replica_user'@'$REPLICA_IP' IDENTIFIED WITH mysql_native_password BY 'replica_password';"
mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -e "GRANT SLAVE ON *.* TO 'replica_user'@'$REPLICA_IP';"
mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -e "FLUSH PRIVILEGES;"

# Creating table to later be tested
mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -e "USE what2watch;"
mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -e "CREATE TABLE replication_testing (
    test_col varchar(64)
);"
mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -e "INSERT INTO replication_testing VALUES 
    ('row 1'),
    ('row 2');"

# Exporting of what2watch db
mysqldump -u $MYSQL_USER -p$MYSQL_PASSWORD what2watch > what2watch.sql
scp what2watch.sql $REPLICA_VM_NAME@$REPLICA_IP:/tmp/