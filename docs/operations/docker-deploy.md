# Deploy dengan Docker: production dan staging

## Apa

Menjalankan Shirobell di VPS yang sudah punya Docker, Nginx (reverse proxy di container), dan MySQL. Ada dua lingkungan di VPS yang sama, terpisah total (container, database, file unggahan, `.env`):

| Branch | Lingkungan | Domain | Folder di VPS | `STACK_NAME` |
|--------|------------|--------|---------------|--------------|
| `main` | production | `shirobell.my.id`, `*.shirobell.my.id` | `/opt/shirobell` | `shirobell` |
| `dev` | staging | `dev.shirobell.my.id`, `*.dev.shirobell.my.id` | `/opt/shirobell-dev` | `shirobell-dev` |

Setiap push ke `dev` atau `main` menjalankan GitHub Actions: test, build image, simpan image di GitHub Container Registry (GHCR), lalu deploy ke lingkungannya.

## Kenapa

- Fitur baru dicoba di staging dulu, lalu dirilis ke production dengan merge `dev` ke `main`.
- Tidak bentrok dengan layanan lain di VPS.
- Rollback cukup dengan menjalankan image versi sebelumnya.

## Arsitektur

```
production: Tamu -> Cloudflare -> Nginx proxy -> shirobell-web     -> shirobell-app     -> MySQL (db shirobell)
staging:    Tamu -----------------> Nginx proxy -> shirobell-dev-web -> shirobell-dev-app -> MySQL (db shirobell_dev)
```

Setiap lingkungan punya 4 container (`-web`, `-app`, `-worker`, `-scheduler`) dan volume `-storage` sendiri.

File di repo:
- `Dockerfile`, `docker-compose.prod.yml` (dipakai kedua lingkungan), `docker/`;
- `.env.production.example`, `.env.staging.example`;
- `docker/proxy/shirobell.conf`, `docker/proxy/shirobell-dev.conf`;
- `.github/workflows/deploy.yml`.

## Prerequisites

| Item | Keterangan |
|------|------------|
| VPS | Docker dan Docker Compose v2 (`docker compose version`) |
| Nginx proxy | Container Nginx yang memegang port 80/443, versi 1.25.1 atau lebih baru untuk `http2 on;` |
| MySQL | Versi 8.x (container atau di host) |
| DNS | Domain `shirobell.my.id` di Cloudflare |
| Token Cloudflare | Untuk sertifikat staging: My Profile > API Tokens > template "Edit zone DNS", zona `shirobell.my.id` |

## Langkah

### 1. Branch di GitHub (owner)

1. Buka repo, lalu **Branches > New branch**: nama `main`, sumber `dev`.
2. Buka **Settings > Branches > Add branch protection rule** untuk `main`. Aktifkan **Require a pull request before merging** dan **Require status checks to pass** (pilih job `test`).
3. Rilis ke production dilakukan lewat Pull Request dari `dev` ke `main`.

### 2. Cloudflare DNS

| Type | Name | Content | Proxy |
|------|------|---------|-------|
| A | `@` | IP VPS | Proxied (oranye) |
| A | `*` | IP VPS | Proxied (oranye) |
| A | `dev` | IP VPS | DNS only (abu-abu) |
| A | `*.dev` | IP VPS | DNS only (abu-abu) |

- Buka **SSL/TLS > Overview**, lalu pilih **Full (strict)**.
- Buka **SSL/TLS > Origin Server > Create Certificate**, untuk hostname `shirobell.my.id` dan `*.shirobell.my.id`. Simpan certificate sebagai `shirobell-origin.pem` dan private key sebagai `shirobell-origin.key`.

Staging memakai DNS only, karena sertifikat gratis Cloudflare tidak menutup subdomain dua tingkat seperti `budi.dev.shirobell.my.id`.

### 3. Sertifikat staging (Let's Encrypt wildcard)

Di VPS:

```bash
sudo mkdir -p /etc/letsencrypt
sudo sh -c 'echo "dns_cloudflare_api_token = TOKEN-CLOUDFLARE" > /etc/letsencrypt/cloudflare.ini'
sudo chmod 600 /etc/letsencrypt/cloudflare.ini

sudo docker run --rm -v /etc/letsencrypt:/etc/letsencrypt certbot/dns-cloudflare:v2.11.0 certonly \
  --dns-cloudflare --dns-cloudflare-credentials /etc/letsencrypt/cloudflare.ini \
  -d dev.shirobell.my.id -d '*.dev.shirobell.my.id' \
  --agree-tos -m EMAIL-ADMIN --non-interactive
```

Perpanjangan otomatis, dengan cron root mingguan:

