# Архитектура V1.0 — рекламное агентство «Джем»

## 1. Главный принцип

V1.0:

`HTTP → front controller → route → controller/service → repository → JSON`

V1.1:

`HTTP → front controller → route → controller/service → repository → DB`

Templates не читают JSON напрямую.

## 2. Web root

Единственный web-root: `public/`.

Вне web-root находятся PHP-логика, конфигурация, JSON, шаблоны и runtime-файлы.

## 3. Точки входа

- `public/index.php` — публичный сайт.
- `public/admin/index.php` — административная панель.

Статические CSS/JS/images/uploads отдаются веб-сервером напрямую.

## 4. Routing

Публичные маршруты определяются в `routes/web.php`.
Административные — в `routes/admin.php`.

Начальные публичные маршруты:

- `GET /`
- `GET /portfolio`
- `GET /contacts`
- `GET /privacy`
- `POST /contact-request`

Начальные admin-маршруты:

- `GET /admin/`
- `GET /admin/login`

Позже добавляются `/admin/portfolio`, `/admin/categories`, `/admin/slides` и POST-действия.

## 5. Layout и templates

- `templates/layouts/main.php` — общий layout публичной части.
- `templates/layouts/admin.php` — layout админки.
- `templates/partials/` — повторяемые части (`header`, `footer`, далее `navbar`, form components и т.д.).
- `templates/pages/` — страницы публичной части.
- `templates/admin/` — шаблоны административной части.

Template получает уже подготовленные данные и не содержит доступа к файловому хранилищу.

## 6. Application layer

- `app/Http/Controllers/` — принимает маршрут и собирает данные страницы.
- `app/Services/` — бизнес-операции: формы, upload, auth, image processing и т.д.
- `app/Repositories/Contracts/` — интерфейсы доступа к данным.
- `app/Repositories/Json/` — реализация V1.0 поверх JSON.
- `app/Support/` — небольшие инфраструктурные helpers.

При переходе на БД добавляется `app/Repositories/Database/`, а интерфейсы и верхние слои сохраняются.

## 7. Config

- `config/app.php` — приложение, пути, окружение, session.
- `config/mail.php` — SMTP.
- `config/security.php` — CSRF/upload/security limits.
- `.env` — реальные секреты, не коммитится.
- `.env.example` — безопасный шаблон.

## 8. Data

`data/` содержит только структурированные данные V1.0:

- `portfolio.json`
- `categories.json`
- `slides.json`
- `settings.json`

Запись должна выполняться только через repository/service слой с lock + temp file + atomic replace.

## 9. Uploads

Публичные изображения находятся в `public/uploads/`:

- `portfolio/`
- `slides/`
- `services/`

В upload-каталогах запрещается выполнение PHP/скриптов. Имена генерирует приложение. MIME проверяется сервером.

## 10. Runtime

`storage/`:

- `logs/`
- `cache/`
- `tmp/`

Каталог не публикуется веб-сервером.

## 11. Правило зависимостей

Допустимо:

`Controller → Service → Repository Interface → JSON Repository`

`Controller → Repository Interface` допустим для простого read-only сценария.

Недопустимо:

- `Template → JSON`
- `Template → Repository`
- `JS → /data/*.json`
- хранение секретов в `public/`
- загрузка файлов вне централизованного upload service.
