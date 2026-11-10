# Integrasi — Notifikasi Pembayaran Midtrans

## Apa

Endpoint `POST /webhooks/midtrans` menerima HTTP Notification dari Midtrans dan mengaktifkan undangan setelah pembayaran lunas (ADR 007).

## Kenapa

Aktivasi otomatis bergantung pada notifikasi ini. Tanpa notifikasi, customer masih bisa membuka halaman order atau menekan "Cek status pembayaran" (memanggil status API Midtrans), tetapi tidak otomatis.

## Prerequisites

- Akun Midtrans. Sandbox untuk staging, Production setelah aktivasi bisnis.
- `.env` server: `MIDTRANS_SERVER_KEY` (sesuai environment) dan `MIDTRANS_IS_PRODUCTION` (`false` sandbox, `true` production).
- Aplikasi bisa diakses publik lewat HTTPS.

## Setup

1. Dashboard Midtrans, pilih environment yang sesuai, buka **Settings > Configuration** (Payment).
2. **Payment Notification URL**: `https://APP_DOMAIN/webhooks/midtrans`
3. **Finish / Unfinish / Error Redirect URL** boleh dikosongkan: aplikasi mengirim `callbacks.finish` per transaksi ke halaman order.
4. Simpan, lalu `php artisan config:cache` setelah mengubah `.env`.

## Keamanan

- Endpoint dikecualikan dari CSRF (`bootstrap/app.php`), dibatasi `throttle:120,1`.
- `signature_key = SHA512(order_id + status_code + gross_amount + ServerKey)` diverifikasi; salah = 403.
- Status final selalu diambil ulang dari `GET /v2/{order_id}/status`; nominal dicocokkan dengan order.
- Pembayaran kartu dengan `fraud_status` selain `accept` tidak mengaktifkan undangan.
- Semua notifikasi tercatat di tabel `payment_logs` (termasuk yang ditolak).

## Verifikasi

1. Buat undangan, checkout, bayar memakai simulator sandbox Midtrans.
2. Tabel `payment_logs` berisi baris `settlement` dengan `is_processed = 1`.
3. Order menjadi `paid`, undangan `active`, email konfirmasi masuk antrean.

Development lokal: Midtrans tidak bisa menjangkau `localhost`. Setelah membayar di simulator, kembali ke halaman order (otomatis cek status) atau tekan "Cek status pembayaran". Alternatif: tunnel sementara (mis. ngrok) sebagai Notification URL sandbox.

## Troubleshooting

| Gejala | Solusi |
|--------|--------|
| `payment_logs.error = invalid signature` | Server key di `.env` tidak sesuai environment (sandbox vs production) |
| `amount mismatch` | Cek manual di dashboard Midtrans; aktivasi manual oleh admin (M5) |
| Midtrans melaporkan notifikasi gagal (5xx) | Cek `storage/logs/laravel.log`; Midtrans mengirim ulang otomatis |

## Maintenance

Saat pindah ke production: ganti `MIDTRANS_SERVER_KEY` ke key production, set `MIDTRANS_IS_PRODUCTION=true`, perbarui Notification URL di dashboard production, lalu `php artisan config:cache`.
