#!/bin/bash
sudo mkdir /var/www/what2watch
sudo chmod -R 755 /var/www
sudo rsync -av --delete ~/WhatToWatch/frontend/html/ /var/www/what2watch
