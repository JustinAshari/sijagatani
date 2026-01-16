# SiJagaTani - Sistem Informasi Arsip Data Petani

Aplikasi web fullstack untuk mengelola data petani dan penggilingan padi dengan fitur CRUD lengkap dan auto-compress image.

## 🌟 Fitur Utama

### Backend (Laravel)
- ✅ RESTful API dengan Laravel 12
- ✅ CRUD lengkap untuk Data Petani dan Penggilingan
- ✅ Auto compress image menggunakan Intervention Image
- ✅ Validasi data dengan Form Request
- ✅ Relasi database (One to Many: Petani - Penggilingan)
- ✅ Image Service untuk handle upload & compression
- ✅ Laravel Sanctum untuk API authentication (ready)
- ✅ Export data ke Excel menggunakan Laravel Excel

### Frontend (Vue 3)
- ✅ Vue 3 + TypeScript dengan Composition API
- ✅ Interface modern dan responsive
- ✅ CRUD dengan modal dialog
- ✅ Upload image dengan preview
- ✅ Filter dan pencarian data
- ✅ Vue Router untuk navigasi
- ✅ Axios untuk HTTP requests
- ✅ Pinia untuk state management

## 📁 Struktur Database

### Tabel Petani
- `id` - Primary Key
- `nik` - NIK (16 digit, unique)
- `nama` - Nama lengkap
- `alamat` - Alamat lengkap
- `no_telepon` - Nomor telepon
- `jenis_kelamin` - L/P
- `tanggal_lahir` - Tanggal lahir
- `foto` - Foto petani (auto-compressed)

## 🚀 Instalasi dan Menjalankan

### Persiapan Database

1. Buat database MySQL:
```bash
mysql -u root -e "CREATE DATABASE sijagatani_db"
```

### Backend Setup

1. Masuk ke folder backend:
```bash
cd d:\laragon\www\sijagatani\backend
```

2. Install dependencies:
```bash
composer install
```

3. Setup environment (sudah dikonfigurasi):
- Database: MySQL (sijagatani_db)
- APP_URL: http://localhost:8000
- CORS: localhost:5173

4. Jalankan migrasi:
```bash
php artisan migrate
```

5. Jalankan server:
```bash
php artisan serve
```

Server backend akan berjalan di: **http://localhost:8000**

### Frontend Setup

1. Masuk ke folder frontend:
```bash
cd d:\laragon\www\sijagatani\frontend
```

2. Install dependencies (sudah terinstall):
```bash
npm install
```

3. Jalankan development server:
```bash
npm run dev
```

Server frontend akan berjalan di: **http://localhost:5173**

## 📡 API Endpoints

### Petani
- `GET /api/petani` - List semua petani
- `POST /api/petani` - Tambah petani baru
- `GET /api/petani/{id}` - Detail petani
- `PUT /api/petani/{id}` - Update petani
- `DELETE /api/petani/{id}` - Hapus petani
- `GET /api/petani/export/excel` - Export data ke Excel

### Penggilingan
- `GET /api/penggilingan` - List semua data penggilingan
- `POST /api/penggilingan` - Tambah data penggilingan baru
- `GET /api/penggilingan/{id}` - Detail data penggilingan
- `PUT /api/penggilingan/{id}` - Update data penggilingan
- `DELETE /api/penggilingan/{id}` - Hapus data penggilingan
- `GET /api/penggilingan/summary` - Ringkasan data penggilingan
- `GET /api/penggilingan/export/excel` - Export data ke Excel

## 🖼️ Image Compression

Image yang diupload akan otomatis dikompres dengan konfigurasi:
- **Foto Petani**: Max width 800px, quality 85%
- **Foto KTP**: Max width 1200px, quality 85%
- **Foto Komoditi**: Max width 1200px, quality 85%
- **Kwitansi Pembayaran**: Max width 1200px, quality 85%
- Format output: JPEG
- Max upload size: 5MB

Image disimpan di `storage/app/public/` dan dapat diakses via:
```
http://localhost:8000/storage/{path}
```

## 💻 Penggunaan Aplikasi

### Mengelola Data Petani

1. Akses menu **Data Petani**
2. Klik tombol **+ Tambah Petani**
3. Isi form dengan data yang diperlukan
4. Upload foto jika diperlukan (akan otomatis dikompres)
5. Klik **Simpan**

**Fitur lain:**
- 👁️ **Detail**: Melihat informasi lengkap petani
- ✏️ **Edit**: Mengubah data petani
- 🗑️ **Hapus**: Menghapus data petani
- 🔍 **Cari**: Cari berdasarkan nama atau NIK
- 📊 **Export**: Export data ke Excel

### Mengelola Data Penggilingan

1. Akses menu **Data Penggilingan**
2. Klik tombol **+ Tambah Data**
3. Pilih petani dan isi data penggilingan
4. Upload foto-foto yang diperlukan
5. Klik **Simpan**

**Fitur lain:**
- 👁️ **Detail**: Melihat informasi lengkap
- ✏️ **Edit**: Mengubah data
- 🗑️ **Hapus**: Menghapus data
- 🔍 **Filter**: Filter berdasarkan berbagai kriteria
- 📊 **Summary**: Melihat ringkasan data
- 📊 **Export**: Export data ke Excel

## 🛠️ Teknologi yang Digunakan

### Backend
- Laravel 12
- PHP 8.2+
- MySQL
- Intervention Image (Image Processing)
- Laravel Sanctum (API Auth)

### Frontend
- Vue 3
- TypeScript
- Vite
- Vue Router
- Pinia
- Axios

## 📝 Catatan Development

- Image compression menggunakan GD Driver (built-in PHP)
- Storage menggunakan symbolic link (`php artisan storage:link`)
- CORS sudah dikonfigurasi untuk development
- API menggunakan prefix `/api`
- Frontend menggunakan composition API style

## 🔐 Security

- Input validation di backend dengan Form Request
- CSRF protection enabled
- File upload validation (type & size)
- Image compression untuk menghemat storage
- SQL injection protection via Eloquent ORM

## 📄 License

Open-source project untuk pembelajaran dan pengembangan sistem informasi pertanian.

