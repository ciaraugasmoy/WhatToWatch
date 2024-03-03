# Setup

binding addresses
'''
echo -e "[mysqld]\nbind-address = 0.0.0.0" | sudo tee -a /etc/mysql/my.cnf
'''
To set up Database, currently two users are hardcoded in there
```
sudo apt install mysql-server
sudo mysql < database_setup.sql
```
To populate with test data please run
```
./database_testing_setup.sh
```
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
