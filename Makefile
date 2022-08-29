build:
	cp -R -u -p -v ./.env.example ./.env
	docker-compose build

down:
	docker-compose down

up:
	docker-compose up -d
	docker-compose exec platform composer run compile

start:
	make build
	make up

enter:
	docker-compose exec platform bash

pre-test:
	docker-compose exec platform composer run pre-test

test:
	docker-compose exec platform composer run test-dev

pre-push-test:
	docker-compose exec platform composer run pre-push-test

test-ci:
	docker-compose exec platform composer run test

cleanup:
	docker-compose exec platform composer run fixlint

stop:
	docker-compose stop
