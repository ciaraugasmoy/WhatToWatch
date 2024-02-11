mysql -u root -p -e "CREATE USER 'tester'@'localhost' IDENTIFIED BY 'testerpassword'; GRANT ALL PRIVILEGES ON testdb.* TO 'tester'@'localhost'; FLUSH PRIVILEGES;"
mysql -u tester -p testerpassword -e "CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL); INSERT INTO users (username, password) VALUES ('steve', '12345');"
