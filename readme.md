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