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



migrations-create: ## Создать файл миграции
	docker compose exec -it application php bin/console make:migration -n

migrations-up: ## Накатить миграции
	docker compose exec -it application php bin/console doctrine:migrations:migrate -n

migrations-down: ## Откатить миграции
	docker compose exec -it application php bin/console doctrine:migrations:migrate 'prev' -n

migrations-create-up: ## Создать файл миграций и накатить его
	make cache-clear && make migrations-create && make migrations-up



schedule-run:
	docker compose exec -it application php bin/console messenger:consume -vv

schedule-stop:
	docker compose exec -it application php bin/console messenger:stop-workers

schedule-debug:
	docker compose exec -it application php bin/console debug:schedule



test-run: ## Запустить тесты
	docker compose exec -it application php vendor/bin/codecept run

test-run-functional-test: ## Запустить определенный функциональный тест
	docker compose exec -it application php vendor/bin/codecept run tests/Functional/ServersCest:tryToTest

test-init: ## Инициализация тестовой базы
	docker compose exec -it application php bin/console doctrine:database:drop --if-exists --force --env=test || true
	docker compose exec -it application php bin/console doctrine:database:create --env=test
	docker compose exec -it application php bin/console doctrine:migrations:migrate -n --env=test



cache-clear:
	make cache-clear-insert
	docker compose exec -it application chmod -R 777 var

cache-clear-insert:
	docker compose exec -it application rm -rf var
	docker compose exec -it application php bin/console cache:clear
    docker compose exec -it application php bin/console cache:warmup



phpcs:
	docker compose exec -it application php vendor/bin/php-cs-fixer fix --allow-risky=yes

phpstan:
	docker compose exec -it application php vendor/bin/phpstan analyse src

.PHONY: test-init
