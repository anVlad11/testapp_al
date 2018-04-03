# Тестовое приложение

## Окружение для запуска
* Nginx/Apache
* PHP 7.1
* PostgreSQL 9.5

## Дополнительно

Токен для чтения задач пользователя А: `8040c6830d2e14af191feef7eaf`

Токен для записи задач пользователя А: `akpsdkaosdpoasdkopsadk`

Настройки соединения с базой находятся в [конфиге](module/Application/config/module.config.php) модуля Application

Настройки по-умолчанию:

```php
'params'      => [
                    'host'     => 'localhost',
                    'port'     => '5432',
                    'user'     => 'taskman',
                    'password' => 'taskman',
                    'dbname'   => 'taskman',
                ],
```

Провести миграцию [Version20180331164621](data/Migrations/Version20180331164621.php)

```bash
./vendor/bin/doctrine-module migrations:migrate 20180331164621
```

Загрузить пакеты из Composer

```bash
composer update
```

Тесты
```bash
./vendor/bin/phpunit --testsuite Application
```

Документация по API доступа на [Postman](https://documenter.getpostman.com/view/525400/taskmanager/RVtynBG2)

### Структура базы данных
#### taskman.users

Таблица содержит информацию о пользователях

Сущность - [User](module/Application/src/Entity/User.php)

Столбец | Описание
--------|---------
id | UUIDv4 идентификатор
login | Уникальное имя пользователя

#### taskman.tasks

Таблица содержит информацию о задачах

Сущность - [Task](module/Application/src/Entity/Task.php)

Столбец | Описание
--------|---------
id | UUIDv4 идентификатор
title | Заголовок задачи
description | Описание задачи
created_at | Момент создания задачи
user_id | Идентификатор владельца задачи (users.id)

#### taskman.tokens

Таблица содержит информацию о токенах доступа

Сущность - [Token](module/Application/src/Entity/Token.php)

Столбец | Описание
--------|---------
id | UUIDv4 идентификатор
token | Уникальный текстовый идентификатор
user_id | Идентификатор владельца токена (users.id)

#### taskman.permissions

Сущность - [Permission](module/Application/src/Entity/Permission.php)

Таблица содержит информацию о правах, назначенных токенам доступа

Столбец | Описание
--------|---------
id | UUIDv4 идентификатор
permission | Право доступа
token_id | Идентификатор токена (tokens.id)