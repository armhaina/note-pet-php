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

.PHONY: test-init
