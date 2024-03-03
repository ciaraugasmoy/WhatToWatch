# binding addresses
'''
echo -e "[mysqld]\nbind-address = 0.0.0.0" | sudo tee -a /etc/mysql/my.cnf
'''
To set up Database
Please run
```
sudo apt install mysql-server

sudo mysql < database_setup.sql
```
To populate with test data please run
```
./database_testing_setup.sh
```

**OTHER**
command to make .sh file executable incase it isnt
```
chmod +x database_testing_setup.sh
```
The file `create_db_users.sh` just ignore it pls, dont delete it
