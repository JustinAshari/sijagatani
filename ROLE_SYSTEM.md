# Sistem Role & Permission - SIJAGATANI

## Revisi Sistem Role (20 Januari 2026)

### Konsep Penting
**Semua akun dalam sistem adalah akun admin/petugas, TIDAK ADA akun untuk petani atau masyarakat umum.**

Data petani dan penggilingan dikelola oleh petugas Bulog, bukan oleh petani itu sendiri.

---

## Daftar Role

### 1. **SuperAdmin**
- **Role Code:** `superadmin`
- **Pemegang:** Super Administrator sistem
- **Akses:**
  - ✅ Akses penuh ke seluruh sistem
  - ✅ Manajemen user (CRUD users)
  - ✅ Manajemen data petani (CRUD)
  - ✅ Manajemen data penggilingan/makloon (CRUD)
  - ✅ Manajemen data wilayah (CRUD)
  - ✅ Export/Import data

### 2. **Admin**
- **Role Code:** `admin`
- **Pemegang:** Administrator umum
- **Akses:**
  - ✅ Manajemen data petani (CRUD)
  - ✅ Manajemen data penggilingan/makloon (CRUD)
  - ❌ Tidak bisa manajemen user
  - ❌ Tidak bisa manajemen wilayah

### 3. **Admin Lapangan Bulog** (Role: `petani`)
- **Role Code:** `petani`
- **Pemegang:** Admin lapangan Bulog di lapangan
- **Fungsi:** Mengelola data petani yang mengajukan komoditi
- **Akses:**
  - ✅ Menambah data petani baru
  - ✅ Mengedit data petani
  - ✅ Menghapus data petani
  - ✅ Melihat & export data petani
  - ✅ Upload foto KTP, foto petani, foto komoditi, kwitansi pembayaran
  - ❌ Tidak bisa akses data penggilingan
  - ❌ Tidak bisa manajemen user
  - ❌ Tidak bisa manajemen wilayah

**CATATAN:** 
- Role "petani" BUKAN untuk petani sesungguhnya
- Ini adalah role untuk admin/petugas Bulog yang bertugas mencatat data petani
- Satu akun bisa digunakan untuk mencatat banyak petani

### 4. **Admin Penggilingan** (Role: `penggilingan`)
- **Role Code:** `penggilingan`
- **Pemegang:** Admin pekerja di tempat penggilingan/makloon
- **Fungsi:** Mengelola data makloon dan transportasi GKP
- **Akses:**
  - ✅ Menambah data penggilingan/makloon baru
  - ✅ Mengedit data penggilingan
  - ✅ Menghapus data penggilingan
  - ✅ Melihat & export data penggilingan
  - ✅ Kelola data transport (nama pengemudi, nopol, kuantum, nota timbang, surat jalan)
  - ✅ Upload foto GKP (sebelum & sesudah)
  - ✅ Export laporan Makloon GKP (Excel)
  - ❌ Tidak bisa akses data petani
  - ❌ Tidak bisa manajemen user
  - ❌ Tidak bisa manajemen wilayah

**CATATAN:** 
- Setiap tempat penggilingan/makloon dibuatkan akun khusus
- Satu akun untuk satu lokasi penggilingan
- Akun ini dipegang oleh petugas/admin di penggilingan tersebut

---

## Matriks Permission

| Fitur | SuperAdmin | Admin | Admin Lapangan (petani) | Admin Penggilingan |
|-------|-----------|-------|------------------------|-------------------|
| **Data Petani** |
| Lihat data petani | ✅ | ✅ | ✅ | ❌ |
| Tambah data petani | ✅ | ✅ | ✅ | ❌ |
| Edit data petani | ✅ | ✅ | ✅ | ❌ |
| Hapus data petani | ✅ | ✅ | ✅ | ❌ |
| Export data petani | ✅ | ✅ | ✅ | ❌ |
| **Data Penggilingan/Makloon** |
| Lihat data penggilingan | ✅ | ✅ | ❌ | ✅ |
| Tambah data penggilingan | ✅ | ✅ | ❌ | ✅ |
| Edit data penggilingan | ✅ | ✅ | ❌ | ✅ |
| Hapus data penggilingan | ✅ | ✅ | ❌ | ✅ |
| Export data penggilingan | ✅ | ✅ | ❌ | ✅ |
| Export Makloon GKP | ✅ | ✅ | ❌ | ✅ |
| **Manajemen User** |
| Lihat daftar user | ✅ | ❌ | ❌ | ❌ |
| Tambah user baru | ✅ | ❌ | ❌ | ❌ |
| Edit user | ✅ | ❌ | ❌ | ❌ |
| Hapus user | ✅ | ❌ | ❌ | ❌ |
| **Manajemen Wilayah** |
| CRUD Provinsi | ✅ | ❌ | ❌ | ❌ |
| CRUD Kabupaten | ✅ | ❌ | ❌ | ❌ |
| CRUD Kecamatan | ✅ | ❌ | ❌ | ❌ |
| CRUD Kalurahan | ✅ | ❌ | ❌ | ❌ |
| Export/Import Wilayah | ✅ | ❌ | ❌ | ❌ |

---

## Implementasi di Kode

### Middleware di Routes (`routes/api.php`)

