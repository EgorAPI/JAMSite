# Рекламное агентство «Джем» — V1.0 scaffold

Начальный каркас проекта.

## Требования

- PHP 8.1+
- Apache с `mod_rewrite` либо эквивалентные rewrite rules на другом сервере
- DocumentRoot должен указывать на `public/`

## Локальный запуск

Для быстрого smoke-test без Apache rewrite:

```bash
php -S 127.0.0.1:8080 -t public public/index.php
```

Для production используется web-root `public/` и правила из `public/.htaccess`.

Архитектура описана в `ARCHITECTURE.md`.
