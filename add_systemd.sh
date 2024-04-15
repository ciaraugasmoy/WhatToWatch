#!/bin/bash 
dir="/etc/systemd/system/rmq_server.service"
touch $dir 
echo "[Unit]" >> $dir
echo "Description=IT490 Rabbit MQ server" >> $dir
echo "[Service]" >> $dir
echo "Type=Simple" >> $dir
echo "ExecStart=/usr/bin/php `pwd`/rabbitmq/rpc_server.php" >> $dir
echo "Restart=always" >> $dir
echo "[Install]" >> $dir
echo "WantedBy=multi-user.target" >> $dir
sudo systemctl daemon-reload 
sudo systemctl enable rmq_server
sudo systemctl start rmq_server
