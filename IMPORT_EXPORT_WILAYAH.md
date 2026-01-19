# Export & Import Data Wilayah

Dokumentasi fitur Export dan Import data wilayah (Provinsi, Kabupaten, Kecamatan, Kalurahan/Desa) untuk Super Admin.

## 🎯 Fitur Utama

### 1. Export Data Wilayah
- **Satu kali export** untuk **SEMUA data wilayah**
- File Excel dengan **1 sheet** berisi **4 tabel terpisah**:
  - Tabel 1: **PROVINSI** (Tipe, ID, ID Parent, Nama Parent, Nama)
  - Tabel 2: **KABUPATEN** (Tipe, ID, ID Parent, Nama Parent, Nama)
  - Tabel 3: **KECAMATAN** (Tipe, ID, ID Parent, Nama Parent, Nama)
  - Tabel 4: **KALURAHAN** (Tipe, ID, ID Parent, Nama Parent, Nama)
- **Tanpa kolom tanggal** (created_at, updated_at)
- Format: `.xlsx`
- Tabel dipisahkan dengan **baris kosong**

### 2. Import Data Wilayah
- **Satu kali import** untuk **SEMUA data wilayah**
- File harus berisi **1 sheet** dengan **4 tabel** (dipisahkan baris kosong)
- Import dilakukan **berurutan otomatis** berdasarkan kolom **Tipe**
- Validasi otomatis untuk setiap baris
- Format support: `.xlsx`, `.xls`, `.csv`
- Max ukuran: **5 MB**

### 3. Template Download
- Template Excel dengan **1 sheet**
- Berisi **4 tabel contoh** dengan data sample
- Header terformat (bold dengan background abu-abu)
- Siap diisi data sesuai format

---

## 📋 Format Data (1 Sheet dengan 4 Tabel)

### Header Kolom:
| Tipe | ID | ID Parent | Nama Parent | Nama |
|------|-------|-----------|-------------|------|

### Contoh Data dalam 1 Sheet:

```
Tipe        | ID | ID Parent | Nama Parent      | Nama
-----------------------------------------------------------------
PROVINSI    | 1  |           |                  | DI Yogyakarta
PROVINSI    | 2  |           |                  | Jawa Tengah

KABUPATEN   | 1  | 1         | DI Yogyakarta    | Sleman
KABUPATEN   | 2  | 1         | DI Yogyakarta    | Bantul

KECAMATAN   | 1  | 1         | Sleman           | Mlati
KECAMATAN   | 2  | 1         | Sleman           | Depok

KALURAHAN   | 1  | 1         | Mlati            | Sendangadi
KALURAHAN   | 2  | 1         | Mlati            | Tlogoadi
```

### Penjelasan Kolom:

1. **Tipe**: Jenis data (`PROVINSI`, `KABUPATEN`, `KECAMATAN`, `KALURAHAN`) - **Case sensitive!**
2. **ID**: ID unik untuk setiap record (opsional saat import, auto-generate jika kosong)
3. **ID Parent**: ID dari tabel parent (kosong untuk Provinsi, wajib untuk yang lain)
4. **Nama Parent**: Nama parent (opsional, hanya untuk info - tidak wajib)
5. **Nama**: Nama wilayah - **Wajib diisi!**

### Validasi per Tipe:

#### PROVINSI
- **Tipe**: Harus "PROVINSI" (uppercase)
- **ID Parent**: Kosong (tidak ada parent)
- **Nama**: Required, max 255 karakter, unique

#### KABUPATEN
- **Tipe**: Harus "KABUPATEN" (uppercase)
- **ID Parent**: Required, must exist in provinsi table
- **Nama**: Required, max 255 karakter

#### KECAMATAN
- **Tipe**: Harus "KECAMATAN" (uppercase)
- **ID Parent**: Required, must exist in kabupaten table
- **Nama**: Required, max 255 karakter

