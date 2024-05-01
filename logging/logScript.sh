#!/bin/bash

password=$(cat ./.env)

while true
do
    php logHandler.php

    sshpass -p "$password" rsync -avz -e ssh ./logs/WhatToWatch.log vboxuser@172.27.129.66:~/WhatToWatch/logging/logs/WhatToWatch.log

    sleep 10
done
