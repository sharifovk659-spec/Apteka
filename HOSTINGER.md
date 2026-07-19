# Hostinger — apteka.inovaauto.com

Production сайт: **https://apteka.inovaauto.com**  
Server path: `/home/u417315406/domains/inovaauto.com/public_html/apteka`

> **Vercel:** Laravel + MySQL дар Vercel кор намекунад. Автодеплой = **GitHub Actions → Hostinger** (`.github/workflows/deploy-hostinger.yml`).

---

## 1. hPanel: subdomain

1. Domains → Subdomains → `apteka` барои `inovaauto.com`
2. Folder: `public_html/apteka` (ё Document Root → `public_html/apteka/public` — **беҳтар**)
3. SSL (Let's Encrypt) фаъол кунед

Агар Document Root = `public_html/apteka` бошад, файли реша `.htaccess` + `index.php` дархостҳоро ба `/public` мефиристанд.

---

## 2. MySQL (hPanel)

1. Databases → MySQL → Create database + user
2. User-ро ба database bind кунед
3. Credentials-ро дар `.env` (server) гузоред:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://apteka.inovaauto.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u417315406_XXXX
DB_USERNAME=u417315406_XXXX
DB_PASSWORD=********
```

Нусхаи намуна: `.env.production.example`

---

## 3. Якумин насби дастӣ (як бор)

Аз SSH (Hostinger) ё Terminal дар File Manager:

```bash
cd ~/domains/inovaauto.com/public_html/apteka

# .env-ро аз .env.production.example созед ва credentials-ро пур кунед
cp .env.production.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force   # танҳо бори аввал (маълумоти тест)
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Admin (баъди seed):
- URL: https://apteka.inovaauto.com/admin/login
- Email: `admin@salomat.local`
- Password: `Admin12345!` — **ҳатман иваз кунед**

---

## 4. GitHub Secrets (барои auto-deploy)

Repository → Settings → Secrets and variables → Actions:

| Secret | Мисол |
|--------|--------|
| `FTP_SERVER` | `ftp.YOUR_HOST.hostinger.com` ё IP |
| `FTP_USERNAME` | FTP user аз hPanel |
| `FTP_PASSWORD` | FTP password |
| `FTP_SERVER_DIR` | `/domains/inovaauto.com/public_html/apteka/` |

Опционалӣ (SSH барои `artisan migrate` баъди deploy):

| Secret | Мисол |
|--------|--------|
| `SSH_HOST` | IP ё hostname |
| `SSH_USERNAME` | u417315406 |
| `SSH_PASSWORD` | SSH password |
| `SSH_PORT` | `22` |
| `SSH_APP_PATH` | `/home/u417315406/domains/inovaauto.com/public_html/apteka` |

`.env`-ро FTP **тоза намекунад** — онро як бор дар server гузоред ва нигоҳ доред.

---

## 5. Автодеплой

1. Кодро ба GitHub push кунед (`main` ё `master`)
2. Workflow `Deploy to Hostinger` иҷро мешавад:
   - `composer install --no-dev`
   - `npm ci && npm run build`
   - FTP upload
   - (агар SSH бошад) migrate + cache

---

## 4. Муҳим

- DocumentRoot → `/public` (ё fallback `.htaccess` дар реша)
- `APP_DEBUG=false`
- `storage` ва `bootstrap/cache` writable (775)
- `php artisan storage:link`
- HTTPS + SSL дар hPanel

---

## 6. Санҷиш баъди deploy

- https://apteka.inovaauto.com/
- https://apteka.inovaauto.com/catalog
- https://apteka.inovaauto.com/admin/login
- Захираи мол / gallery / заказ

---

## 7. Локалӣ (XAMPP)

```
http://localhost/salomat/public
```

`.env` локалӣ: `APP_URL=http://localhost/salomat/public`, `DB_PORT=3308`
