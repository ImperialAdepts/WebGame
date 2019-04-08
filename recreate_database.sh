#!/usr/bin/env bash
#sudo docker-compose exec php-fpm bin/console doctrine:schema:drop --force --env=dev --em=default --dump-sql
sudo docker-compose exec php-fpm bin/console doctrine:schema:drop --force --env=dev --em=default
#sudo docker-compose exec php-fpm bin/console doctrine:schema:drop --force --env=dev --em=planet --dump-sql
sudo docker-compose exec php-fpm bin/console doctrine:schema:drop --force --env=dev --em=planet
#sudo docker-compose exec php-fpm bin/console doctrine:schema:create --env=dev --em=default --dump-sql
sudo docker-compose exec php-fpm bin/console doctrine:schema:create --env=dev --em=default
#sudo docker-compose exec php-fpm bin/console doctrine:schema:create --env=dev --em=planet --dump-sql
sudo docker-compose exec php-fpm bin/console doctrine:schema:create --env=dev --em=planet
sudo docker-compose exec php-fpm bin/console doctrine:fixtures:load --env=dev --em=planet -v
