init: build tests

build:
	docker-compose build
	docker-compose run --rm app composer install
	docker-compose run --rm app bin/console doctrine:schema:update --force


tests: phpunit behat

phpunit:
	docker-compose run --rm app bin/phpunit

behat:
	docker-compose run --rm app vendor/bin/behat

up:
	docker-compose up -d