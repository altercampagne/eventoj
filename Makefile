.PHONY: ${TARGETS}
.DEFAULT_GOAL := help

DOCKER_COMPOSE=docker compose $*

help:
	@echo "\033[1;36mEVENTOJ AVAILABLE COMMANDS :\033[0m"
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z_0-9-]+:.*?##/ { printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[33m%s\033[0m\n", substr($$0, 5) } ' Makefile

##@ Base commands
install: vendors-install build start db-reset assets-install assets-build ## Start the docker stack and prepare the application
	@echo "\n"
	@echo "\033[32m🥳 EVERYTHING IS RUNNING! 🥳\033[0m"
	@echo "\033[32mVisit http://eventoj.local to continue.\033[0m"

vendors-install: ## Install vendors
	@$(DOCKER_COMPOSE) exec php composer ins
	@$(DOCKER_COMPOSE) exec php bin/console importmap:install

##@ Docker commands
build: ## Build docker stack
	@$(DOCKER_COMPOSE) build

start: ## Start the whole docker stack
	@$(DOCKER_COMPOSE) up --detach --remove-orphans
	@$(DOCKER_COMPOSE) cp .docker/paheko/association.sqlite paheko:/var/www/paheko/data/association.sqlite
	@$(DOCKER_COMPOSE) exec paheko chown www-data: /var/www/paheko/data/association.sqlite

stop: ## Stop the docker stack
	@$(DOCKER_COMPOSE) stop

destroy: ## Destroy all containers, volumes, networks, ...
	@$(DOCKER_COMPOSE) down --remove-orphans --volumes --rmi=local

bash: ## Enter in the application container directly
	@$(DOCKER_COMPOSE) exec php /bin/bash

psql: ## Enter in DB container
	@$(DOCKER_COMPOSE) exec database psql -h database -U app app

psql-test: ## Enter in Test DB container
	@$(DOCKER_COMPOSE) exec database psql -h database -U app app_test

##@ Backends commands
db-reset: ## Reset DB
	@$(DOCKER_COMPOSE) run php bin/reset-db

db-reset-test: ## Reset test DB
	@$(DOCKER_COMPOSE) run -e APP_ENV=test php bin/reset-db

messenger-consume: ## Consume messages from async queue
	@$(DOCKER_COMPOSE) run php bin/console messenger:consume async -vv -l 1 --time-limit=60

##@ Assets commands
assets-install: ## Download assets dependencies
	@$(DOCKER_COMPOSE) run php bin/console importmap:install

assets-build: ## Build assets (SASS)
	@$(DOCKER_COMPOSE) run php bin/console sass:build

##@ Quality commands
test: ## Run all tests
	@$(DOCKER_COMPOSE) run php bin/phpunit

phpstan: ## Run PHPStan
	@$(DOCKER_COMPOSE) run php composer install --working-dir=tools/phpstan
	@$(DOCKER_COMPOSE) run php tools/phpstan/vendor/bin/phpstan analyse --memory-limit=512M

cs-lint: ## Lint all files
	@$(DOCKER_COMPOSE) run php bin/console lint:twig templates/
	@$(DOCKER_COMPOSE) run php bin/console lint:yaml config/
	@$(DOCKER_COMPOSE) run php composer install --working-dir=tools/php-cs-fixer
	@$(DOCKER_COMPOSE) run php tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix: ## Fix CS using PHP-CS
	@$(DOCKER_COMPOSE) run php composer install --working-dir=tools/php-cs-fixer
	@$(DOCKER_COMPOSE) run php tools/php-cs-fixer/vendor/bin/php-cs-fixer fix
