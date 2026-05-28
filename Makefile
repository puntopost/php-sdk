PHP74_IMAGE      = php:7.4.0-fpm
PHP_LATEST_IMAGE = php:8.5.2-fpm
COMPOSER_IMAGE   = composer:2.2
DOCKER_RUN_PHP74        = docker run --rm -v "$(PWD):/app" -w /app $(PHP74_IMAGE)
DOCKER_RUN_PHP_LATEST   = docker run --rm -v "$(PWD):/app" -w /app $(PHP_LATEST_IMAGE)
DOCKER_RUN_COMPOSER     = docker run --rm -v "$(PWD):/app" -w /app $(COMPOSER_IMAGE)

.PHONY: install validate check lint lint-latest test test-latest ecs ecs-fix phpstan sandbox bash example

# Defaults for `make example` — override on the command line or via the shell env.
# e.g. PP_USERNAME=foo PP_PASSWORD=bar make example NAME=login
#      make example NAME=get_merchant PP_MERCHANT_ID=MX001
# Every other endpoint-specific value is prompted interactively by the script.
# (PP_ prefix avoids collisions with system env vars like USERNAME / USER.)
PP_HOST        ?= https://localhost
PP_USERNAME    ?=
PP_PASSWORD    ?=
PP_MERCHANT_ID ?=
NAME           ?=

check: ecs phpstan validate lint lint-latest test test-latest

validate:
	@echo "Validating composer.json..."
	@$(DOCKER_RUN_COMPOSER) validate --strict

install:
	@echo "Installing dependencies with Composer 2.2..."
	@$(DOCKER_RUN_COMPOSER) install --no-interaction --prefer-dist

lint:
	@echo "Running PHP 7.4 syntax check on all source files..."
	@$(DOCKER_RUN_PHP74) sh -c 'find src tests -name "*.php" | sort | xargs -I{} php -l {} && echo "\nAll files passed syntax check."'

lint-latest:
	@echo "Running PHP 8.5 syntax check on all source files..."
	@$(DOCKER_RUN_PHP_LATEST) sh -c 'find src tests -name "*.php" | sort | xargs -I{} php -l {} && echo "\nAll files passed syntax check."'

test:
	@echo "Running PHPUnit tests with PHP 7.4..."
	@$(DOCKER_RUN_PHP74) vendor/bin/phpunit --configuration phpunit.xml.dist

test-latest:
	@echo "Running PHPUnit tests with PHP 8.5 (latest)..."
	@$(DOCKER_RUN_PHP_LATEST) vendor/bin/phpunit --configuration phpunit.xml.dist

ecs:
	@echo "Checking code style..."
	@$(DOCKER_RUN_PHP74) vendor/bin/ecs check --no-progress-bar

ecs-fix:
	@echo "Fixing code style..."
	@$(DOCKER_RUN_PHP74) vendor/bin/ecs check --fix --no-progress-bar

phpstan:
	@echo "Running PHPStan..."
	@$(DOCKER_RUN_PHP74) vendor/bin/phpstan analyse --configuration phpstan.neon --no-progress --memory-limit=512M

sandbox:
	@echo "Running sandbox test..."
	@$(DOCKER_RUN_PHP74) php public/test.php

example:
	@if [ -z "$(NAME)" ]; then \
	    echo "Usage: make example NAME=<script> [PP_HOST=...] [PP_USERNAME=...] [PP_PASSWORD=...] [PP_MERCHANT_ID=...]"; \
	    echo "Available scripts:"; \
	    ls examples/*.php 2>/dev/null | sed 's|examples/||;s|\.php$$||' | grep -v '^bootstrap$$' | sed 's/^/  - /'; \
	    echo ""; \
	    echo "Required env: PP_USERNAME, PP_PASSWORD (and PP_MERCHANT_ID where relevant)"; \
	    echo "Optional env: PP_HOST (default: https://localhost)"; \
	    echo "Anything else (parcel/pudo/postal code, filters, etc.) is prompted by the script."; \
	    exit 1; \
	fi
	@if [ ! -f "examples/$(NAME).php" ]; then \
	    echo "ERROR: examples/$(NAME).php does not exist."; \
	    exit 1; \
	fi
	@docker run --rm -i $$([ -t 0 ] && echo -t) --network host \
	    -e PP_HOST="$(PP_HOST)" \
	    -e PP_USERNAME="$(PP_USERNAME)" \
	    -e PP_PASSWORD="$(PP_PASSWORD)" \
	    -e PP_MERCHANT_ID="$(PP_MERCHANT_ID)" \
	    -v "$(PWD):/app" -w /app $(PHP74_IMAGE) \
	    php examples/$(NAME).php

bash:
	@docker run --rm -it -v "$(PWD):/app" -w /app $(COMPOSER_IMAGE) sh
