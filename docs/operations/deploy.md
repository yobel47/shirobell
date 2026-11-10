# Deploy — Shirobell di VPS (Ubuntu + Nginx)

## Apa

Langkah memasang Shirobell di satu VPS: domain utama (`APP_DOMAIN`) dan semua subdomain undangan (`*.APP_DOMAIN`) dilayani aplikasi Laravel yang sama.

## Kenapa

Undangan dibuka di subdomain (ADR 003). Server harus menerima wildcard DNS dan wildcard SSL, lalu meneruskan semua host ke `apps/public`. Laravel yang memilah domain utama dan subdomain.

## Prerequisites

| Komponen | Versi / catatan |
|----------|-----------------|
| OS | Ubuntu 24.04 LTS (atau setara) |
| PHP | 8.3 dengan ekstensi: `bcmath`, `ctype`, `curl`, `fileinfo`, `gd` (dengan WebP), `intl`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip` |
| Database | MySQL 8.x, database `utf8mb4_unicode_ci`, user khusus aplikasi dengan hak hanya ke database tersebut |
| Web server | Nginx + PHP-FPM |
| Lainnya | Composer 2, Node.js 20+ (untuk build aset), Supervisor, Certbot |
| Domain | Akses mengatur DNS domain |
| Akun pihak ketiga | SMTP untuk email, Midtrans (production setelah aktivasi bisnis) |

Nilai rahasia (password DB, SMTP, server key Midtrans, IP server) disimpan di `.env` server dan `prod-docs/`, tidak di repo.

## 1. DNS

| Type | Name | Value |
|------|------|-------|
| A | `@` | IP VPS |
| A | `*` | IP VPS |

Verifikasi: `dig +short APP_DOMAIN` dan `dig +short test.APP_DOMAIN` mengembalikan IP yang sama.

## 2. SSL wildcard

Sertifikat harus mencakup `APP_DOMAIN` dan `*.APP_DOMAIN`. Wildcard Let's Encrypt wajib memakai DNS challenge.

Opsi A, DNS di Cloudflare (disarankan):
```bash
sudo apt install certbot python3-certbot-dns-cloudflare
# simpan API token Cloudflare (Zone:DNS:Edit) di /root/.secrets/cloudflare.ini, chmod 600
sudo certbot certonly --dns-cloudflare --dns-cloudflare-credentials /root/.secrets/cloudflare.ini \
  -d APP_DOMAIN -d '*.APP_DOMAIN'
```
Perpanjangan otomatis lewat timer `certbot.timer` (cek: `systemctl list-timers | grep certbot`).

Opsi B, proxy Cloudflare (oranye) dengan Origin Certificate wildcard: pasang sertifikat origin di Nginx, SSL mode "Full (strict)". Jika memakai proxy, atur `TrustProxies` (lihat bagian Troubleshooting) agar IP tamu (rate limit) dan HTTPS terdeteksi benar.

## 3. Kode dan dependensi

```bash
sudo mkdir -p /var/www/shirobell && sudo chown $USER:www-data /var/www/shirobell
cd /var/www/shirobell
git clone <URL_REPO> apps && cd apps
git checkout main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
cp .env.example .env && php artisan key:generate
```

## 4. `.env` production (checklist)

| Key | Nilai |
|-----|-------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://APP_DOMAIN` |
| `APP_DOMAIN` | domain utama tanpa skema, mis. `shirobell.com` |
| `APP_TIMEZONE` | `Asia/Jakarta` |
| `DB_*` | koneksi MySQL production |
| `SESSION_SECURE_COOKIE` | `true` |
| `SESSION_DOMAIN` | kosong (cookie hanya untuk domain utama; subdomain punya session sendiri) |
| `QUEUE_CONNECTION` | `database` |
| `MAIL_*` | SMTP production, `MAIL_FROM_ADDRESS` di domain sendiri |
| `MIDTRANS_SERVER_KEY` | server key environment yang dipakai |
| `MIDTRANS_IS_PRODUCTION` | `true` untuk production, `false` untuk staging |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD` | akun admin awal (hapus `ADMIN_PASSWORD` dari `.env` setelah seeder dijalankan) |
| `SECURITY_CSP_ENABLED` | `true` |

## 5. Database, storage, cache

```bash
php artisan migrate --force
php artisan db:seed --force          # settings, tema, admin awal (aman diulang)
php artisan storage:link
php artisan filament:assets
php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

## 6. PHP-FPM

