#!/bin/bash

# VM credentials - REPLACE
SOURCE_IP="127.0.0.1"
LOG_FILE="mysql-bin.12345"
LOG_POS=1234

# MySQL credentials
MYSQL_USER="what2watchadmin"
MYSQL_PASSWORD="what2watchpassword"
DB_NAME="what2watch"

# Precautionary, should be done already
apt-get update
apt install openssh-server
ufw allow from $SOURCE_IP to any port 3306
ufw allow from $SOURCE_IP to any port 22

mysql -u root -p -e "DROP DATABASE IF EXISTS $DB_NAME;"
mysql -u root -p -e "CREATE DATABASE $DB_NAME;"
mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -e "exit;"
mysql -u $MYSQL_USER -p$MYSQL_PASSWORD $DB_NAME < /tmp/$DB_NAME.sql

# Updating replica MySQL conf file
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
bind-address		= 127.0.0.1
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
server-id	= 2
log_bin		= /var/log/mysql/mysql-bin.log
# binlog_expire_logs_seconds	= 2592000
max_binlog_size   = 100M
binlog_do_db	= what2watch
# binlog_ignore_db	= include_database_name
relay-log   =   /var/log/mysql/mysql-relay-bin.log
" > /etc/mysql/mysql.conf.d/mysqld.cnf
rm /var/lib/mysql/auto.cnf
systemctl restart mysql

# Configuring replication
mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -e "CHANGE REPLICATION SOURCE TO 
SOURCE_HOST='$SOURCE_IP',
SOURCE_USER='replica_user',
SOURCE_PASSWORD='replica_password',
SOURCE_LOG_FILE='$LOG_FILE',
SOURCE_LOG_POS=$LOG_POS;"
mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -e "START REPLICA;"