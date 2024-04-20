#!/bin/bash

# Run sudo chmod +x ssl_apache.sh

# Generate a self-signed SSL certificate
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/server.localhost.key -out /etc/ssl/certs/server.localhost.crt

# Update web server configuration file for HTTPS
echo "
<VirtualHost www.what2watch.com:80>
        # The ServerName directive sets the request scheme, hostname and port that
        # the server uses to identify itself. This is used when creating
        # redirection URLs. In the context of virtual hosts, the ServerName
        # specifies what hostname must appear in the request's Host: header to
        # match this virtual host. For the default virtual host (this file) this
        # value is not decisive as it is used as a last resort host regardless.
        # However, you must set it for any further virtual host explicitly.
        
        ServerName www.what2watch.com
        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/what2watch
        Redirect permanent / https://www.what2watch.com

        # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
        # error, crit, alert, emerg.
        # It is also possible to configure the loglevel for particular
        # modules, e.g.
        #LogLevel info ssl:warn

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

        # For most configuration files from conf-available/, which are
        # enabled or disabled at a global level, it is possible to
        # include a line for only one particular virtual host. For example the
        # following line enables the CGI configuration for this host only
        # after it has been globally disabled with "a2disconf".
        #Include conf-available/serve-cgi-bin.conf
</VirtualHost>

<VirtualHost www.what2watch.com:443>
        ServerName www.what2watch.com
        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/what2watch

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

        SSLEngine on
        SSLCertificateKeyFile /etc/ssl/private/server.localhost.key
        SSLCertificateFile /etc/ssl/certs/server.localhost.crt
</VirtualHost>
" > /etc/apache2/sites-available/what2watch.conf

# Enable SSL module and the site
sudo a2enmod ssl
sudo a2ensite what2watch.conf #Should already be activated, just a precaution

# To make sure domain is hosted locally - CHANGE TO APACHE VM IP
echo "
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