```
0 3 * * 1 docker run --rm -v /etc/letsencrypt:/etc/letsencrypt certbot/dns-cloudflare:v2.11.0 renew --quiet && docker exec NAMA-CONTAINER-NGINX nginx -s reload
```

### 4. Nginx proxy

1. Cari nama network container Nginx:

   ```bash
   docker inspect NAMA-CONTAINER-NGINX --format '{{json .NetworkSettings.Networks}}'
   ```

   Nama ini menjadi `PROXY_NETWORK` di kedua `.env`.
2. Letakkan `shirobell-origin.pem` dan `shirobell-origin.key` di folder sertifikat yang di-mount ke container Nginx (contoh: `/etc/nginx/certs`). Pastikan juga `/etc/letsencrypt` ter-mount (hanya baca).
3. Salin `docker/proxy/shirobell.conf` dan `docker/proxy/shirobell-dev.conf` ke folder `conf.d` Nginx. Sesuaikan path sertifikat kalau berbeda. Untuk Nginx lebih lama dari 1.25.1, ganti `listen 443 ssl; http2 on;` dengan `listen 443 ssl http2;`.
4. Jalankan `docker exec NAMA-CONTAINER-NGINX nginx -t`, lalu `nginx -s reload`.

Production mengambil IP pengunjung dari header `CF-Connecting-IP`. Untuk mencegah pemalsuan header lewat akses langsung ke IP VPS, sebaiknya port 443 production hanya menerima IP Cloudflare (opsional, lewat firewall atau `allow`/`deny`).

### 5. Database (dua database terpisah)

```bash
docker exec -it NAMA-CONTAINER-MYSQL mysql -uroot -p
```

```sql
CREATE DATABASE shirobell CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'shirobell'@'%' IDENTIFIED BY 'PASSWORD-PRODUCTION';
GRANT ALL PRIVILEGES ON shirobell.* TO 'shirobell'@'%';

CREATE DATABASE shirobell_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'shirobell_dev'@'%' IDENTIFIED BY 'PASSWORD-STAGING-BERBEDA';
GRANT ALL PRIVILEGES ON shirobell_dev.* TO 'shirobell_dev'@'%';
FLUSH PRIVILEGES;
```

Network MySQL menjadi `DB_NETWORK`, dan nama container MySQL menjadi `DB_HOST`. Kalau MySQL di host: `DB_HOST=host.docker.internal`, dan `DB_NETWORK` diisi network yang sudah ada (misalnya network proxy).

### 6. Folder dan `.env`

```bash
sudo mkdir -p /opt/shirobell /opt/shirobell-dev && sudo chown $USER /opt/shirobell /opt/shirobell-dev
nano /opt/shirobell/.env        # isi dari .env.production.example
nano /opt/shirobell-dev/.env    # isi dari .env.staging.example
chmod 600 /opt/shirobell/.env /opt/shirobell-dev/.env
```

| Kunci | Production | Staging |
|-------|-----------|---------|
| `APP_KEY` | `php artisan key:generate --show` di laptop | Buat **lagi** (berbeda) |
| `PROXY_NETWORK`, `DB_NETWORK`, `DB_HOST` | Dari langkah 4 dan 5 | Sama |
| `DB_PASSWORD` | Password production | Password staging |
| `MAIL_USERNAME`, `MAIL_PASSWORD` | Brevo | Boleh sama |
| `MIDTRANS_SERVER_KEY` | Sandbox dulu, production setelah disetujui (`MIDTRANS_IS_PRODUCTION=true`) | Selalu sandbox |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD` | Admin production | Admin staging |

### 7. Kunci deploy dan secret GitHub

Di laptop:

```bash
ssh-keygen -t ed25519 -C "shirobell-deploy" -f shirobell_deploy -N ""
```

1. Tambahkan isi `shirobell_deploy.pub` ke `~/.ssh/authorized_keys` milik user deploy di VPS. User ini harus anggota grup `docker`.
2. Di GitHub, buka **Settings > Secrets and variables > Actions > Repository secrets**:

   | Secret | Isi |
   |--------|-----|
   | `VPS_HOST` | IP VPS |
   | `VPS_USER` | User deploy |
   | `VPS_SSH_KEY` | Isi file `shirobell_deploy` (private key) |
   | `VPS_PORT` | Opsional, kalau SSH bukan port 22 |

3. Buka **Settings > Environments**, lalu buat dua environment, masing-masing dengan secret `DEPLOY_PATH`:

   | Environment | `DEPLOY_PATH` |
   |-------------|---------------|
   | `staging` | `/opt/shirobell-dev` |
   | `production` | `/opt/shirobell` |

   Di environment `production`, aktifkan **Required reviewers** (diri sendiri), supaya deploy production harus disetujui dulu.

4. Hapus file private key dari laptop setelah disimpan di GitHub.

Selama secret belum lengkap, job deploy dilewati dengan peringatan, bukan gagal.

### 8. Deploy pertama

1. **Staging:** push apa pun ke `dev`, atau buka **Actions > CI & Deploy > Run workflow > dev**.
2. Setelah job deploy hijau, jalankan seeder awal sekali saja (pengaturan default, tema, admin):

   ```bash
   cd /opt/shirobell-dev && docker compose -f docker-compose.prod.yml run --rm app php artisan db:seed --force
   ```

3. Uji staging. Kalau sudah baik, buat Pull Request `dev` ke `main`, lalu merge. Production ter-deploy (dengan persetujuan kalau Required reviewers aktif). Jalankan seeder awal yang sama di `/opt/shirobell`.
4. Hapus `ADMIN_PASSWORD` dari `.env` setelah berhasil login.

### 9. Midtrans

Satu akun Midtrans punya satu Notification URL per mode:
- **Sandbox**: `https://dev.shirobell.my.id/webhooks/midtrans`. Selama production belum disetujui dan masih memakai sandbox, URL ini dipindah ke production saat uji bayar di sana.
- **Production** (setelah disetujui): `https://shirobell.my.id/webhooks/midtrans`.

