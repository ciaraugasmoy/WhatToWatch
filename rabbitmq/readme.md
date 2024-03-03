# Setup

```
sudo apt install php -y
sudo apt-get install php-curl
sudo apt install composer -y
composer install

```

```
sudo apt install rabbitmq-server -y
sudo apt install php-amqplib -y
sudo apt-get install php-mysql -y
```

setting up rabbitmq
```
sudo rabbitmqctl add_vhost brokerHost
sudo rabbitmqctl add_user mqadmin mqadminpass
sudo rabbitmqctl set_user_tags mqadmin administrator
sudo rabbitmqctl set_permissions -p brokerHost mqadmin ".*" ".*" ".*"
sudo rabbitmqctl delete_user guest
```
# Important
## Configuration
Please adjust the credentials in `credentials.ini` to YOUR DATABASE credentials. In particular please update the IP to be the DB ip for the group network.
