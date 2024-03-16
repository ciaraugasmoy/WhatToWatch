# Setup

binding addresses try
```
echo -e "[mysqld]\nbind-address = 0.0.0.0" | sudo tee -a /etc/mysql/my.cnf
```
or 
```
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```
And replace 127.0.0.1's with the your ip TO the following fields: bind-address and mysqlx-bind-address.

-------------------------------------------------------------------------------------------------------

To set up Database, currently two users are hardcoded in there
Please open database_setup.sql and change the user created to use the IP of your RABBITMQ machine. do not change anything else.
```
sudo apt install mysql-server
sudo mysql < database_setup.sql
sudo mysql < user_setup.sql
```
To populate with test data please run the test_data_scripts
# Firewalls
Please use your RABBITMQ SERVER ip
```
sudo ufw reset -y
sudo ufw default deny incoming
sudo ufw allow from BROKER_IP to any port 3306
sudo ufw enable
```
**OTHER**
command to make .sh file executable incase it isnt
```
chmod +x database_testing_setup.sh
```
The file `create_db_users.sh` just ignore it pls, dont delete it
