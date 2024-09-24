.SILENT:

all: test

.PHONY: test
test:
	docker compose run --rm test

.PHONY: build
build:
	docker compose build