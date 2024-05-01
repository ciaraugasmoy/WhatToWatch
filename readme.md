# What2Watch Repo

there is a readme in each folder for each server respectively with additional information if any problems are encountered

## Setting Up Database
Please adjust user credentials in [user_setup](database/user_setup.sql) if necessary
```
sudo mysql< database_setup.sql
sudo mysql <user_setup.sql
cd test_data_scripts
./generate_users.sh
```
in rabbitmq/api
```
./watchProviderHandler.php
```
then you can in database/updates
```
sudo mysql < curation_pop.sql
```
the you can populate some movies using searchPop in rabbitmq/api
```
./searchPop.php
```
now you can run in frontend/html/setup
```
steve.php
```
the password for steve is 12345 and this generate some friend requests, accepted and pending as well as some fake reviews


## Setting up RabbitMQ
adjust credentials for database access [here](rabbitmq/credentials.ini.example)
adjust credentials for email [here](rabbitmq/email_credentials.ini.example)
```
sudo apt install rabbitmq-server
sudo apt install php
sudo apt install composer
sudo apt install php php-curl php-mysql -y
```
then *subject to change
```
sudo rabbitmqctl add_vhost brokerHost
sudo rabbitmqctl add_user mqadmin mqadminpass
sudo rabbitmqctl set_user_tags mqadmin administrator
sudo rabbitmqctl set_permissions -p brokerHost mqadmin ".*" ".*" ".*"
sudo rabbitmqctl delete_user guest
```

**Firewalls**
```
sudo ufw reset
sudo ufw default deny incoming
sudo ufw allow from WEBSERVERIP to any port 5672/tcp
sudo ufw enable
```

## Setting Up Apache
after setting up rabbitmq, please adjust [these credentials as necessary](frontend/html/client/config.ini.example)
```
sudo apt install apache2
sudo apt install php php-curl -y
cd frontend/html/
composer install
sudo apt install composer
sudo ./sync_apache.sh
```
**Firewalls**
```
sudo ufw default deny
sudo ufw allow "Apache Full"
sudo ufw enable
```

## Useful

```
curl -s https://install.zerotier.com | sudo bash 
sudo snap install zerotier
sudo snap start zerotier.one
sudo zerotier-cli join <network-id>
sudo zerotier-cli info
sudo zerotier-cli listnetworks <network-id>
```
