# Deploy dengan Docker (VPS yang sudah memakai Docker)

## Apa

Menjalankan Shirobell di VPS yang sudah punya Docker, reverse proxy, dan MySQL. Image dibangun oleh GitHub Actions dan disimpan di GitHub Container Registry (GHCR). VPS menarik image itu setiap kali ada perubahan di branch `main`.

## Kenapa

- Tidak bentrok dengan layanan lain di VPS.
- Deploy otomatis setelah test lulus.
- Rollback cukup dengan menjalankan image versi sebelumnya.

## Arsitektur

```
Tamu -> Cloudflare (HTTPS, *.shirobell.my.id) -> reverse proxy (container yang sudah ada)
     -> shirobell-web (Nginx) -> shirobell-app (PHP-FPM) -> MySQL yang sudah ada
                                  shirobell-worker    (antrean email)
                                  shirobell-scheduler (kedaluwarsa, pengingat)
```

| Container | Isi |
|-----------|-----|
| `shirobell-web` | Nginx, file publik, file unggahan (volume `shirobell-storage`, hanya baca) |
| `shirobell-app` | Aplikasi Laravel (PHP-FPM) |
| `shirobell-worker` | `php artisan queue:work` |
| `shirobell-scheduler` | `php artisan schedule:work` |

File di repo: `Dockerfile`, `docker-compose.prod.yml`, `docker/`, `.env.production.example`, `.github/workflows/deploy.yml`.

## Prerequisites

| Item | Keterangan |
|------|------------|
| VPS | Docker dan Docker Compose v2 (`docker compose version`) |
| Reverse proxy | Container yang memegang port 80/443 (Nginx Proxy Manager, Traefik, atau Caddy) |
| MySQL | Versi 8.x, berupa container atau terpasang di host |
| Domain | `shirobell.my.id` dengan DNS di Cloudflare |
| SMTP | Brevo (lihat bagian `.env`) |

## Langkah

### 1. Cloudflare

1. Buka **DNS**, lalu tambahkan dua record, keduanya **Proxied** (awan oranye):

   | Type | Name | Content |
   |------|------|---------|
   | A | `@` | IP VPS |
   | A | `*` | IP VPS |

2. Buka **SSL/TLS > Overview**, lalu pilih mode **Full (strict)**. Jangan pilih Flexible, karena akan menyebabkan redirect berulang.
3. Buka **SSL/TLS > Origin Server > Create Certificate**. Hostname: `shirobell.my.id` dan `*.shirobell.my.id`, masa berlaku 15 tahun. Simpan isi **Origin Certificate** dan **Private Key**. Private key hanya ditampilkan sekali.

### 2. Cari nama network

```bash
docker ps --format '{{.Names}}'                                         # nama container proxy dan MySQL
docker inspect <container-proxy> --format '{{json .NetworkSettings.Networks}}'
docker inspect <container-mysql> --format '{{json .NetworkSettings.Networks}}'
```

Catat nama network proxy sebagai `PROXY_NETWORK` dan network MySQL sebagai `DB_NETWORK`. Nama container MySQL dipakai sebagai `DB_HOST`.

Kalau MySQL terpasang di host (bukan container):
- `DB_HOST=host.docker.internal`;
- MySQL harus mendengarkan di IP bridge Docker;
- isi `DB_NETWORK` dengan network apa pun yang sudah ada, misalnya network proxy.

### 3. Database

```bash
docker exec -it <container-mysql> mysql -uroot -p
```

```sql
CREATE DATABASE shirobell CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'shirobell'@'%' IDENTIFIED BY 'GANTI-DENGAN-PASSWORD-KUAT';
GRANT ALL PRIVILEGES ON shirobell.* TO 'shirobell'@'%';
FLUSH PRIVILEGES;
```

Hak akses hanya untuk database `shirobell`.

### 4. Folder dan `.env` di VPS

```bash
sudo mkdir -p /opt/shirobell && sudo chown $USER /opt/shirobell
cd /opt/shirobell
nano .env      # salin isi .env.production.example dari repo, lalu isi
chmod 600 .env
```

Yang wajib diisi:

