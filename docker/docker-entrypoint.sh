#!/bin/sh

/usr/local/bin/composer install
/var/www/app/bin/console assets:install
chmod -R 777 /var/www/app/var/
chmod 777 /var/www/app/symfony

exec /var/www/app/symfony serve