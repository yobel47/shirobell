# Cronjob — Laravel Scheduler

## Apa

Satu entri cron yang menjalankan `php artisan schedule:run` tiap menit. Jadwal didefinisikan di `routes/console.php`:

| Command | Jadwal | Fungsi |
|---------|--------|--------|
| `invitations:expire` | tiap jam | Undangan `active` dengan `active_until` lewat menjadi `expired` + email ke pemilik |
| `orders:expire` | tiap jam | Order `pending` lewat `expires_at` + 1 jam menjadi `expired` |
| `invitations:remind` | harian 09:00 (Asia/Jakarta) | Email pengingat sekali per periode untuk undangan yang berakhir dalam 7 hari |

## Kenapa

Tanpa scheduler, undangan yang masa aktifnya habis tetap tidak tampil ke tamu (dicek `active_until` saat request), tetapi statusnya tidak berubah, email tidak terkirim, dan order lama tetap `pending`.

## Prerequisites

- Akses crontab user yang menjalankan aplikasi (mis. `www-data`).
- Queue worker berjalan (email dikirim lewat queue).

## Setup

```bash
sudo crontab -u www-data -e
# tambahkan satu baris:
* * * * * cd /var/www/shirobell/apps && php artisan schedule:run >> /dev/null 2>&1
```

## Verifikasi

```bash
php artisan schedule:list          # tiga jadwal tampil dengan "Next Due"
php artisan invitations:expire     # bisa dijalankan manual, aman diulang
sudo crontab -u www-data -l
```

## Troubleshooting

| Gejala | Solusi |
|--------|--------|
| Undangan lewat masa aktif masih `active` | Cek crontab dan `storage/logs/laravel.log`; jalankan `php artisan invitations:expire` manual |
| Pengingat terkirim di jam salah | Pastikan `APP_TIMEZONE=Asia/Jakarta` |
| Command bertumpuk | Jadwal memakai `withoutOverlapping()`; hapus lock dengan `php artisan schedule:clear-cache` |

## Maintenance

Tidak perlu restart setelah deploy; cron membaca kode terbaru tiap menit.
