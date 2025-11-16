# Note Pet PHP

[![PHP 8.2](https://img.shields.io/badge/php-8.2-%23777BB4?style=for-the-badge&logo=php&logoColor=black">)](https://www.php.net/releases/8.2/ru.php)
[![Symfony 7.3](https://img.shields.io/badge/symfony-7.3-%23000000.svg?style=for-the-badge&logo=symfony&logoColor=white)](https://symfony.com/releases/7.3)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15.3-41b883?style=for-the-badge&logo=postgresql&logoColor=black)](https://www.postgresql.org/docs/release/15.3/)

Пет-проект по созданию заметок пользователей.

# Запуск

1. Скопировать `.env.example` в `.env`
2. Выполнить `make up`

P.S. Запуск проекта успешно завершен если в логах контейнера увидите записи типа:

```
2025-11-16 04:21:46,639 INFO success: cron entered RUNNING state, process has stayed up for > than 1 seconds (startsecs)
2025-11-16 04:21:46,639 INFO success: nginx entered RUNNING state, process has stayed up for > than 1 seconds (startsecs)
2025-11-16 04:21:46,639 INFO success: php entered RUNNING state, process has stayed up for > than 1 seconds (startsecs)
```

# Контроллеры

Запросы, которые помечены символом "🔓" требуют авторизации, для этого надо выполнить следующие шаги:

1. Выполнить запрос `http://localhost/api/v1/users` методом **POST**
    * Передать поле: "email"
    * Передать поле: "password"

2. Выполнить запрос `http://localhost/api/login_check` методом **POST**
    * Передать поле: "username"
    * Передать поле: "password"

3. Полученный токен передавать в виде заголовка во всех запросах требующих авторизации:
    * Заголовок токена: `Authorization: Bearer ТОКЕН`

---

+ LoginController
    + POST: `http://localhost/api/login_check`

+ UserController
    + POST: `http://localhost/api/v1/users`
    + 🔓 GET: `http://localhost/api/v1/users/:id`
    + 🔓 PUT: `http://localhost/api/v1/users/:id`
    + 🔓 DELETE: `http://localhost/api/v1/users/:id`

+ NoteController
    + 🔓 GET: `http://localhost/api/v1/notes`
    + 🔓 POST: `http://localhost/api/v1/notes`
    + 🔓 GET: `http://localhost/api/v1/notes/:id`
    + 🔓 PUT: `http://localhost/api/v1/notes/:id`
    + 🔓 DELETE: `http://localhost/api/v1/notes/:id`

# Тесты

1. Инициализировать БД для тестов: `test-init`
2. Запустить тесты: `test-run`

# Команды

1. Команда для удаления заметок, которые обновлялись больше чем 30 дней назад: `docker compose exec -it application php bin/console cron:notes-delete`
    * Может запускаться по крону (каждый день в полночь) при запуске сервиса **scheduler**: `make schedule-run`
