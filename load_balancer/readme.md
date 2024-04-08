##.conf file
replace the two ip addresses in the .conf with ip of prod and failsafe

##commands to run
```
sudo apt update 

sudo apt install curl

curl -s https://install.zerotier.com | sudo bash
```

#Join ZeroTier Network

#Setup firewalls
```
sudo ufw --force enable
sudo ufw allow http
sudo ufw allow https
sudo ufw default deny incoming
sudo ufw default allow outgoing
```

#Install and setup NGINX
```
sudo apt install nginx
```
#Copy nginx config to location
```
sudo cp -r nginx.conf /etc/nginx/nginx.conf
```
#Change group+permissions
```
sudo chown -R root:root /etc/nginx
```
#restart nginx
```
sudo systemctl restart nginx
```

#If you get an error while restarting nginx, check what is using port 80 (in my case it was apache2 due to cloned vm)
```
sudo systemctl stop apache2
```
now you can run in frontend/html/setup
```
steve.php
```
the password for steve is 12345 and this generate some friend requests, accepted and pending as well as some fake reviews