## Verifikasi

| Cek | Production | Staging |
|-----|-----------|---------|
| `docker compose -f docker-compose.prod.yml ps` di foldernya | 4 container running, app healthy | Sama |
| `/health` | `https://shirobell.my.id/health` menampilkan `ok` | `https://dev.shirobell.my.id/health` |
| Subdomain acak | `https://acak.shirobell.my.id`: "Undangan tidak ditemukan" | `https://acak.dev.shirobell.my.id` |
| Header | Tanpa `X-Robots-Tag` | `X-Robots-Tag: noindex, nofollow` |
| Login salah 6 kali dari satu HP | Hanya HP itu terkunci | Sama |
| Email verifikasi, checkout sandbox, scan QR di HP | Berhasil | Berhasil |

## Update

Otomatis: push ke `dev` untuk staging, merge ke `main` untuk production. Workflow menjalankan `migrate --force`, `ThemeSeeder`, `up -d`, dan `queue:restart`.

Setelah mengubah `.env`:

```bash
cd /opt/shirobell   # atau /opt/shirobell-dev
docker compose -f docker-compose.prod.yml up -d --force-recreate
```

## Rollback

```bash
cd /opt/shirobell
IMAGE_TAG=SHA-COMMIT-SEBELUMNYA docker compose -f docker-compose.prod.yml up -d
```

Tag image ada di GitHub: **Packages > shirobell-app**. Kalau ada migrasi yang bermasalah, backup database dulu, lalu jalankan `docker compose -f docker-compose.prod.yml run --rm app php artisan migrate:rollback --step=N`.

Kalau image GHCR private dan perintah dijalankan manual, login dulu: `docker login ghcr.io -u yobel47`, dengan token GitHub ber-scope `read:packages`.

## Backup (production, minimal harian, simpan di luar VPS)

```bash
docker exec NAMA-CONTAINER-MYSQL mysqldump -ushirobell -p --single-transaction shirobell | gzip > shirobell-$(date +%F).sql.gz
docker run --rm -v shirobell-storage:/data -v "$PWD":/backup alpine tar czf /backup/storage-$(date +%F).tgz -C /data app/public
```

Enkripsi sebelum diunggah (misalnya `gpg --symmetric`). Uji restore minimal sebulan sekali.

## Troubleshooting

| Gejala | Penyebab dan solusi |
|--------|---------------------|
| 502 Bad Gateway | Container Nginx tidak satu network dengan `shirobell-web` atau `shirobell-dev-web`. Cek `PROXY_NETWORK` |
| Redirect berulang di production | Cloudflare memakai mode Flexible. Ganti ke Full (strict) |
| Sertifikat staging salah atau tidak aman | Record `dev` atau `*.dev` masih Proxied, atau sertifikat Let's Encrypt belum dibuat atau path-nya salah |
| 419 Page Expired setelah login | `TRUSTED_PROXIES` kosong, atau `APP_URL` bukan `https://` |
| Subdomain undangan 404 | `APP_DOMAIN` salah, atau record wildcard belum ada |
| `SQLSTATE[HY000] [2002]` | `DB_HOST` atau `DB_NETWORK` salah |
| Email tidak terkirim | Cek `docker logs shirobell-worker`, kredensial Brevo, dan verifikasi domain |
| Foto tidak tampil | Volume `-storage` tidak ter-mount di container web |
| Deploy dilewati dengan peringatan | Secret `VPS_*` atau `DEPLOY_PATH` di environment belum diisi |
