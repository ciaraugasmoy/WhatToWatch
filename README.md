# WhatToWatch

sudo apt install mysql-server


#CREATE DATABASES AND SET UP REMOTE USER
sudo mysql -u root

create database DATABASE_NAME;

ADD TABLES, DATA ETC,

ADD USER!!
(IN MYSQL ROOT)
create user 'USERNAME'@'IP ADDRESS OF RABBITMQ MACHINE' identified by 'PASSWORD';

GRANT ALL PRIVILEGES ON *.* TO 'USERNAME'@'IP ADDRESS OF RABBITMQ MACHINE' WITH GRANT OPTION;

FLUSH PRIVILEGES;

exit;


THEN!
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf

and replace 127.0.0.1's with the ip of the MYSQL MACHINE TO the following fields: bind-address and mysqlx-bind-address.

then restart:  sudo systemctl restart mysql
