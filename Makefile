PHP74_IMAGE      = php:7.4.0-fpm
PHP_LATEST_IMAGE = php:8.5.2-fpm
COMPOSER_IMAGE   = composer:2.2
DOCKER_RUN_PHP74        = docker run --rm -v "$(PWD):/app" -w /app $(PHP74_IMAGE)
DOCKER_RUN_PHP_LATEST   = docker run --rm -v "$(PWD):/app" -w /app $(PHP_LATEST_IMAGE)
DOCKER_RUN_COMPOSER     = docker run --rm -v "$(PWD):/app" -w /app $(COMPOSER_IMAGE)

.PHONY: install lint lint-latest test test-latest ecs ecs-fix phpstan sandbox

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
