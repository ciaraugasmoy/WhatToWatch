# Setup
- php, -curl to improve composer, -mysql to make connection to db
- composer to install php reqs
```
sudo apt install rabbitmq-server -y
sudo apt install php php-curl php-mysql -y
sudo apt install composer -y
composer install
```
this should be installed by composer but if it doesnt work do
```
sudo apt install php-amqplib -y
```

## Setting up rabbitmq credentials
maybe I will set up a .ini another time but for now do please, as these credentials are hardcoded into rpc_server.php on line 11
```
sudo rabbitmqctl add_vhost brokerHost
sudo rabbitmqctl add_user mqadmin mqadminpass
sudo rabbitmqctl set_user_tags mqadmin administrator
sudo rabbitmqctl set_permissions -p brokerHost mqadmin ".*" ".*" ".*"
sudo rabbitmqctl delete_user guest
```
## setting up mysql database credentials
Please adjust the credentials in `credentials.ini` to YOUR DATABASE credentials. In particular please update the IP to be the DB ip for the group network.

# Files Summary
rpc_server.php is an executable which runs the server class, accepting json messages from the client and returning a response also in json format.
UserHandler.php connects to the database and provides `login`, `validation`, and `signup` functions.

# Firewalls Setup
Please use the ip for the webserver in your specific network
```
sudo ufw reset -y
sudo ufw default deny incoming
sudo ufw allow from WEBSERVERIP to any port 5672
sudo ufw enable
```
