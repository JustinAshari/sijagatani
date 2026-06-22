# SiJagaTani

SiJagaTani adalah aplikasi web internal untuk pendataan petani, penggilingan padi, wilayah, akun pengguna, dan audit aktivitas. Project ini memakai backend Laravel dan frontend Vue 3 dengan akses berbasis role.

Dokumentasi breakdown lengkap ada di [PROJECT_BREAKDOWN.md](PROJECT_BREAKDOWN.md).

## Ringkasan Stack

### Backend
- PHP 8.2+
- Laravel 12
- MySQL
- Laravel Sanctum
- Maatwebsite Excel
- Intervention Image

### Frontend
- JavaScript
- Vue 3
- Vue Router
- Pinia
- Axios
- Vite

## Fitur Utama

- Login dan logout berbasis token
- Role-based access control untuk superadmin, admin, lapangan, dan penggilingan
- Dashboard ringkasan data
- CRUD data petani
- CRUD data penggilingan / makloon
- Upload dan kompresi gambar otomatis
- Export data ke Excel
- Verifikasi data
- Manajemen wilayah
- Manajemen user dan sub-admin
- Audit log aktivitas

## Struktur Project

```text
.
├── backend/
└── frontend/
```

### Backend
- `app/Http/Controllers/Api`: endpoint API utama
- `app/Http/Requests`: validasi input
- `app/Services`: helper logika bisnis seperti image processing dan logging
- `app/Exports`: export Excel
- `app/Imports`: import data wilayah
- `app/Models`: model dan relasi database
- `database/migrations`: skema database
- `routes/api.php`: seluruh route API

### Frontend
- `src/views`: halaman utama aplikasi
- `src/components`: komponen UI reusable
- `src/router`: navigasi dan guard role
- `src/stores`: state management autentikasi
- `src/services`: komunikasi ke API

## Halaman Utama

- Login
- Beranda / Dashboard
- Data Petani
- Surat Pernyataan
- Data Penggilingan
- Kelola Sub-Admin
- Kelola Akun
- Data Wilayah
- Log Aktivitas
- About

## Cara Menjalankan

### Backend

```bash
cd backend
composer install
php artisan migrate
php artisan serve
```

### Frontend

```bash
cd frontend
npm install
npm run dev
```

## Endpoint Inti

- `POST /api/login`
- `GET /api/me`
- `GET /api/petani`
- `GET /api/penggilingan`
- `GET /api/users`
- `GET /api/activity-logs`

## Catatan

- Frontend saat ini menggunakan JavaScript, bukan TypeScript.
- Layout dashboard memakai sidebar dan navbar responsif.
- Upload file memakai storage public Laravel.

