# Supervisor — Queue Worker

## Apa

Proses `php artisan queue:work` yang mengirim email antrean: `PaymentSucceeded`, `InvitationExpiringSoon`, `InvitationExpired`.

## Kenapa

Mailable memakai `ShouldQueue` dan `QUEUE_CONNECTION=database`. Tanpa worker, email tersimpan di tabel `jobs` dan tidak pernah terkirim. Pembayaran dan aktivasi undangan tetap berjalan tanpa worker; hanya email yang tertunda.

## Prerequisites

- Supervisor terpasang (`sudo apt-get install supervisor`).
- `.env` server berisi `QUEUE_CONNECTION=database` dan konfigurasi `MAIL_*` yang valid.
- Migrasi sudah dijalankan (tabel `jobs`, `failed_jobs`).

## Setup

File `/etc/supervisor/conf.d/shirobell-queue.conf` (sesuaikan path dan user dengan server):

```ini
[program:shirobell-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/shirobell/apps/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/shirobell/apps/storage/logs/queue.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start shirobell-queue:*
```

## Verifikasi

```bash
sudo supervisorctl status shirobell-queue:*   # RUNNING
php artisan queue:failed                      # idealnya kosong
```

## Development lokal

Jalankan `php artisan queue:work` di terminal terpisah, atau set `QUEUE_CONNECTION=sync` di `.env` lokal agar email langsung diproses. Dengan `MAIL_MAILER=log`, isi email tertulis di `storage/logs/laravel.log`.

## Troubleshooting

| Gejala | Penyebab | Solusi |
|--------|----------|--------|
| Email tidak terkirim, tabel `jobs` bertambah | Worker mati | `sudo supervisorctl restart shirobell-queue:*` |
| Job masuk `failed_jobs` | SMTP salah / timeout | Perbaiki `MAIL_*`, lalu `php artisan queue:retry all` |
| Perubahan kode tidak berlaku | Worker memakai kode lama | `php artisan queue:restart` setelah deploy |

## Maintenance

Setiap deploy: `php artisan queue:restart` (worker berhenti setelah job berjalan selesai, Supervisor menyalakan ulang).
