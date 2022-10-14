up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear docker-pull docker-build docker-up

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

bash:
	docker-compose exec php-fpm bash

bash-front:
	docker-compose exec frontend-nodejs bash

f-up:
	docker-compose exec frontend-nodejs npm install build

watch:
	docker-compose exec frontend-nodejs npm run watch

nodejs:
	docker-compose exec frontend-nodejs

telegram-get-messages:
	docker-compose exec php-fpm bin/console telegram:get-messages




