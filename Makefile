ifneq ($(shell docker compose version 2>/dev/null),)
  DOCKER_COMPOSE=docker compose
else
  DOCKER_COMPOSE=docker-compose
endif

## All these make targets (commands) are only useful for a Docker environment!

# Master command to build and start everything
start: build up apply

debug: export XDEBUG_MODE=debug,develop
debug: build up apply

# Builds containers (init .env while at it)
build:
	[ ! -f ./.env ] && cp -p -v ./.env.dockerinit ./.env || true
	$(DOCKER_COMPOSE) build

down:
	$(DOCKER_COMPOSE) down

# Starts containers in the background
up:
	$(DOCKER_COMPOSE) up -d

# Applies changes (dependencies, migrations) to running containers
apply: composer-install migrate

# Runs composer install (updates dependencies)
composer-install:
	$(DOCKER_COMPOSE) exec platform util wait_bootstrap
	$(DOCKER_COMPOSE) exec platform util run_composer_install
	$(DOCKER_COMPOSE) exec platform_tasks util wait_bootstrap
	$(DOCKER_COMPOSE) exec platform_tasks util run_composer_install

# Runs database migrations
migrate:
	$(DOCKER_COMPOSE) exec platform util wait_bootstrap
	$(DOCKER_COMPOSE) exec platform util run_migrations

# Tails logs on the screen
logs:
	$(DOCKER_COMPOSE) logs -f

enter:
	$(DOCKER_COMPOSE) exec platform bash

pre-test:
	$(DOCKER_COMPOSE) exec platform composer run pre-test

test: export XDEBUG_MODE=coverage
test:
	$(DOCKER_COMPOSE) exec platform composer run test-dev

pre-push-test:
	$(DOCKER_COMPOSE) exec platform composer run pre-push-test

test-ci:
	$(DOCKER_COMPOSE) exec platform composer run test

cleanup:
	$(DOCKER_COMPOSE) exec platform composer run fixlint

stop:
	$(DOCKER_COMPOSE) stop
