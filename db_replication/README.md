# Setup and instructions
**Replace source/replica IP's and REPLICA_VM_NAME in source script**
**Replace source IP address in replica script**<br>
**Replace SOURCE_LOG_FILE and SOURCE_LOG_POS in replica-script AFTER running source-script**
The command in MySQL is ```SHOW MASTER STATUS;``` Run on your source machine
_________________________________________________

Make sure the files are executable and are run as sudo
```chmod +x source-replication.sh```
```chmod +x replica-replication.sh```
```sudo ./source-replication.sh```
```sudo ./replica-replication.sh```
__________________________________________________



