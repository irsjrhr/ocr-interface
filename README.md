# Webhook Nanonets - Laravel

Project ini berisi implementasi Laravel PHP untuk menerima webhook Nanonets, memvalidasi API key, menyimpan hasil transformasi payload ke file JSON, dan mengelola statistik penggunaan.

## Endpoint

- `GET /` health check
- `POST /api/v1/admin/keys` membuat API key client
- `GET /api/v1/admin/keys` menampilkan daftar API key
- `DELETE /api/v1/admin/keys/{key}` menghapus API key
- `GET /api/v1/admin/usages` menampilkan statistik penggunaan
- `POST /api/v1/webhook` menerima payload Nanonets

## Header

- Admin endpoint memakai header `x-admin-key`
- Webhook endpoint memakai header `x-api-key`
- Webhook mendukung header opsional `source-type`
- Webhook mendukung header opsional `include-pdf` dengan nilai `1`, `true`, `yes`, atau `on`

## Setup

1. Install dependency:

```bash
composer install
```

2. Jika belum ada `.env`, copy dari `.env.example`:

```bash
copy .env.example .env
```

3. Generate application key:

```bash
php artisan key:generate
```

4. Pastikan database di `.env` sudah sesuai. Default project ini memakai SQLite:

```env
DB_CONNECTION=sqlite
```

Untuk SQLite, aktifkan extension PHP `pdo_sqlite` dan `sqlite3`. Jika memakai MySQL, ubah `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.

5. Jalankan migration:

```bash
php artisan migrate
```

6. Jalankan server:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Untuk instalasi sederhana, gunakan `QUEUE_CONNECTION=sync` agar job webhook langsung diproses. Jika ingin proses benar-benar berjalan di background, gunakan queue driver seperti `database` atau `redis`, lalu jalankan `php artisan queue:work`.

Pastikan PHP extension `fileinfo`, `openssl`, dan driver database yang dipakai Laravel sudah aktif.
