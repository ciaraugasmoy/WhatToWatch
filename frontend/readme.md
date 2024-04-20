# Setup

```
sudo apt install php -y
sudo apt-get install php-curl
sudo apt install composer -y
chmod +x client_rpc.php
```

Currently supported requests are:
- `login` needing `username` `password` `email` `dob`
- `signup` needing `username` `password`
- `validate` needing `username` `token`
''' rsync -av --delete ~/WhatToWatch/frontend/html/ /var/www/what2watch '''

in html > client > config.ini adjust your credentials for access to rabbitmq server