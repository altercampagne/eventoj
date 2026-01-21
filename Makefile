.DEFAULT_GOAL := help

DOCKER_COMPOSE=docker compose $*

.PHONY: help
help:
	@echo "\033[1;36mAVAILABLE COMMANDS :\033[0m"
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z_0-9-]+:.*?##/ { printf "  \033[32m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[33m%s\033[0m\n", substr($$0, 5) } ' Makefile

##@ Base commands
.PHONY: doctor
doctor: ## Check that everything is OK
	@bin/doctor

.PHONY: install
install: build start vendors-install db-reset assets-install assets-build ## Start the docker stack and prepare the application
	@echo "\n"
	@echo "\033[32m🥳 EVERYTHING IS RUNNING! 🥳\033[0m"
	@echo "\033[32mVisit http://eventoj.local to continue.\033[0m"

.PHONY: vendors-install
vendors-install: ## Install vendors
	$(DOCKER_COMPOSE) exec php composer ins
	$(DOCKER_COMPOSE) exec php bin/console importmap:install

.PHONY: vendors-update
vendors-update: ## Update all vendors
	$(DOCKER_COMPOSE) run --rm php composer up
	$(DOCKER_COMPOSE) run --rm php composer --working-dir tools/php-cs-fixer up
	$(DOCKER_COMPOSE) run --rm php composer --working-dir tools/phpstan up
	$(DOCKER_COMPOSE) run --rm php composer --working-dir tools/twig-cs-fixer up
	$(DOCKER_COMPOSE) run --rm php composer --working-dir tools/rector up
	$(DOCKER_COMPOSE) run --rm php bin/console importmap:update

.PHONY: deploy-production
deploy-production: ## Deploy origin/main to production
	git fetch
	git push origin origin/main:production
	@echo "\033[32mVisit \033[33mhttps://github.com/altercampagne/eventoj/deployments/production\033[32m to see the progress of the deployment.\033[0m"

##@ Docker commands
.PHONY: build
build: ## Build docker stack
	$(DOCKER_COMPOSE) build

.PHONY: start
start: ## Start the whole docker stack
	$(DOCKER_COMPOSE) up --detach --remove-orphans
	$(DOCKER_COMPOSE) cp .docker/paheko/association.sqlite paheko:/var/www/paheko/data/association.sqlite
	$(DOCKER_COMPOSE) exec paheko chown www-data: /var/www/paheko/data/association.sqlite

.PHONY: stop
stop: ## Stop the docker stack
	$(DOCKER_COMPOSE) stop

.PHONY: destroy
destroy: ## Destroy all containers, volumes, networks, ...
	$(DOCKER_COMPOSE) down --remove-orphans --volumes --rmi=local

.PHONY: bash
bash: ## Enter in the application container directly
	$(DOCKER_COMPOSE) exec php /bin/bash

.PHONY: psql
psql: ## Enter in DB container
	$(DOCKER_COMPOSE) exec database psql -h database -U app app

.PHONY: psql-test
psql-test: ## Enter in Test DB container
	$(DOCKER_COMPOSE) exec database psql -h database -U app app_test

##@ Backends commands
.PHONY: db-reset
db-reset: ## Reset DB
	$(DOCKER_COMPOSE) run --rm php bin/reset-db
	$(DOCKER_COMPOSE) run --rm php bin/console eventoj:event:update-stage-full-property

.PHONY: db-reset-test
db-reset-test: ## Reset test DB
	$(DOCKER_COMPOSE) run --rm -e APP_ENV=test php bin/reset-db

.PHONY: messenger-consume
messenger-consume: ## Consume messages from async queue
	$(DOCKER_COMPOSE) run --rm php bin/console messenger:consume async -vv -l 1 --time-limit=60

##@ Assets commands
.PHONY: assets-install
assets-install: ## Download assets dependencies
	$(DOCKER_COMPOSE) run --rm php bin/console importmap:install

.PHONY: assets-build
assets-build: ## Build assets (SASS)
	$(DOCKER_COMPOSE) run --rm php bin/console sass:build

##@ Quality commands
.PHONY: sanitize-and-check
sanitize-and-check: rector-fix cs-fix phpstan db-reset-test test ## Run Rector, PHP-CS-fixer, PHPStan & tests
	$(DOCKER_COMPOSE) run --rm -e APP_ENV_test php bin/console doctrine:schema:validate

.PHONY: test
test: ## Run all tests
	$(DOCKER_COMPOSE) run --rm php rm -f var/cache/bab_tested_routes_checker_bundle_route_storage
	$(DOCKER_COMPOSE) run --rm php bin/phpunit
	$(DOCKER_COMPOSE) run --rm php bin/console bab:tested-routes-checker:check

.PHONY: phpstan
phpstan: tools/phpstan/vendor ## Run PHPStan
	$(DOCKER_COMPOSE) run --rm php tools/phpstan/vendor/bin/phpstan  --memory-limit=512M analyse

phpstan-generate-baseline: tools/phpstan/vendor ## Run PHPStan to generate the baseline
	$(DOCKER_COMPOSE) run --rm php tools/phpstan/vendor/bin/phpstan analyse  --memory-limit=512M --generate-baseline

.PHONY: cs-lint
cs-lint: tools/php-cs-fixer/vendor tools/twig-cs-fixer/vendor ## Lint all files
	$(DOCKER_COMPOSE) run --rm php bin/console lint:twig templates/
	$(DOCKER_COMPOSE) run --rm php bin/console lint:yaml config/ .github/
	$(DOCKER_COMPOSE) run --rm php tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --diff
	$(DOCKER_COMPOSE) run --rm php tools/twig-cs-fixer/vendor/bin/twig-cs-fixer lint

.PHONY: cs-fix
cs-fix: tools/php-cs-fixer/vendor ## Fix CS using PHP-CS
	$(DOCKER_COMPOSE) run --rm php tools/php-cs-fixer/vendor/bin/php-cs-fixer fix
	$(DOCKER_COMPOSE) run --rm php tools/twig-cs-fixer/vendor/bin/twig-cs-fixer fix

.PHONY: rector-dry-run
rector-dry-run: tools/rector/vendor ## Launch Rector in dry-run mode
	$(DOCKER_COMPOSE) run --rm php tools/rector/vendor/bin/rector process --dry-run

.PHONY: rector-fix
rector-fix: tools/rector/vendor ## Launch Rector to fix files
	$(DOCKER_COMPOSE) run --rm php tools/rector/vendor/bin/rector process

tools/php-cs-fixer/vendor:
	@$(DOCKER_COMPOSE) run --rm php composer install --working-dir=tools/php-cs-fixer
tools/phpstan/vendor:
	@$(DOCKER_COMPOSE) run --rm php composer install --working-dir=tools/phpstan
tools/rector/vendor:
	@$(DOCKER_COMPOSE) run --rm php composer install --working-dir=tools/rector
tools/twig-cs-fixer/vendor:
	@$(DOCKER_COMPOSE) run --rm php composer install --working-dir=tools/twig-cs-fixer
