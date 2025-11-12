include .env

up: ## Запустить
	docker compose up -d --build --remove-orphans

stop: ## Остановить
	docker compose stop

down: ## Удалить
	docker compose down -v

stop-up: ## Остановить и запустить
	make stop && make up

down-up: ## Удалить и запустить
	make down && make up

cache-clear:
	make cache-clear-insert
	docker compose exec -it application chmod -R 777 var

cache-clear-insert:
	docker compose exec -it application rm -rf var
	docker compose exec -it application php bin/console cache:clear
    docker compose exec -it application php bin/console cache:warmup

.PHONY: test-init
