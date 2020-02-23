ROOT_DIR = $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))
PRIVATE_KEY_REL = storage/oauth-private.key
PUBLIC_KEY_REL = storage/oauth-public.key
PRIVATE_KEY = $(ROOT_DIR)/$(PRIVATE_KEY_REL)
PUBLIC_KEY = $(ROOT_DIR)/$(PUBLIC_KEY_REL)

APP_NAME = Hello DevOps Team

SHELL ?= /bin/bash
ARGS = $(filter-out $@,$(MAKECMDGOALS))

IMAGE_TAG = latest
IMAGE_NAME = zorkyy/messenger-api-sf3

BUILD_ID ?= $(shell /bin/date "+%Y%m%d-%H%M%S")

.SILENT: ;               # no need for @
.ONESHELL: ;             # recipes execute in same shell
.NOTPARALLEL: ;          # wait for this target to finish
.EXPORT_ALL_VARIABLES: ; # send all vars to shell
Makefile: ;              # skip prerequisite discovery

# Run make help by default
.DEFAULT_GOAL = help

VERSION = 0.0.1

.env:
	cp $@.example $@

%.env:
	cp $@.dist $@

.PHONY: up
up: ## Starts and attaches to containers for a service
	docker-compose up -d

.PHONY: down
down: ## Stop & destroy containers
	docker-compose down

.PHONY: stats
stats: ## Show containers info (CPU, Mem, PIDs, Status, Ports etc.)
	docker stats \
		--no-stream \
		--format \
			"table {{.Name}}\t{{.CPUPerc}}\t{{.MemPerc}}\t{{.MemUsage}}\t{{.NetIO}}\t{{.BlockIO}}\t{{.PIDs}}"

.PHONY: shell
shell: docker-compose.yml ## Go to the application container via sh (if any)
	docker-compose exec $(ARGS) sh

.PHONY: bash
bash: docker-compose.yml ## Go to the application container via bash (if any)
	docker-compose exec $(ARGS) /bin/bash

.PHONY: install
install: composer.phar ## Install deps
	docker-compose exec app composer install --no-interaction

.PHONY: init-db
init-db: ## Init DB and provide app with initial data
	docker-compose exec app sh -c "./bin/init-db"

.PHONY: test
test: ## Run tests
	docker-compose exec app sh -c "./vendor/bin/codecept run"

.PHONY: build
build: docker-compose.yml ## Docker build app
	docker-compose build app

.PHONY: help
help: .title ## Show this help and exit (default target)
	echo ''
	printf "                %s: \033[94m%s\033[0m \033[90m[%s] [%s]\033[0m\n" "Usage" "make" "target" "ENV_VARIABLE=ENV_VALUE ..."
	echo ''
	echo '                Available targets:'
	# Print all commands, which have '##' comments right of it's name.
	# Commands gives from all Makefiles included in project.
	# Sorted in alphabetical order.
	echo '                ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
	grep -hE '^[a-zA-Z. 0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | \
		 awk 'BEGIN {FS = ":.*?## " }; {printf "\033[36m%+15s\033[0m: %s\n", $$1, $$2}'
	echo '                ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
	echo ''

%:
	@:
