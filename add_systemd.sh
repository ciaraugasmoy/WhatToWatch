#!/bin/bash 
dir="/etc/systemd/system/rmq_server.service"
touch $dir 
echo "[Unit]" > $dir
echo "Description=IT490 Rabbit MQ server" >> $dir
echo "[Service]" >> $dir
echo "Type=simple" >> $dir
echo "ExecStart=/usr/bin/php `pwd`/rabbitmq/rpc_server.php" >> $dir
echo "Restart=on-failure" >> $dir
echo "[Install]" >> $dir
echo "WantedBy=multi-user.target" >> $dir
sudo systemctl enable rmq_server
sudo systemctl start rmq_server
sudo systemctl daemon-reload 
