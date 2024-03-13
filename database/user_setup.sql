
CREATE USER IF NOT EXISTS 'what2watchadmin'@'localhost' IDENTIFIED BY 'what2watchpassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON what2watch.* TO 'what2watchadmin'@'localhost';
FLUSH PRIVILEGES;
CREATE USER IF NOT EXISTS 'vdbadmin'@'10.147.20.78' IDENTIFIED BY 'what2watchpassword';
GRANT SELECT, INSERT, UPDATE, DELETE ON what2watch.* TO 'vdbadmin'@'10.147.20.78';
FLUSH PRIVILEGES;
