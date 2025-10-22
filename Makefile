.SILENT:

all: test

.PHONY: shell sh
shell sh:
	docker compose run --rm shell sh

.PHONY: test
test:
	docker compose run --rm shell ./vendor/bin/phpunit tests

.PHONY: test-legacy
test-legacy:
	docker compose run --rm test

.PHONY: build
build:
	docker compose build
