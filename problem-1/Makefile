setup:
	cd docker/php && ./build.sh

start:
	docker compose up -d

stop:
	docker compose stop

test-phpcs:
	docker compose exec coding-test-php vendor/bin/phpcs -s --standard=phpcs.xml.dist

test-phpstan:
	docker compose exec coding-test-php vendor/bin/phpstan analyse -c phpstan.neon --memory-limit 1G

test-unit:
	docker compose exec coding-test-php vendor/bin/phpunit
