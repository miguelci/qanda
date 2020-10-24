# Q/A app made with Laravel and the Artisan Console

## To prepare the app

This will start the docker containers locally. For this you will need to install
[docker compose](https://docs.docker.com/compose/install/).
After finishing the previous step you can run: 

- `docker-compose up -d`

## To run the app inside  php docker container

- `docker-compose exec php sh` - This command will put inside the container, where you'll be able to execute the code
which is connected already to a mysql database.

- `php artisan qanda:interactive` - this will start the Q/A app

- `php artisan qanda:reset` - this will reset all the progress

- `vendor/bin/phpunit` - to run all the tests
