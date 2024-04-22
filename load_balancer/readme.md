

## .conf file
replace the two ip addresses in the .conf with ip of prod and failsafe

## commands to run
```
sudo apt update 

sudo apt install curl

curl -s https://install.zerotier.com | sudo bash
```

# Join ZeroTier Network
Join network

# Setup firewall
```
sudo ufw --force enable
sudo ufw allow http
sudo ufw allow https
sudo ufw default deny incoming
sudo ufw default allow outgoing
```

# Install and setup NGINX
```
sudo apt install nginx
```
# Copy nginx config to location
```
sudo cp -r nginx.conf /etc/nginx/nginx.conf
```
# Change group+permissions
```
sudo chown -R root:root /etc/nginx
```
# Create certs
```
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/server.localhost.key -out /etc/ssl/certs/server.localhost.crt
sudo openssl dhparam -out /etc/ssl/dhparam.pem 2048
```

# Restart NGINX
```
sudo systemctl restart nginx
```

# If you get an error while restarting nginx, check what is using port 80 (in my case it was apache2 due to cloned vm)
```
sudo systemctl stop apache2
```