#### KALURAHAN
- **Tipe**: Harus "KALURAHAN" (uppercase)
- **ID Parent**: Required, must exist in kecamatan table
- **Nama**: Required, max 255 karakter

---

## 🚀 Cara Penggunaan

### Export Data
1. Login sebagai **Super Admin**
2. Buka menu **Data Wilayah**
3. Klik tombol **"📥 Export Semua Data"** di header (atas tabs)
4. File `data_wilayah_[timestamp].xlsx` akan terdownload
5. File berisi **1 sheet** dengan **4 tabel** (Provinsi, Kabupaten, Kecamatan, Kalurahan)

### Import Data
1. Login sebagai **Super Admin**
2. Buka menu **Data Wilayah**
3. Klik tombol **"📤 Import Data"** di header (atas tabs)
4. Di modal import:
   - Klik **"📄 Download Template Data Wilayah"** untuk mendapatkan template
   - Isi data di **1 sheet** dengan **4 tabel** (pisahkan dengan baris kosong)
   - Pastikan kolom **Tipe** diisi dengan benar (PROVINSI, KABUPATEN, KECAMATAN, KALURAHAN)
   - **Upload file Excel** yang sudah diisi
5. Klik **"Import Sekarang"**
6. Sistem akan import **otomatis berdasarkan kolom Tipe**
7. Refresh otomatis setelah import berhasil

---

## ⚠️ Catatan Penting

### Format Sheet
- **1 Sheet saja** dengan nama "Data Wilayah" atau default
- **4 Tabel** dalam sheet yang sama
- Pisahkan setiap tabel dengan **1 baris kosong**
- **Header harus di baris 1**: Tipe, ID, ID Parent, Nama Parent, Nama

### Kolom Tipe (PENTING!)
- Harus **UPPERCASE**: `PROVINSI`, `KABUPATEN`, `KECAMATAN`, `KALURAHAN`
- **Case sensitive** - jika lowercase/mixed akan error
- Menentukan tabel mana yang akan menerima data

### Urutan Import
- Import **tidak harus berurutan** dalam file
- Sistem akan memproses berdasarkan kolom **Tipe**
- **NAMUN**: Pastikan ID Parent sudah exist sebelum import child
- Contoh: Import Provinsi dulu, baru Kabupaten (karena Kabupaten butuh ID Provinsi)

### ID Parent
- **Kosong** untuk Provinsi (tidak ada parent)
- **Wajib** untuk Kabupaten (ID Provinsi)
- **Wajib** untuk Kecamatan (ID Kabupaten)
- **Wajib** untuk Kalurahan (ID Kecamatan)
- ID Parent harus **valid** dan **exist** di tabel parent

### Nama Parent
- Kolom **opsional** (hanya untuk informasi/memudahkan baca)
- Tidak divalidasi dan tidak disimpan
- Hanya untuk memudahkan pengecekan visual

### Validasi
- Semua field **required** harus diisi
- ID parent harus **valid** (exist di tabel parent)
- Nama Provinsi harus **unique**
- Format file: `.xlsx`, `.xls`, atau `.csv`
- Maksimal ukuran: **5 MB**

### Error Handling
- Jika ada error di salah satu baris, **import akan berhenti**
- Pesan error akan ditampilkan dengan detail baris yang error
- Data yang sudah diimport **tidak akan di-rollback**
- Perbaiki error dan import ulang

---

## 🔧 Technical Details

### Backend (Laravel)

#### Files Created/Modified:
1. **`app/Exports/WilayahExport.php`** - Single sheet dengan 4 tabel
2. **`app/Imports/WilayahImport.php`** - Baca 1 sheet dengan 4 tabel
3. **`app/Http/Controllers/WilayahController.php`** - 3 methods:
   - `exportWilayah()` - Export semua data (1 sheet, 4 tabel)
   - `importWilayah()` - Import semua data (1 sheet, 4 tabel)
   - `templateWilayah()` - Download template (1 sheet, 4 tabel contoh)
