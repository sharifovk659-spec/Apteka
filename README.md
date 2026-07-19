# Salomat — интернет-аптека

Laravel 12 + Blade + MySQL + Vite. Публичный каталог, корзина, оформление заказов и админ-панель.

## Требования

- PHP 8.2+
- Composer 2
- Node.js 20+ и npm
- MySQL 8 / MariaDB 10.6+
- Apache или Nginx (DocumentRoot → `public/`)

Расширения PHP: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` (рекомендуется для WebP).

## Установка

### 1. Клонирование

```bash
git clone <repository-url> salomat
cd salomat
```

### 2. Зависимости PHP

```bash
composer install
```

### 3. Окружение

```bash
cp .env.example .env
php artisan key:generate
```

Отредактируйте `.env`: укажите параметры MySQL (`DB_*`) и `APP_URL`.

**Не коммитьте `.env` в Git.** Файл содержит секреты (`APP_KEY`, пароль БД).

### 4. База данных

Создайте базу MySQL, затем:

```bash
php artisan migrate --seed
```

После seed создаётся администратор:

| Поле | Значение |
|------|----------|
| Email | `admin@salomat.local` |
| Пароль | `Admin12345!` |

В production смените пароль при первом входе (`must_change_password`).

### 5. Storage

```bash
php artisan storage:link
```

### 6. Frontend

```bash
npm install
npm run build
```

### 7. Apache (локально, XAMPP)

1. Junction или копия проекта: `C:\xampp\htdocs\salomat`
2. VirtualHost с `DocumentRoot` → `C:/xampp/htdocs/salomat/public`
3. В `hosts`: `127.0.0.1 salomat.local`
4. `mod_rewrite` включён, `AllowOverride All`

Альтернатива: `http://localhost/salomat/public` (редирект на `http://salomat.local/`).

### 8. Запуск для разработки

```bash
php artisan serve
npm run dev
```

## Админ-панель

- URL: `http://salomat.local/admin/login`
- Логин: `admin@salomat.local`
- Пароль: `Admin12345!` (только для dev/seed)

## Полезные команды

```bash
php artisan optimize:clear
php artisan salomat:generate-images
php artisan salomat:verify
```

## Безопасность

В репозиторий **не добавляются**:

- `.env`, пароли, `APP_KEY`
- дампы БД с персональными данными
- загруженные изображения клиентов (`storage/app/public/products/` и т.п.)

## Деплой

- Production: **https://apteka.inovaauto.com** (Hostinger)
- Инструкция: [HOSTINGER.md](HOSTINGER.md)
- Auto-deploy: GitHub Actions → FTP Hostinger (`.github/workflows/deploy-hostinger.yml`)
- **Vercel:** Laravel + MySQL дастгирӣ намешавад; `vercel.json` деплои Vercel-ро қасдан қатъ мекунад

См. также [DEPLOYMENT.md](DEPLOYMENT.md).

## Лицензия

MIT