| Kunci | Isi |
|-------|-----|
| `APP_KEY` | Jalankan `php artisan key:generate --show` di laptop, lalu tempel hasilnya |
| `PROXY_NETWORK`, `DB_NETWORK`, `DB_HOST` | Dari langkah 2 |
| `DB_PASSWORD` | Dari langkah 3 |
| `MAIL_USERNAME`, `MAIL_PASSWORD` | Login dan kunci SMTP Brevo |
| `MIDTRANS_SERVER_KEY` | Server key **sandbox** dulu. Ganti ke production setelah disetujui, lalu set `MIDTRANS_IS_PRODUCTION=true` |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD` | Akun admin pertama (dibaca sekali oleh `AdminSeeder`) |

### 5. Kunci deploy SSH dan secret GitHub

Di laptop:

```bash
ssh-keygen -t ed25519 -C "shirobell-deploy" -f shirobell_deploy -N ""
```

1. Tambahkan isi `shirobell_deploy.pub` ke `~/.ssh/authorized_keys` milik user deploy di VPS. User ini harus anggota grup `docker`.
2. Di GitHub, buka **Settings > Secrets and variables > Actions > New repository secret**:

   | Secret | Isi |
   |--------|-----|
   | `VPS_HOST` | IP VPS |
   | `VPS_USER` | User deploy |
   | `VPS_SSH_KEY` | Isi file `shirobell_deploy` (private key) |
   | `DEPLOY_PATH` | `/opt/shirobell` |
   | `VPS_PORT` | Opsional, kalau SSH bukan port 22 |

3. Hapus file private key dari laptop setelah disimpan di GitHub.

Anggota grup `docker` setara dengan root. Pakai kunci deploy terpisah ini hanya untuk deploy.

### 6. Reverse proxy

Nginx Proxy Manager:
1. Buka **SSL Certificates > Add SSL Certificate > Custom**, lalu unggah Origin Certificate dan Private Key dari Cloudflare.
2. Buka **Hosts > Proxy Hosts > Add Proxy Host**:
   - Domain Names: `shirobell.my.id` dan `*.shirobell.my.id`;
   - Scheme `http`, Forward Hostname `shirobell-web`, Forward Port `80`;
   - tab SSL: pilih sertifikat tadi, lalu aktifkan **Force SSL** dan **HTTP/2**.
3. Container NPM harus berada di network `PROXY_NETWORK` yang sama dengan `shirobell-web`.

Traefik dan Caddy: arahkan `shirobell.my.id` dan `*.shirobell.my.id` ke `http://shirobell-web:80`, dengan sertifikat Origin Cloudflare. Teruskan header `X-Forwarded-For`, `X-Forwarded-Proto`, dan `Host` (default di ketiganya).

### 7. Deploy pertama

1. Owner merge `dev` ke `main` di GitHub. Workflow **CI & Deploy** akan menjalankan test, build, push image, lalu deploy.
2. Setelah job deploy hijau, jalankan seeder awal sekali saja (pengaturan default, tema, dan admin):

   ```bash
   cd /opt/shirobell
   docker compose -f docker-compose.prod.yml run --rm app php artisan db:seed --force
   ```

3. Hapus `ADMIN_PASSWORD` dari `.env` setelah berhasil login, lalu ganti sandi admin di panel.

Kalau image GHCR bersifat private dan deploy dijalankan manual, login dulu dengan token GitHub (scope `read:packages`): `docker login ghcr.io -u yobel47`.

### 8. Midtrans

Isi **Payment Notification URL** di dashboard Midtrans dengan `https://shirobell.my.id/webhooks/midtrans`. Detail ada di [midtrans-notification.md](./midtrans-notification.md).

## Verifikasi

| Cek | Hasil yang diharapkan |
|-----|----------------------|
| `docker compose -f docker-compose.prod.yml ps` | 4 container `running`, `shirobell-app` `healthy` |
| `https://shirobell.my.id/health` | `{"status":"ok","database":"ok"}` |
| `https://shirobell.my.id/tema` | Katalog 65 tema |
| `https://acak.shirobell.my.id` | Halaman "Undangan tidak ditemukan" |
| Daftar akun baru | Email verifikasi masuk |
| Checkout sandbox | Order lunas, undangan aktif |
| Hari-H di HP | Kamera terbuka untuk scan QR (butuh HTTPS) |
| Coba login salah 6 kali dari satu HP | Hanya HP itu yang terkunci (`TRUSTED_PROXIES=*` bekerja) |

## Update

Otomatis: setiap merge ke `main`. Workflow menjalankan `migrate --force`, `ThemeSeeder`, `up -d`, dan `queue:restart`.

Manual (misalnya setelah mengubah `.env`):

```bash
cd /opt/shirobell
docker compose -f docker-compose.prod.yml up -d --force-recreate
```

Container membangun ulang cache config saat start, jadi perubahan `.env` langsung terbaca.

## Rollback

```bash
cd /opt/shirobell
IMAGE_TAG=<sha-commit-sebelumnya> docker compose -f docker-compose.prod.yml up -d
```

Daftar tag ada di GitHub, menu **Packages > shirobell-app**. Kalau ada migrasi baru yang bermasalah, backup database dulu, lalu jalankan `php artisan migrate:rollback --step=N` lewat `run --rm app`.

## Backup

Minimal harian, simpan di luar VPS:

```bash
docker exec <container-mysql> mysqldump -ushirobell -p --single-transaction shirobell | gzip > shirobell-$(date +%F).sql.gz
docker run --rm -v shirobell-storage:/data -v "$PWD":/backup alpine tar czf /backup/storage-$(date +%F).tgz -C /data app/public
```

Enkripsi sebelum diunggah, misalnya dengan `gpg --symmetric`. Uji restore minimal sebulan sekali.

## Troubleshooting

| Gejala | Penyebab dan solusi |
|--------|---------------------|
| 502 Bad Gateway | Proxy tidak satu network dengan `shirobell-web`. Cek `PROXY_NETWORK` dan Forward Hostname |
| Redirect berulang | Cloudflare memakai mode Flexible. Ganti ke Full (strict) |
| 419 Page Expired setelah login | `SESSION_SECURE_COOKIE=true` tanpa HTTPS, atau `TRUSTED_PROXIES` kosong |
| Subdomain undangan 404 | `APP_DOMAIN` bukan `shirobell.my.id`, atau record `*` belum ada |
| `SQLSTATE[HY000] [2002]` | `DB_HOST` atau `DB_NETWORK` salah |
| Email tidak terkirim | Cek `docker logs shirobell-worker`, kredensial Brevo, dan verifikasi domain di Brevo |
| Foto tidak tampil | Volume `shirobell-storage` tidak ter-mount di `shirobell-web` |
