# WhatToWatch


SET UP FRESH UBUNTU INSTALL
sudo apt install composer
sudo apt install rabbitmq-server
sudo apt install php-amqplib
sudo rabbitmq-plugins enable rabbitmq_management

MAKE A DIRECTORY THEN CD INTO IT
touch composer.json

PUT THESE VALUES INTO THE composer.json FILE:
{
  "require": {
      "php-amqplib/php-amqplib": "^3.6"
  }
}

THEN DO composer install IN THE DIRECTORY

