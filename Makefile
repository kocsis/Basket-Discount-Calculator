all: run

test:
	docker run -v $(shell pwd):/app --rm phpunit/phpunit --colors=always tests

run:
	- docker-compose up -d
	- Site: http://localhost