Di `/etc/php/8.3/fpm/php.ini` (batas upload aplikasi: foto 8 MB, MP3 10 MB, file sementara Livewire 12 MB):
```ini
upload_max_filesize = 16M
post_max_size = 20M
memory_limit = 256M
expose_php = Off
```
`sudo systemctl restart php8.3-fpm`

## 7. Nginx

`/etc/nginx/sites-available/shirobell`:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name APP_DOMAIN *.APP_DOMAIN;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    listen [::]:443 ssl;
    http2 on;
    server_name APP_DOMAIN *.APP_DOMAIN;

    ssl_certificate     /etc/letsencrypt/live/APP_DOMAIN/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/APP_DOMAIN/privkey.pem;

    root /var/www/shirobell/apps/public;
    index index.php;
    charset utf-8;
    client_max_body_size 20M;
    server_tokens off;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~* \.(?:css|js|woff2?|svg|png|jpe?g|webp|mp3)$ {
        expires 30d;
        access_log off;
        try_files $uri /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
```bash
sudo ln -s /etc/nginx/sites-available/shirobell /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

## 8. Background process dan integrasi

1. Queue worker: [supervisor/queue-worker.md](./supervisor/queue-worker.md)
2. Scheduler: [cronjobs/scheduler.md](./cronjobs/scheduler.md)
3. Notification URL Midtrans: [midtrans-notification.md](./midtrans-notification.md)

## 9. Backup

Minimal harian, simpan di luar VPS (object storage) dan terenkripsi:
- Database: `mysqldump --single-transaction` lalu enkripsi (mis. `gpg --symmetric --cipher-algo AES256`).
- File unggahan: `storage/app/public/` (foto, QRIS, MP3).
- Uji restore minimal sekali sebulan.

## Verifikasi

| Cek | Hasil yang diharapkan |
|-----|----------------------|
| `curl -I http://APP_DOMAIN` | 301 ke https |
| `curl https://APP_DOMAIN/health` | `{"status":"ok","database":"ok"}` |
| `curl -I https://APP_DOMAIN` | ada `Strict-Transport-Security`, `Content-Security-Policy`, `X-Frame-Options` |
| `https://acak.APP_DOMAIN` | halaman "Undangan tidak ditemukan" (404) |
| `https://acak.APP_DOMAIN/login` | 404 (route utama tidak tersedia di subdomain) |
| `https://APP_DOMAIN/admin` | halaman login admin |
| `https://APP_DOMAIN/tema/rose-garden` | demo tema tampil |
| Daftar akun, verifikasi email | email masuk (queue worker berjalan) |
| Checkout + bayar (sandbox di staging) | order lunas, undangan aktif |

## Update (deploy berikutnya)

```bash
cd /var/www/shirobell/apps
php artisan down --render="errors::503"
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan filament:assets
php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache
php artisan queue:restart
php artisan up
```

Rollback: `git checkout <commit_sebelumnya>` lalu ulangi langkah di atas tanpa `migrate` (atau `php artisan migrate:rollback --step=N` jika migrasi baru bermasalah, setelah backup).

## Troubleshooting

| Gejala | Penyebab | Solusi |
|--------|----------|--------|
| Subdomain menampilkan halaman Nginx default | `server_name` tidak memuat `*.APP_DOMAIN` | Perbaiki blok server, `nginx -t`, reload |
| Sertifikat tidak valid di subdomain | Sertifikat bukan wildcard | Terbitkan ulang dengan `-d '*.APP_DOMAIN'` |
| Semua tamu kena rate limit bersamaan / redirect loop HTTPS | Di belakang proxy (Cloudflare) tanpa trusted proxies | Di `bootstrap/app.php`: `$middleware->trustProxies(at: '*');` hanya jika server hanya bisa diakses lewat proxy |
| Upload gagal "413" | `client_max_body_size` / `post_max_size` kecil | Sesuaikan bagian 6 dan 7 |
| Panel admin tanpa gaya | Aset Filament belum dipublikasi | `php artisan filament:assets` |
| Gambar tidak tampil | `storage:link` belum dibuat | `php artisan storage:link` |

## Maintenance

- Perbarui dependensi berkala: `composer audit`, `npm audit`, lalu deploy.
- Pantau `storage/logs/laravel.log` dan `GET /health` (uptime monitor).
- Rotasi log: `logrotate` untuk `storage/logs/*.log` (harian, simpan 14 hari).
