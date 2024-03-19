# What2Watch Repo

there is a readme in each folder for each server respectively

## Useful

```
curl -s https://install.zerotier.com | sudo bash 
sudo snap install zerotier
sudo snap start zerotier.one
sudo zerotier-cli join <network-id>
sudo zerotier-cli info
sudo zerotier-cli listnetworks <network-id>
```
## Setting Up Apache
```
sudo apt install apache2
./sync_apache.sh
sudo apt install composer
```
### Firewalls
```
sudo ufw default deny
sudo ufw allow "Apache Full"
sudo ufw enable
```

## Setting up RabbitMQ
```
sudo apt install rabbitmq-server
sudo apt install php
sudo apt install composer
sudo apt install php-curl
```
## Setting up testing data
in database
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