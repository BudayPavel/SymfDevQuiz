#!/usr/bin/env bash
service nginx stop
service apache2 stop
/etc/init.d/mysql stop
service php7.2-fpm stop
docker-compose up --build --force-recreate
docker-compose exec php composer install
docker ps -a
docker-compose logs -f