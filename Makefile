## All these make targets (commands) are only useful for a Docker environment!

# Master command to build and start everything
start: build up apply

# Builds containers (init .env while at it)
build:
	[ ! -f ./.env ] && cp -p -v ./.env.dockerinit ./.env || true
	docker-compose build

down:
	docker-compose down

# Starts containers in the background
up:
	docker-compose up -d

# Applies changes (dependencies, migrations) to running containers
apply: composer-install migrate

# Runs composer install (updates dependencies)
composer-install:
	docker-compose exec platform util wait_bootstrap
	docker-compose exec platform util run_composer_install
	docker-compose exec platform_tasks util wait_bootstrap
	docker-compose exec platform_tasks util run_composer_install

# Runs database migrations
migrate:
	docker-compose exec platform util wait_bootstrap
	docker-compose exec platform util run_migrations

# Tails logs on the screen
logs:
	docker-compose logs -f

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
