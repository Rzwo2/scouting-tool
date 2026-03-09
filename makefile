include .env.local
export

build.dev:
	sudo docker compose --env-file .env --env-file .env.local build --pull --no-cache

build.prod:
	sudo docker compose --env-file .env --env-file .env.local -f compose.yaml -f compose.prod.yaml build --pull --no-cache

up.dev:
	docker compose --env-file .env --env-file .env.local up --wait

up.prod:
	docker compose --env-file .env --env-file .env.local -f compose.yaml -f compose.prod.yaml up --wait

down:
	docker compose down --remove-orphans

logs.docker.php:
	docker compose logs php

logs.docker.database:
	docker compose logs database

bash.php:
	docker compose exec php bash

bash.database:
	docker compose exec $(MARIADB_HOST) mariadb --ssl=off -D $(MARIADB_DATABASE) -u $(MARIADB_USER) -p$(MARIADB_PASSWORD)
