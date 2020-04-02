###
# This Makefile provides portable, stack-agnostic commands (unlike npm)
#
# These commands are meant to be run from your local machine, not from
# within the docker container. Each command handles exec'ing into the
# docker container to run the command

ifneq (,)
	This makefile requires GNU Make.
endif
# #######
# Basics
platform-onboarded:
	docker-compose exec platform /bin/bash
	composer migrate

platform-exec:
	@docker exec -it platform_platform_1 bash
exec:platform-exec

# ######
# DB
seed-docker:
	docker exec -it platform_platform_1 /bin/bash
	composer migrate

migrate:
	make migrate-docker

db-total-reset:
	@echo "🔥 Resetting DB"
	docker exec -it platform_platform_1 composer dump-autoload
	make migrate-docker
	composer migrate
dbtr:db-total-reset

# ######
# CLIs
redis-cli:
	docker exec -it platform_redis_1 redis-cli

mysql-cli:
	docker exec -it platform_mysql_1 /bin/bash
	mysql -u root -p

# ###########
# TESTING
test:
	@docker exec -it platform_platform_1 bash
	composer run test

# Listing commands in the .PHONY section ensures that the command is recognized when
# running `make [command]`
#
# Otherwise, if a command is the same name as a folder/file in the root of the project,
# and the make command is run, `make` will attempt to `make` that file or folder.
.PHONY: platform-onboarded \
	platform-exec \
	exec \
	migrate \
	db-total-reset \
	dbtr \
	drop-db \
	redis-cli \
	mysql-cli \
	test \

