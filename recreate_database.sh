#!/usr/bin/env bash
sudo docker-compose exec php-fpm bin/console doctrine:schema:drop --force
sudo docker-compose exec php-fpm bin/console doctrine:schema:create
sudo docker-compose exec php-fpm bin/console doctrine:fixtures:load --env=dev -q
