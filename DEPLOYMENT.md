# Деплой Salomat (Laravel + MySQL)

Production: **https://apteka.inovaauto.com**  
Hostinger path: `/home/u417315406/domains/inovaauto.com/public_html/apteka`

Полная инструкция Hostinger + GitHub Actions: **[HOSTINGER.md](HOSTINGER.md)**

> **Vercel:** не переносите Laravel backend на Vercel. Auto-deploy = GitHub Actions → Hostinger.

---

## 1. Требования сервера

| Компонент | Минимум |
|-----------|---------|
| PHP | 8.2+ |
| MySQL | 8.0+ / MariaDB 10.6+ |
| Composer | 2.x (в CI) |
| Node.js | 20+ (в CI для build) |
| Web-сервер | Apache (Hostinger) |

### PHP extensions

`bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `gd`, `zip`

---

## 2. Environment (production)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://apteka.inovaauto.com
DB_HOST=localhost
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

См. `.env.production.example`

---

## 3. DocumentRoot

Рекомендуется: `.../public_html/apteka/public`

Если Document Root = `.../public_html/apteka`, работают корневые `.htaccess` + `index.php`.

---

## 4. Deploy команды (CI / SSH)

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R ug+rwx storage bootstrap/cache
```

---

## 5. HTTPS

SSL в hPanel (Let's Encrypt). В production Laravel принуждает `https://` (AppServiceProvider).

---

## 6. Backup

```bash
mysqldump -u USER -p DATABASE > backup-$(date +%F).sql
```

Копируйте также `storage/app/public/`.
