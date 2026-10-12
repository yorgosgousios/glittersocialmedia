include .env

WPCLI = docker compose run --rm wpcli wp

default: up

## help	:	Print commands help.
.PHONY: help
help : Makefile
	@sed -n 's/^##//p' $<

## up	:	Start up containers.
.PHONY: up
up:
	@echo "Starting up containers for $(PROJECT_NAME)..."
	docker compose up -d --remove-orphans

## stop	:	Stop containers.
.PHONY: stop down
stop down:
	docker compose stop

## prune	:	Remove containers and their volumes (wipes the database and WP core).
.PHONY: prune
prune:
	docker compose down -v

## ps	:	List running containers.
.PHONY: ps
ps:
	@docker compose ps

## logs	:	Follow WordPress logs.
.PHONY: logs
logs:
	docker compose logs -f wordpress

## shell	:	Shell into the WordPress container.
.PHONY: shell
shell:
	docker compose exec wordpress bash

## install	:	Install WordPress, activate the theme and create pages/menus (idempotent).
.PHONY: install
install:
	docker compose run --rm \
		-e SITE_URL=https://$(PROJECT_BASE_URL) \
		-e WP_ADMIN_USER=$(WP_ADMIN_USER) -e WP_ADMIN_PASSWORD=$(WP_ADMIN_PASSWORD) -e WP_ADMIN_EMAIL=$(WP_ADMIN_EMAIL) \
		wpcli sh /scripts/install.sh

## seed	:	Create bilingual placeholder content with generated images.
.PHONY: seed
seed:
	$(WPCLI) eval-file /scripts/seed.php

## unseed	:	Delete all placeholder content (both languages).
.PHONY: unseed
unseed:
	$(WPCLI) eval-file /scripts/unseed.php

# WP-CLI as the host user, so files written into ./theme stay editable.
I18N = docker compose run --rm -T -u $$(id -u):$$(id -g) -e HOME=/tmp wpcli wp i18n
LANG_DIR = wp-content/themes/glitter/languages

## pot	:	Regenerate theme/languages/glitter.pot from the theme's strings.
.PHONY: pot
pot:
	$(I18N) make-pot wp-content/themes/glitter $(LANG_DIR)/glitter.pot --domain=glitter --exclude=assets

## translations	:	Compile theme/languages/el.po into el.mo + el.l10n.php.
.PHONY: translations
translations:
	$(I18N) make-mo $(LANG_DIR)/el.po $(LANG_DIR)
	$(I18N) make-php $(LANG_DIR)/el.po $(LANG_DIR)

## smoke	:	Check key pages return 200 and PHP logs no errors.
.PHONY: smoke
smoke:
	sh scripts/smoke.sh

## lint	:	PHPCS (WordPress standards), ESLint and Stylelint.
.PHONY: lint
lint:
	docker run --rm -u $$(id -u):$$(id -g) -v "$$PWD":/app -w /app composer:2 vendor/bin/phpcs
	npm run -s lint

## wp	:	Run WP-CLI, e.g. `make wp -- plugin list`
.PHONY: wp
wp:
	$(WPCLI) $(filter-out $@,$(MAKECMDGOALS))

%:
	@:
