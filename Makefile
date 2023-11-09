.PHONY: ${TARGETS}
.DEFAULT_GOAL := help

DOCKER_COMPOSE=docker compose $*

help:
	@echo "\033[1;36mCIKLANTOJ AVAILABLE COMMANDS :\033[0m"
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

##@ DB commands
db-reset: ## Reset DB data.
	@$(DOCKER_COMPOSE) run php bin/reset-db

phpstan: ## Run PHPStan
	@bin/qa phpstan analyse
