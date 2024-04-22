#!/bin/bash

# Run sudo chmod +x ssl_apache.sh

# Generate a self-signed SSL certificate
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/server.localhost.key -out /etc/ssl/certs/server.localhost.crt


# Update web server configuration file for HTTPS
sudo echo "

<VirtualHost *:80>
        ServerName www.what2watch.com
        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/what2watch

        ErrorLog /error.log
        CustomLog /access.log combined

</VirtualHost>

<VirtualHost *:443>
        ServerName www.what2watch.com
        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/what2watch

        ErrorLog /error.log
        CustomLog /access.log combined

        SSLEngine on
        SSLCertificateKeyFile /etc/ssl/private/server.localhost.key
        SSLCertificateFile /etc/ssl/certs/server.localhost.crt
</VirtualHost>

<Directory /var/www>
        Options FollowSymLinks
        AllowOverride All

        Order Allow,Deny
        Allow From All
</Directory>
" > /etc/apache2/sites-available/what2watch.conf

# Enable SSL module and the site
sudo a2enmod ssl
sudo rm /etc/apache2/sites-enabled/* #removing any site that is already enabled (including previous what2watch)
sudo a2ensite what2watch.conf #Should already be activated, just a precaution

# To make sure domain is hosted locally - CHANGE TO APACHE VM IP
sudo echo "
127.0.0.1       www.what2watch.com 
127.0.0.1       localhost
127.0.1.1       490test.myguest.virtualbox.org  490test

# The following lines are desirable for IPv6 capable hosts
::1     ip6-localhost ip6-loopback
fe00::0 ip6-localnet
ff00::0 ip6-mcastprefix
ff02::1 ip6-allnodes
ff02::2 ip6-allrouters
" > /etc/hosts

# Restart Apache
systemctl restart apache2