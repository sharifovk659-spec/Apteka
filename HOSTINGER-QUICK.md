# Ёрии тез — Hostinger (бе Vercel)

## ❌ Vercel-ро Import накунед

Laravel + MySQL дар Vercel кор намекунад.  
Сайт бояд дар **Hostinger** бошад: `https://apteka.inovaauto.com`

GitHub: https://github.com/sharifovk659-spec/Apteka

---

## 1. Subdomain (hPanel)

- Subdomain: `apteka`
- Folder: `public_html/apteka`
- Document Root беҳтар: `public_html/apteka/public`
- SSL фаъол

---

## 2. `.env` дар сервер

Дар компютер файл ҳаст: `.env.hostinger` (бо базаи шумо).

1. File Manager → `public_html/apteka/`
2. Файлро бор кунед ҳамчун ном: **`.env`**
3. Мундариҷа аз `.env.hostinger` (APP_KEY ва DB аллакай ҳаст)

База:
- DB: `u417315406_Apteka`
- User: `u417315406_adsererr_15441`
- Host: `localhost`

---

## 3. Код дар сервер

### Варианти A — File Manager / Git Deploy Hostinger

Агар Hostinger Git дошта бошад:
- Repo: `https://github.com/sharifovk659-spec/Apteka.git`
- Branch: `main`
- Path: `public_html/apteka`

Баъд SSH:

```bash
cd ~/domains/inovaauto.com/public_html/apteka
composer install --no-dev --optimize-autoloader
npm ci && npm run build
# ё public/build-ро аз CI бор кунед
php artisan key:generate --force   # танҳо агар APP_KEY холӣ бошад
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache
```

### Варианти B — GitHub Actions + FTP

Secrets:
- `FTP_SERVER`
- `FTP_USERNAME`
- `FTP_PASSWORD`
- `FTP_SERVER_DIR` = `/domains/inovaauto.com/public_html/apteka/`

---

## 4. Санҷиш

- https://apteka.inovaauto.com/
- https://apteka.inovaauto.com/admin/login  
  Email: `admin@salomat.local`  
  Password: `Admin12345!`

---

## Локалӣ

http://localhost/salomat/public
