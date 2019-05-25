#!/usr/bin/env bash
sudo docker-compose exec php-fpm bin/console doctrine:schema:drop --force --env=dev --em=default
sudo docker-compose exec php-fpm bin/console doctrine:schema:drop --force --env=dev --em=planet_test1
sudo docker-compose exec php-fpm bin/console doctrine:schema:drop --force --env=dev --em=planet_test2
sudo docker-compose exec php-fpm bin/console doctrine:schema:create --env=dev --em=default
sudo docker-compose exec php-fpm bin/console doctrine:schema:create --env=dev --em=planet_test1
sudo docker-compose exec php-fpm bin/console doctrine:schema:create --env=dev --em=planet_test2
sudo docker-compose exec php-fpm bin/console doctrine:fixtures:load --env=dev --em=default -qvv
