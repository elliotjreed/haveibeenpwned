.PHONY: all
all: vendor

vendor: composer.json composer.lock
	composer install

.PHONY: test phpcs static-analysis phpunit composer-validate composer-outdated

test: phpcs static-analysis phpunit mutation composer-validate composer-outdated

static-analysis: vendor
	composer run-script static-analysis

mutation: vendor
	composer run-script mutation

phpunit: vendor
	composer run-script phpunit:coverage

debug: vendor
	composer run-script phpunit:debug

md: vendor
	composer run-script phpmd

phpcs: vendor
	composer run-script phpcs

composer-validate: vendor
	composer validate --no-check-publish

composer-outdated: vendor
	composer outdated

.PHONY: clean
clean:
	rm -rf build/ vendor/
