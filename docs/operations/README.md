# Operations — Shirobell

Setup server yang wajib agar aplikasi berjalan benar. Mode: manual-setup (owner menjalankan di server).
Semua nilai rahasia ada di `.env` server (lihat `prod-docs/`), tidak pernah di file ini.

| Komponen | Kenapa | Dokumen |
|----------|--------|---------|
| Deploy Docker (dipakai) | Production (`main`, shirobell.my.id) dan staging (`dev`, dev.shirobell.my.id) di VPS Docker; deploy otomatis via GitHub Actions | [docker-deploy.md](./docker-deploy.md) |
| Deploy VPS manual (alternatif) | Nginx dan PHP-FPM langsung di host | [deploy.md](./deploy.md) |
| Queue worker | Email (konfirmasi bayar, pengingat, berakhir) dikirim lewat queue `database` | [supervisor/queue-worker.md](./supervisor/queue-worker.md) |
| Scheduler (cron) | Expire undangan & order tiap jam, pengingat H-7 harian | [cronjobs/scheduler.md](./cronjobs/scheduler.md) |
| Notifikasi Midtrans | Aktivasi undangan otomatis setelah bayar | [midtrans-notification.md](./midtrans-notification.md) |
| Storage link | Foto/musik/QRIS di `storage/app/public` disajikan dari `/storage` | `php artisan storage:link` sekali setelah deploy pertama |
| Wildcard subdomain | Undangan dibuka di `{slug}.APP_DOMAIN` | DNS `*.APP_DOMAIN` + SSL wildcard (ADR 003); lihat [deploy.md](./deploy.md) |
| Health check | Monitoring uptime | `GET /health` (200 ok, 503 degraded) |
| Aset panel admin | CSS/JS Filament di `public/{css,js,fonts}/filament` tidak masuk git | Otomatis lewat `composer install` (`filament:upgrade`); manual: `php artisan filament:assets` |
| Akun admin | Panel `/admin` hanya untuk role admin | `ADMIN_EMAIL`/`ADMIN_PASSWORD` di `.env`, lalu `php artisan db:seed --class=AdminSeeder` |