4. **`routes/api.php`** - 3 routes

#### API Endpoints (SuperAdmin only):
- `GET /api/wilayah/export` - Export semua data wilayah (1 sheet)
- `POST /api/wilayah/import` - Import semua data wilayah (1 sheet)
- `GET /api/wilayah/template` - Download template (1 sheet dengan contoh)

---

### Frontend (Vue.js)

#### Files Modified:
1. **`src/views/WilayahView.vue`**
   - Export/Import buttons di header (atas tabs)
   - Import modal untuk combined import
   - Functions:
     - `exportAllData()` - Export semua data (1 file, 1 sheet)
     - `downloadTemplate()` - Download template (1 sheet)
     - `importData()` - Import semua data (1 file, 1 sheet)

---

## 📦 Package Requirements

### Backend:
```bash
composer require maatwebsite/excel
```

### Frontend:
- Axios untuk API calls
- File upload handling

---

## 🎨 UI/UX

### Button Locations:
- **Export Semua Data**: Header utama (sebelum tabs)
- **Import Data**: Header utama (sebelah Export button)
- **Tambah Data**: Di setiap tab (kanan atas section)

### Modal Import:
- Step 1: Download template (1 sheet dengan 4 tabel contoh)
- Step 2: Upload file Excel
- Validation notes dan instructions
- Import progress indicator

---

## 🔐 Akses & Permissions

✅ **Super Admin**: Full akses (CRUD, export, import, template)
❌ **Admin**: Tidak ada akses export/import
❌ **Petani**: Tidak ada akses
❌ **Penggilingan**: Tidak ada akses

---

## 📝 Example Data Flow

### Export:
1. User klik "Export Semua Data"
2. Backend query all data (Provinsi, Kabupaten, Kecamatan, Kalurahan)
3. Generate Excel dengan **1 sheet** berisi **4 tabel** (dipisah baris kosong)
4. Download file `data_wilayah_[timestamp].xlsx`

### Import:
1. User klik "Import Data"
2. User download template (1 sheet dengan 4 tabel contoh)
3. User isi data di 1 sheet (4 tabel dengan baris kosong pemisah)
4. User upload file Excel
5. Backend baca setiap baris dan proses berdasarkan kolom **Tipe**
6. Backend import otomatis ke tabel yang sesuai
7. Success message + auto refresh data

---

## 🐛 Troubleshooting

### Error: "Tipe tidak valid"
- **Solusi**: Pastikan kolom Tipe diisi dengan **UPPERCASE**: `PROVINSI`, `KABUPATEN`, `KECAMATAN`, atau `KALURAHAN`

### Error: "ID parent tidak valid"
- **Solusi**: Pastikan ID parent sudah exist di tabel parent
- Contoh: ID Provinsi di baris Kabupaten harus sudah ada di baris Provinsi

### Error: "Nama wajib diisi"
- **Solusi**: Pastikan kolom **Nama** tidak kosong

### Error: "Format file tidak valid"
- **Solusi**: Gunakan template yang disediakan atau pastikan format `.xlsx`, `.xls`, `.csv`

### Error: "File terlalu besar"
- **Solusi**: Compress file atau import data bertahap (max 5 MB)

---

## ✅ Best Practices

1. **Download template terlebih dahulu** untuk melihat format yang benar
2. **Isi data berurutan**: Provinsi → Kabupaten → Kecamatan → Kalurahan
3. **Pisahkan tabel dengan 1 baris kosong** untuk kemudahan membaca
4. **Gunakan UPPERCASE untuk kolom Tipe** (PROVINSI, KABUPATEN, etc.)
5. **Backup data** sebelum import untuk jaga-jaga
6. **Test dengan data kecil** dulu sebelum import data besar

---

## 📞 Support

Untuk pertanyaan atau masalah, hubungi administrator sistem.
