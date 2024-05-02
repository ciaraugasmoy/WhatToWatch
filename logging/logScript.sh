#!/bin/bash

password=$(cat ./.env)

while true
do
    php logHandler.php

    sshpass -p "$password" rsync -avz -e ssh ./logs/what2watch.log vboxuser@172.27.129.66:~/WhatToWatch/logging/logs/what2watch.log

    sleep 10
done
