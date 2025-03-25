# VARIABLES
ENV_FILE	   = .docker/.env
DOCKER_COMPOSE = docker compose
CONTAINER      = webserver
EXEC           = $(DOCKER_COMPOSE) exec -t $(CONTAINER)
EXEC_PHP       = $(EXEC) php
SYMFONY        = $(EXEC_PHP) bin/console
COMPOSER       = $(EXEC) composer
CURRENT-DIR  := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))
CURRENT_UID  := $(shell id -u)


.DEFAULT_GOAL := deploy

.PHONY: deploy build deps update-deps composer-install ci composer-update cu composer-require cr composer start stop down recreate rebuild test reload clear bash style lint lint-diff static-analysis schema-validate create-db create-db/dev create-db/test migrate migrate/dev migrate/test drop-db drop-db/dev drop-db/test migration/diff migration/gen rm-database

deploy: build
	@echo "📦 Build done"

build: create_env_file rebuild

# 🚚 Dependencies
deps: composer-install

update-deps: composer-update

create_env_file:
	@if [ ! -f .env.local ]; then cp .env .env.local; fi
# 🐘 Composer
composer-install ci: ACTION=install

composer-update cu: ACTION=update $(module)

composer-require cr: ACTION=require $(module)

composer composer-install ci composer-update composer-require cr: create_env_file
	@$(COMPOSER) $(ACTION) \
			--ignore-platform-reqs \
			--no-ansi \
			--prefer-dist
# 🐳 Docker Compose
start: create_env_file
	@echo "🚀 Deploy!!!"
	@$(DOCKER_COMPOSE) up -d
stop:
	$(DOCKER_COMPOSE) stop
down:
	$(DOCKER_COMPOSE) down
recreate:
	@echo "🔥 Recreate container!!!"
	@$(DOCKER_COMPOSE) up -d --build --remove-orphans --force-recreate
	make deps
rebuild:
	@echo "🔥 Rebuild container!!!"
	@$(DOCKER_COMPOSE) build --pull --force-rm --no-cache
	make start
	make deps

# 🧪 Tests
test: create_env_file
	 $(EXEC) sh -c "APP_ENV=test ./vendor/bin/phpunit -c phpunit.xml.dist --no-coverage --order-by=random"

test/coverage: create_env_file
	$(EXEC)  sh -c "APP_ENV=test ./vendor/bin/phpunit --coverage-text --coverage-clover=coverage.xml --order-by=random"

# 🦝 Apache
reload:
	@$(EXEC) /bin/bash service apache2 restart || true

# 🧹 Clear cache
clear:
	$(SYMFONY) cache:clear

# 🐚 Shell
bash:
	$(DOCKER_COMPOSE) exec -it $(CONTAINER) /bin/bash

# 🦊 Linter
style: lint static-analysis
lint:
	@$(EXEC) sh -c "PHP_CS_FIXER_IGNORE_ENV=1 ./vendor/bin/php-cs-fixer fix --diff"
	@echo "Coding Standar Fixer Executed ✅"

lint-diff:
	@$(EXEC)  ./vendor/bin/php-cs-fixer fix --dry-run --diff
	@echo "Coding Standar Fixer Executed ✅"

static-analysis:
	@$(EXEC)  ./vendor/bin/phpstan analyse -c phpstan.dist.neon

rm-database:
	@docker-compose rm -f database

create-db: create-db/dev create-db/test
create-db/dev:
	@$(SYMFONY) doctrine:database:create --env=dev --no-interaction --if-not-exists
create-db/test:
	@$(SYMFONY)  doctrine:database:create --env=test --no-interaction --if-not-exists

migrate: migrate/dev migrate/test
migrate/dev:
	@$(SYMFONY)  doctrine:migrations:migrate --env=dev

migrate/test:
	@$(SYMFONY)  doctrine:migrations:migrate --env=test

migration/diff:
	@$(SYMFONY)  doctrine:migrations:diff

migration/gen:
	@$(SYMFONY)  doctrine:migrations:generate

drop-db: drop-db/dev  drop-db/test
drop-db/dev:
	@$(SYMFONY)  doctrine:database:drop --force --env=dev --if-exists
drop-db/test:
	@$(SYMFONY)  doctrine:database:drop --force --env=test --if-exists
schema-validate: schema-validate/dev schema-validate/test
schema-validate/dev:
	@$(SYMFONY)  doctrine:schema:validate
schema-validate/test:
	@$(SYMFONY)  doctrine:schema:validate --env=test