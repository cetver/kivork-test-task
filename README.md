INSTALL
-------
### php
```bash
git clone https://github.com/cetver/kivork-test-task 
composer install
```
### db
```bash
sudo -H -u postgres psql <<EOT
CREATE DATABASE kivork_alexandr_cetvertacov;
EOT
cd <project-dir>
./yii migrate/up --migrationPath=modules/forecast/migrations --interactive=0
```
### server
```bash
sudo bash -c 'echo "127.0.0.1    kivork-alexandr-cetvertacov.dev" >> /etc/hosts'
если ff будет перекидывать на https хост выше, то network.stricttransportsecurity.preloadlist - false
server {
    listen 80; 

    server_name kivork-alexandr-cetvertacov.dev;
    root        /var/www/kivork-test-task/web; # <====================================
    index       index.php;  

    error_log   /var/log/nginx/kivork-alexandr-cetvertacov-error.log; # <====================================

    location / {        
        try_files $uri /index.php$is_args$args;
    } 
    
    location ~ ^/assets/.*\.php$ {
        deny all;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;        
        fastcgi_pass unix:/var/run/php/php7.3-fpm.sock; # <====================================
        try_files $uri =404;
    }

    location ~* /\. {
        deny all;
    }
}
```
ОПИСАНИЕ
-------
Все интересное [тут](https://github.com/cetver/kivork-test-task/tree/master/modules/forecast)

### console command 
```bash
./yii forecast
```

### web 
http://kivork-alexandr-cetvertacov.dev/forecast/default/index


PS
--
В коде нет комментариев к методам, тестов, некоторых валидаций, вперемешку return type declarations.

Установка происходит не через системы виртуализации или контейнеризации, а через команды.

Это не production-ready код, а тестовое  задание.