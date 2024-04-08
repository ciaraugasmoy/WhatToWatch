#replace the two ip addresses in the .conf with ip of prod and failsafe

sudo apt update 

sudo apt install curl

curl -s https://install.zerotier.com | sudo bash

#JOIN ZEROTIER NETWORK

#setup firewalls
sudo ufw --force enable
sudo ufw allow http
sudo ufw allow https
sudo ufw default deny incoming
sudo ufw default allow outgoing


sudo apt install nginx

#copy nginx config to location
sudo cp -r nginx.conf /etc/nginx/nginx.conf

#change group+permissions
sudo chown -R root:root /etc/nginx

#restart nginx
sudo systemctl restart nginx

#IF U GET ERROR: sudo systemctl stop apache2