```php
// Petani Routes - Admin, Admin Lapangan, dan SuperAdmin bisa akses
Route::middleware('role:admin,petani,superadmin')->group(function () {
    Route::get('/petani', [PetaniController::class, 'index']);
    Route::post('/petani', [PetaniController::class, 'store']);
    Route::put('/petani/{id}', [PetaniController::class, 'update']);
    Route::delete('/petani/{id}', [PetaniController::class, 'destroy']);
    // ...
});

// Penggilingan Routes - Admin, Admin Penggilingan, dan SuperAdmin bisa akses
Route::middleware('role:admin,penggilingan,superadmin')->group(function () {
    Route::get('/penggilingan', [PenggilinganController::class, 'index']);
    Route::post('/penggilingan', [PenggilinganController::class, 'store']);
    Route::put('/penggilingan/{id}', [PenggilinganController::class, 'update']);
    Route::delete('/penggilingan/{id}', [PenggilinganController::class, 'destroy']);
    // ...
});

// User Management - Hanya SuperAdmin
Route::middleware('role:superadmin')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    // ...
});
```

### Helper Methods di User Model (`app/Models/User.php`)

```php
// Check role
$user->isSuperAdmin()          // true jika role = superadmin
$user->isAdmin()                // true jika role = admin
$user->isAdminLapangan()        // true jika role = petani (Admin Lapangan Bulog)
$user->isAdminPenggilingan()    // true jika role = penggilingan

// Check permission
$user->canManagePetani()        // true jika bisa kelola data petani
$user->canManagePenggilingan()  // true jika bisa kelola data penggilingan

// Get role name
$user->getRoleName()            // "Admin Lapangan Bulog", "Admin Penggilingan", dst
```

---

## Perubahan dari Sistem Sebelumnya

### ❌ Sistem Lama (Salah)
- Role "petani" dan "penggilingan" **hanya bisa melihat data**
- Role tersebut tidak bisa menambah/edit/hapus data

### ✅ Sistem Baru (Benar)
- Role "petani" (Admin Lapangan) **bisa menambah, edit, hapus data petani**
- Role "penggilingan" (Admin Penggilingan) **bisa menambah, edit, hapus data penggilingan**
- Petani dan masyarakat umum **TIDAK memiliki akun** dalam sistem
- Semua data dikelola oleh petugas/admin Bulog dan penggilingan

---

## Contoh Skenario Penggunaan

### Skenario 1: Admin Lapangan Bulog Mencatat Petani
1. Admin lapangan login dengan akun role `petani`
2. Petani datang ke kantor Bulog membawa komoditi
3. Admin mencatat data petani (NIK, nama, alamat, jumlah komoditi, dll)
4. Admin upload foto KTP petani, foto petani, foto komoditi
5. Data tersimpan dalam sistem

### Skenario 2: Admin Penggilingan Mencatat Makloon
1. Admin penggilingan login dengan akun role `penggilingan`
2. GKP datang dari petani untuk digiling
3. Admin mencatat nama petani, nama penggilingan, lokasi
4. Admin input data transportasi (sopir, nopol, kuantum)
5. Admin upload foto GKP sebelum digiling
6. Admin upload nota timbang dan surat jalan
7. Data tersimpan dalam sistem
8. Admin bisa export laporan Makloon GKP

### Skenario 3: SuperAdmin Membuat Akun Baru
1. SuperAdmin login
2. Akses menu User Management
3. Buat akun baru untuk penggilingan "PT Maju Jaya"
   - Username: penggilingan_majujaya
   - Email: majujaya@example.com
   - Role: penggilingan
4. Berikan credential ke admin di PT Maju Jaya
5. Admin PT Maju Jaya bisa mulai mencatat data makloon mereka

---

## Database Schema

### Tabel `users`
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    role ENUM('superadmin', 'admin', 'petani', 'penggilingan') DEFAULT 'petani',
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Nilai Role yang Valid:
- `superadmin` - Super Administrator
- `admin` - Administrator umum  
- `petani` - Admin Lapangan Bulog
- `penggilingan` - Admin Penggilingan/Makloon

---

## Catatan Penting untuk Developer

1. **Jangan gunakan istilah "User Petani" atau "User Penggilingan"**
   - Gunakan "Admin Lapangan Bulog" untuk role `petani`
   - Gunakan "Admin Penggilingan" untuk role `penggilingan`

2. **Di UI/Frontend:**
   - Tampilkan nama role yang lebih deskriptif
   - "petani" → "Admin Lapangan Bulog"
   - "penggilingan" → "Admin Penggilingan"
   
3. **Saat membuat akun baru:**
   - Jelaskan dengan jelas bahwa akun tersebut untuk petugas, bukan untuk petani
   - Setiap penggilingan perlu akun terpisah

4. **Permission check:**
   - Gunakan helper method `canManagePetani()` dan `canManagePenggilingan()`
   - Jangan hardcode pengecekan role di banyak tempat

---

## FAQ

**Q: Apakah petani punya akun?**  
A: **TIDAK**. Petani tidak punya akun. Data petani dicatat oleh Admin Lapangan Bulog.

**Q: Kenapa role-nya dinamakan "petani" padahal bukan untuk petani?**  
A: Ini warisan dari sistem lama. Secara teknis role-nya tetap `petani` di database, tapi secara fungsional ini adalah role untuk Admin Lapangan Bulog yang mengelola data petani.

**Q: Berapa akun yang perlu dibuat untuk 10 penggilingan?**  
A: 10 akun, satu akun untuk setiap penggilingan dengan role `penggilingan`.

**Q: Apakah Admin bisa mengelola data petani dan penggilingan sekaligus?**  
A: Ya, role `admin` bisa mengelola kedua-duanya.

**Q: Apakah Admin Lapangan bisa melihat data penggilingan?**  
A: Tidak. Admin Lapangan (role `petani`) hanya bisa mengelola data petani saja.

**Q: Bagaimana cara menambah role baru?**  
A: 
1. Tambahkan role di migration (ALTER TABLE enum)
2. Update middleware `CheckRole`
3. Update routes dengan role baru
4. Tambahkan helper method di User model

---

*Dokumen ini berlaku efektif sejak 20 Januari 2026*
