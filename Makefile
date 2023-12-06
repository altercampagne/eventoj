.PHONY: ${TARGETS}
.DEFAULT_GOAL := help

DOCKER_COMPOSE=docker compose $*

help:
	@echo "\033[1;36mCIKLANTO AVAILABLE COMMANDS :\033[0m"
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z_0-9-]+:.*?##/ { printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[33m%s\033[0m\n", substr($$0, 5) } ' Makefile

##@ Base commands
doctor: ## Check that all needed requirements are installed on your host
	@./bin/doctor

##@ Docker commands
start: ## Start the whole docker stack
	@$(DOCKER_COMPOSE) up --detach --remove-orphans --build

stop: ## Stop the docker stack
	@$(DOCKER_COMPOSE) stop

destroy: ## Destroy all containers, volumes, networks, ...
	@$(DOCKER_COMPOSE) down --remove-orphans --volumes --rmi=local

bash: ## Enter in the application container directly
	@$(DOCKER_COMPOSE) exec php /bin/bash

psql: ## Enter in DB container
	@$(DOCKER_COMPOSE) exec database psql -h database -U app app

##@ Backends commands
db-reset: ## Reset DB
	@$(DOCKER_COMPOSE) run php bin/reset-db

db-reset-test: ## Reset test DB
	@$(DOCKER_COMPOSE) run -e APP_ENV=test php bin/reset-db

messenger-consume: ## Consume messages from async queue
	@$(DOCKER_COMPOSE) run php bin/console messenger:consume async -vv -l 1 --time-limit=60

##@ Quality commands
test: ## Run all tests
	@$(DOCKER_COMPOSE) run php bin/phpunit

phpstan: ## Run PHPStan
	@$(DOCKER_COMPOSE) run php composer install --working-dir=tools/phpstan
	@$(DOCKER_COMPOSE) run php tools/phpstan/vendor/bin/phpstan analyse

cs-lint: ## Lint all files
	@$(DOCKER_COMPOSE) run php bin/console lint:twig templates/
	@$(DOCKER_COMPOSE) run php bin/console lint:yaml config/
	@$(DOCKER_COMPOSE) run php composer install --working-dir=tools/php-cs-fixer
	@$(DOCKER_COMPOSE) run php tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix: ## Fix CS using PHP-CS
	@$(DOCKER_COMPOSE) run php composer install --working-dir=tools/php-cs-fixer
	@$(DOCKER_COMPOSE) run php tools/php-cs-fixer/vendor/bin/php-cs-fixer fix
