SHELL := /bin/bash
.PHONY: help install install-npm test lint lint-php lint-phpstan lint-js lint-css lint-md lint-html lint-json lint-fix clean

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-18s\033[0m %s\n", $$1, $$2}'

install: ## Install PHP dependencies
	composer install

install-npm: ## Install Node.js dependencies
	npm install

test: ## Run PHPUnit tests
	vendor/bin/phpunit --no-coverage

test-coverage: ## Run tests with HTML coverage report
	vendor/bin/phpunit --coverage-html coverage

lint: lint-php lint-phpstan lint-js lint-css lint-md lint-html lint-json ## Run all linters (report mode — no auto-fix)

lint-php: ## Run PHPCS (WordPress VIP + PSR-12)
	vendor/bin/phpcs

lint-phpstan: ## Run PHPStan static analysis (Level 5)
	vendor/bin/phpstan analyse --level=5

lint-js: ## Run ESLint
	npm run lint:js

lint-css: ## Run Stylelint
	npm run lint:css

lint-md: ## Run markdownlint
	npm run lint:md

lint-html: ## Run HTMLHint
	npm run lint:html

lint-json: ## Validate JSON files
	npm run lint:json

lint-fix: ## Auto-fix PHP code style (PHPCBF — use with caution, not in CI)
	vendor/bin/phpcbf

clean: ## Remove generated files
	rm -rf vendor/
	rm -rf node_modules/
	rm -rf coverage/
	rm -f composer.lock
	rm -f .phpunit.result.cache

