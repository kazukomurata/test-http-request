phpunit:
	docker-compose run --rm php /app/vendor/bin/phpunit
install:
	docker-compose run --rm composer composer install

	