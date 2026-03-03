<template>
  <div class="petani-list">
    <div class="page-card">

    <!-- Hero Banner -->
    <div class="hero-banner">
      <div class="hero-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
      </div>
      <div class="hero-text">
        <h2>Data Petani</h2>
        <p>Kelola dan pantau data petani yang terdaftar di sistem</p>
      </div>
    </div>

    <div class="header">
      <h2>
        <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
          <polyline points="10 9 9 9 8 9"/>
        </svg>
        Data Petani
      </h2>
      <div class="header-actions">
        <button @click="exportExcel" class="btn-success" :disabled="loading">
          <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Export Excel
        </button>
        <button v-if="authStore.canManagePetani" @click="showAddModal = true" class="btn-primary">
          <span>+</span> Tambah Data Petani
        </button>
      </div>
    </div>

    <div class="filter-section">
      <div class="filter-row">
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Cari nama atau NIK petani..."
          class="search-input"
        />
        <select v-model="filterKabupaten" class="filter-select">
          <option value="">Semua Kabupaten</option>
          <option v-for="kab in kabupatenList" :key="kab" :value="kab">
            {{ kab }}
          </option>
        </select>
      </div>
      <div class="filter-row">
        <div class="date-filter">
          <label>Dari:</label>
          <input v-model="filterTanggalDari" type="date" class="date-input" />
        </div>
        <div class="date-filter">
          <label>Sampai:</label>
          <input v-model="filterTanggalSampai" type="date" class="date-input" />
        </div>
        <button @click="applyFilter" class="btn-filter">
          <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
          Filter
        </button>
        <button @click="resetFilter" class="btn-reset">
          <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="23 4 23 10 17 10"/>
            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
          </svg>
          Reset
        </button>
      </div>
    </div>

    <div class="stats-bar">
      <div class="stat-item">
        <span class="stat-label">Total Petani:</span>
        <span class="stat-value">{{ filteredPetani.length }}</span>
      </div>
      <div class="stat-item" v-if="filterKabupaten">
        <span class="stat-label">{{ filterKabupaten }}:</span>
        <span class="stat-value">{{ filteredPetani.length }}</span>
      </div>
    </div>

    <!-- Table dengan style baru -->
    <!-- Column Picker -->
    <div class="col-picker-bar">
      <div class="col-picker-wrapper">
        <div v-if="showColPicker" class="col-picker-overlay" @click="showColPicker = false"></div>
        <button @click="showColPicker = !showColPicker" class="btn-col-picker" :class="{ active: showColPicker }">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
            <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
          </svg>
          Tampilkan Kolom
          <svg class="chevron-icon" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="6 9 12 15 18 9"/>
          </svg>
        </button>
        <div v-if="showColPicker" class="col-picker-dropdown">
          <div class="col-picker-header">
            <span>Pilih Kolom Tampil</span>
          </div>
          <label v-for="col in colDefs" :key="col.key" class="col-picker-item" :class="{ 'col-active': visibleCols[col.key] }">
            <span class="col-check-box">
              <svg v-if="visibleCols[col.key]" xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
            </span>
            <input type="checkbox" v-model="visibleCols[col.key]" style="display:none" />
            <span class="col-label">{{ col.label }}</span>
          </label>
        </div>
      </div>
    </div>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th v-if="visibleCols.tanggal">Tanggal</th>
            <th v-if="visibleCols.nik">NIK</th>
            <th v-if="visibleCols.nama">Nama</th>
            <th v-if="visibleCols.luas_lahan">Luas Lahan</th>
            <th v-if="visibleCols.alamat_lahan">Alamat Lahan</th>
            <th v-if="visibleCols.potensi_panen">Potensi Panen</th>
            <th v-if="visibleCols.komoditi">Komoditi</th>
            <th v-if="visibleCols.alamat">Alamat</th>
            <th v-if="visibleCols.provinsi_id">Provinsi</th>
            <th v-if="visibleCols.kabupaten_id">Kabupaten</th>
            <th v-if="visibleCols.kecamatan_id">Kecamatan</th>
            <th v-if="visibleCols.kalurahan_id">Kalurahan</th>
            <th v-if="visibleCols.no_telepon">No Telepon</th>
            <th v-if="visibleCols.bank">Bank</th>
            <th v-if="visibleCols.no_rekening">No Rekening</th>
            <th v-if="visibleCols.status_verifikasi">Status Verifikasi</th>
            <th v-if="visibleCols.catatan_verifikasi">Catatan Verifikasi</th>
            <th>Aksi</th>
            <th>Verifikasi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td :colspan="colSpan" class="loading-cell">Loading...</td>
          </tr>
          <tr v-else-if="filteredPetani.length === 0">
            <td :colspan="colSpan" class="empty-cell">Tidak ada data petani</td>
          </tr>
          <tr v-else v-for="(petani, index) in filteredPetani" :key="petani.id">
            <td>{{ index + 1 }}</td>
            <td v-if="visibleCols.tanggal">{{ formatDate(petani.tanggal) }}</td>
            <td v-if="visibleCols.nik">{{ petani.nik }}</td>
            <td v-if="visibleCols.nama">{{ petani.nama }}</td>
            <td v-if="visibleCols.luas_lahan">{{ formatNumber(petani.luas_lahan) }} HA</td>
            <td v-if="visibleCols.alamat_lahan">{{ petani.alamat_lahan || '-' }}</td>
            <td v-if="visibleCols.potensi_panen">{{ formatNumber(petani.potensi_panen) }} KG</td>
            <td v-if="visibleCols.komoditi" class="text-center">
              <span class="badge" :class="`badge-${petani.komoditi.toLowerCase()}`">
                {{ petani.komoditi }}
              </span>
            </td>
            <td v-if="visibleCols.alamat">{{ petani.alamat || '-' }}</td>
            <td v-if="visibleCols.provinsi_id">{{ petani.provinsi?.nama || '-' }}</td>
            <td v-if="visibleCols.kabupaten_id">{{ petani.kabupaten?.nama || '-' }}</td>
            <td v-if="visibleCols.kecamatan_id">{{ petani.kecamatan?.nama || '-' }}</td>
            <td v-if="visibleCols.kalurahan_id">{{ petani.kalurahan?.nama || '-' }}</td>
            <td v-if="visibleCols.no_telepon">{{ petani.no_telepon || '-' }}</td>
            <td v-if="visibleCols.bank">{{ petani.bank || '-' }}</td>
            <td v-if="visibleCols.no_rekening">{{ petani.no_rekening || '-' }}</td>
            <td v-if="visibleCols.status_verifikasi" class="text-center">
              <span :class="['badge-status', 'badge-' + petani.status_verifikasi]">
                {{ petani.status_verifikasi === 'disetujui' ? 'Disetujui' : petani.status_verifikasi === 'ditolak' ? 'Ditolak' : 'Pending' }}
              </span>
            </td>
            <td v-if="visibleCols.catatan_verifikasi">{{ petani.catatan_verifikasi || '-' }}</td>
            <td class="action-buttons">
              <button @click="viewDetail(petani)" class="btn-view" title="Detail">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
              <button v-if="authStore.canManagePetani" @click="editPetani(petani)" class="btn-edit" title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
              </button>
              <button v-if="authStore.canManagePetani" @click="deletePetani(petani.id)" class="btn-delete" title="Hapus">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="3 6 5 6 21 6"/>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
              </button>
            </td>
            <td class="verifikasi-cell">
              <button v-if="authStore.canVerify" @click="openVerifikasiModal(petani)" class="btn-verify" title="Verifikasi">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                  <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
              </button>
              <span v-else class="no-verify">-</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Add/Edit -->
    <div v-if="showAddModal || showEditModal" class="modal" @click.self="closeModal">
      <div class="modal-content large-modal">
        <div class="modal-header">
          <h3>{{ showEditModal ? 'Edit Data Petani' : 'Tambah Data Petani Baru' }}</h3>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>
        <form @submit.prevent="submitForm" class="petani-form">
          <div class="form-section">
            <h4>
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              Informasi Umum
            </h4>
            <div class="form-row">
              <div class="form-group">
                <label>Tanggal Penambahan Data *</label>
                <input v-model="form.tanggal" type="date" required />
              </div>
              <div class="form-group">
                <label>NIK * <span class="nik-status" :class="nikStatus.class">{{ nikStatus.message }}</span></label>
                <input v-model="form.nik" type="text" maxlength="16" required @input="checkNikDuplicate" />
              </div>
            </div>
            <div class="form-group">
              <label>Nama Lengkap *</label>
              <input v-model="form.nama" type="text" required />
            </div>
          </div>

          <div class="form-section">
            <h4>
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              Alamat & Wilayah
            </h4>
            <div class="form-group">
              <label>Alamat Lengkap *</label>
              <textarea v-model="form.alamat" rows="2" required></textarea>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Provinsi</label>
                <select v-model="form.provinsi_id" @change="onProvinsiChange">
                  <option value="">Pilih Provinsi</option>
                  <option v-for="prov in provinsiList" :key="prov.id" :value="prov.id">
                    {{ prov.nama }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Kabupaten</label>
                <select v-model="form.kabupaten_id" @change="onKabupatenChange" :disabled="!form.provinsi_id">
                  <option value="">Pilih Kabupaten</option>
                  <option v-for="kab in kabupatenOptions" :key="kab.id" :value="kab.id">
                    {{ kab.nama }}
                  </option>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Kecamatan</label>
                <select v-model="form.kecamatan_id" @change="onKecamatanChange" :disabled="!form.kabupaten_id">
                  <option value="">Pilih Kecamatan</option>
                  <option v-for="kec in kecamatanOptions" :key="kec.id" :value="kec.id">
                    {{ kec.nama }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Kalurahan/Desa</label>
                <select v-model="form.kalurahan_id" :disabled="!form.kecamatan_id">
                  <option value="">Pilih Kalurahan/Desa</option>
                  <option v-for="kal in kalurahanOptions" :key="kal.id" :value="kal.id">
                    {{ kal.nama }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h4>
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
              Kontak & Bank
            </h4>
            <div class="form-row">
              <div class="form-group">
                <label>No. Telepon</label>
                <input v-model="form.no_telepon" type="text" maxlength="15" />
              </div>
              <div class="form-group">
                <label>Bank</label>
                <input v-model="form.bank" type="text" placeholder="Contoh: BRI, BCA, Mandiri" />
              </div>
              <div class="form-group">
                <label>No. Rekening</label>
                <input v-model="form.no_rekening" type="text" />
              </div>
            </div>
          </div>

          <div class="form-section">
            <h4>
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 12h20"/>
                <path d="M6 8v8"/>
                <path d="M10 8v8"/>
                <path d="M14 8v8"/>
                <path d="M18 8v8"/>
                <path d="M2 16h20"/>
                <path d="M2 8h20"/>
              </svg>
              Informasi Lahan & Panen
            </h4>
            <div class="form-group">
              <label>Alamat Lahan *</label>
              <textarea v-model="form.alamat_lahan" rows="2" required></textarea>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Luas Lahan (Hektar/HA) *</label>
                <input v-model="form.luas_lahan" type="number" step="0.01" min="0" required />
              </div>
              <div class="form-group">
                <label>Potensi Panen (KG) *</label>
                <input v-model="form.potensi_panen" type="number" step="0.01" min="0" required />
              </div>
              <div class="form-group">
                <label>Komoditi *</label>
                <select v-model="form.komoditi" required>
                  <option value="">Pilih Komoditi</option>
                  <option value="Gabah">Gabah</option>
                  <option value="Jagung">Jagung</option>
                  <option value="Beras">Beras</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h4>
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
              </svg>
              Upload Foto & Dokumen
            </h4>
            <div class="form-row">
              <div class="form-group">
                <label>Foto KTP</label>
                <input type="file" @change="handleFileUpload($event, 'foto_ktp')" accept="image/*" />
                <img v-if="previews.foto_ktp" :src="previews.foto_ktp" class="preview-image" />
              </div>
              <div class="form-group">
                <label>Foto Petani</label>
                <input type="file" @change="handleFileUpload($event, 'foto_petani')" accept="image/*" />
                <img v-if="previews.foto_petani" :src="previews.foto_petani" class="preview-image" />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Foto Komoditi</label>
                <input type="file" @change="handleFileUpload($event, 'foto_komoditi')" accept="image/*" />
                <img v-if="previews.foto_komoditi" :src="previews.foto_komoditi" class="preview-image" />
              </div>
              <div class="form-group">
                <label>Kwitansi Pembayaran</label>
                <input type="file" @change="handleFileUpload($event, 'kwitansi_pembayaran')" accept="image/*,application/pdf" />
                <img v-if="previews.kwitansi_pembayaran && !previews.kwitansi_pembayaran.includes('.pdf')" :src="previews.kwitansi_pembayaran" class="preview-image" />
                <p v-if="previews.kwitansi_pembayaran && previews.kwitansi_pembayaran.includes('.pdf')" class="pdf-indicator">
                  <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                  </svg>
                  PDF telah dipilih
                </p>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Surat Pernyataan (Opsional)</label>
                <input type="file" @change="handleFileUpload($event, 'surat_pernyataan')" accept="image/*" />
                <img v-if="previews.surat_pernyataan" :src="previews.surat_pernyataan" class="preview-image" />
                <small class="help-text">Surat pernyataan yang telah ditandatangani petugas</small>
              </div>
            </div>
            <small class="help-text">* Format: JPG, PNG, PDF. Max: 5MB (akan dikompres otomatis)</small>
          </div>

          <div class="modal-footer">
            <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
            <button type="submit" class="btn-primary" :disabled="loading || nikStatus.isDuplicate">
              {{ loading ? 'Menyimpan...' : 'Simpan Data' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Detail -->
    <div v-if="showDetailModal" class="modal" @click.self="showDetailModal = false">
      <div class="modal-content large-modal">
        <div class="modal-header">
          <h3>Detail Data Petani</h3>
          <button @click="showDetailModal = false" class="close-btn">&times;</button>
        </div>
        <div class="detail-content" v-if="selectedPetani">
          <div class="detail-section">
            <h4>
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              Informasi Umum
            </h4>
            <div class="info-grid">
              <div class="info-item">
                <strong>Tanggal Penambahan Data:</strong>
                <span>{{ formatDate(selectedPetani.tanggal) }}</span>
              </div>
              <div class="info-item">
                <strong>NIK:</strong>
                <span>{{ selectedPetani.nik }}</span>
              </div>
              <div class="info-item">
                <strong>Nama:</strong>
                <span>{{ selectedPetani.nama }}</span>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h4>
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              Alamat & Wilayah
            </h4>
            <div class="info-grid">
              <div class="info-item full-width">
                <strong>Alamat:</strong>
                <span>{{ selectedPetani.alamat }}</span>
              </div>
              <div class="info-item">
                <strong>Provinsi:</strong>
                <span>{{ selectedPetani.provinsi?.nama || '-' }}</span>
              </div>
              <div class="info-item">
                <strong>Kabupaten:</strong>
                <span>{{ selectedPetani.kabupaten?.nama || '-' }}</span>
              </div>
              <div class="info-item">
                <strong>Kecamatan:</strong>
                <span>{{ selectedPetani.kecamatan?.nama || '-' }}</span>
              </div>
              <div class="info-item">
                <strong>Kalurahan/Desa:</strong>
                <span>{{ selectedPetani.kalurahan?.nama || '-' }}</span>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h4>
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
              Kontak & Bank
            </h4>
            <div class="info-grid">
              <div class="info-item">
                <strong>No. Telepon:</strong>
                <span>{{ selectedPetani.no_telepon || '-' }}</span>
              </div>
              <div class="info-item">
                <strong>Bank:</strong>
                <span>{{ selectedPetani.bank || '-' }}</span>
              </div>
              <div class="info-item">
                <strong>No. Rekening:</strong>
                <span>{{ selectedPetani.no_rekening || '-' }}</span>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h4>
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 12h20"/>
                <path d="M6 8v8"/>
                <path d="M10 8v8"/>
                <path d="M14 8v8"/>
                <path d="M18 8v8"/>
                <path d="M2 16h20"/>
                <path d="M2 8h20"/>
              </svg>
              Informasi Lahan & Panen
            </h4>
            <div class="info-grid">
              <div class="info-item full-width">
                <strong>Alamat Lahan:</strong>
                <span>{{ selectedPetani.alamat_lahan }}</span>
              </div>
              <div class="info-item">
                <strong>Luas Lahan:</strong>
                <span>{{ formatNumber(selectedPetani.luas_lahan) }} HA</span>
              </div>
              <div class="info-item">
                <strong>Potensi Panen:</strong>
                <span>{{ formatNumber(selectedPetani.potensi_panen) }} KG</span>
              </div>
              <div class="info-item">
                <strong>Komoditi:</strong>
                <span class="badge" :class="`badge-${selectedPetani.komoditi.toLowerCase()}`">
                  {{ selectedPetani.komoditi }}
                </span>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h4>
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
              </svg>
              Foto & Dokumen
            </h4>
            <div class="photo-grid">
              <div class="photo-item" v-if="selectedPetani.foto_ktp">
                <label>Foto KTP</label>
                <img :src="getImageUrl(selectedPetani.foto_ktp)" alt="Foto KTP" @click="openImage(getImageUrl(selectedPetani.foto_ktp))" />
              </div>
              <div class="photo-item" v-if="selectedPetani.foto_petani">
                <label>Foto Petani</label>
                <img :src="getImageUrl(selectedPetani.foto_petani)" alt="Foto Petani" @click="openImage(getImageUrl(selectedPetani.foto_petani))" />
              </div>
              <div class="photo-item" v-if="selectedPetani.foto_komoditi">
                <label>Foto Komoditi</label>
                <img :src="getImageUrl(selectedPetani.foto_komoditi)" alt="Foto Komoditi" @click="openImage(getImageUrl(selectedPetani.foto_komoditi))" />
              </div>
              <div class="photo-item" v-if="selectedPetani.kwitansi_pembayaran">
                <label>Kwitansi Pembayaran</label>
                <img :src="getImageUrl(selectedPetani.kwitansi_pembayaran)" alt="Kwitansi" @click="openImage(getImageUrl(selectedPetani.kwitansi_pembayaran))" />
              </div>
              <div class="photo-item" v-if="selectedPetani.surat_pernyataan">
                <label>Surat Pernyataan</label>
                <img :src="getImageUrl(selectedPetani.surat_pernyataan)" alt="Surat Pernyataan" @click="openImage(getImageUrl(selectedPetani.surat_pernyataan))" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Verifikasi -->
    <div v-if="showVerifikasiModal" class="modal" @click.self="showVerifikasiModal = false">
      <div class="modal-content modal-verifikasi">
        <div class="modal-header">
          <h3>Verifikasi Data Petani</h3>
          <button @click="showVerifikasiModal = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <p class="verifikasi-info">
            <strong>{{ verifikasiItem?.nama }}</strong><br>
            <span>NIK: {{ verifikasiItem?.nik }}</span>
          </p>
          <div class="form-group">
            <label>Status Verifikasi <span class="required">*</span></label>
            <select v-model="verifikasiForm.status_verifikasi" class="select-status">
              <option value="pending">Pending</option>
              <option value="disetujui">Disetujui</option>
              <option value="ditolak">Ditolak</option>
            </select>
          </div>
          <div class="form-group">
            <label>Catatan</label>
            <textarea
              v-model="verifikasiForm.catatan_verifikasi"
              rows="3"
              placeholder="Catatan verifikasi (opsional)..."
              class="textarea-catatan"
            ></textarea>
          </div>
          <div class="modal-footer-buttons">
            <button @click="showVerifikasiModal = false" class="btn-cancel">Batal</button>
            <button @click="submitVerifikasi" class="btn-submit-verifikasi" :disabled="verifikasiLoading">
              {{ verifikasiLoading ? 'Menyimpan...' : 'Simpan Verifikasi' }}
            </button>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api, { getStorageUrl } from '@/services/api'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const petaniList = ref([])
const searchQuery = ref('')
const filterKabupaten = ref('')
const filterTanggalDari = ref('')
const filterTanggalSampai = ref('')
const showAddModal = ref(false)
const showEditModal = ref(false)
const showDetailModal = ref(false)
const selectedPetani = ref(null)
const loading = ref(false)

const showColPicker = ref(false)
const colDefs = [
  { key: 'tanggal', label: 'Tanggal' },
  { key: 'nik', label: 'NIK' },
  { key: 'nama', label: 'Nama' },
  { key: 'luas_lahan', label: 'Luas Lahan' },
  { key: 'alamat_lahan', label: 'Alamat Lahan' },
  { key: 'potensi_panen', label: 'Potensi Panen' },
  { key: 'komoditi', label: 'Komoditi' },
  { key: 'alamat', label: 'Alamat' },
  { key: 'provinsi_id', label: 'Provinsi' },
  { key: 'kabupaten_id', label: 'Kabupaten' },
  { key: 'kecamatan_id', label: 'Kecamatan' },
  { key: 'kalurahan_id', label: 'Kalurahan' },
  { key: 'no_telepon', label: 'No Telepon' },
  { key: 'bank', label: 'Bank' },
  { key: 'no_rekening', label: 'No Rekening' },
  { key: 'status_verifikasi', label: 'Status Verifikasi' },
  { key: 'catatan_verifikasi', label: 'Catatan Verifikasi' },
]
const visibleCols = ref({
  tanggal: false, nik: true, nama: true, luas_lahan: false, alamat_lahan: false,
  potensi_panen: false, komoditi: true, alamat: false, provinsi_id: false,
  kabupaten_id: true, kecamatan_id: false, kalurahan_id: false,
  no_telepon: false, bank: false, no_rekening: false,
  status_verifikasi: true, catatan_verifikasi: false
})
const colSpan = computed(() => 3 + Object.values(visibleCols.value).filter(Boolean).length)

const showVerifikasiModal = ref(false)
const verifikasiItem = ref(null)
const verifikasiLoading = ref(false)
const verifikasiForm = ref({
  status_verifikasi: 'pending',
  catatan_verifikasi: ''
})
const kabupatenList = computed(() => allKabupaten.value.map(k => k.nama))
const nikCheckTimeout = ref(null)
const nikStatus = ref({ message: '', class: '', isDuplicate: false })

// Wilayah data
const provinsiList = ref([])
const allKabupaten = ref([])
const allKecamatan = ref([])
const allKalurahan = ref([])

// Filtered options based on parent selection
const kabupatenOptions = ref([])
const kecamatanOptions = ref([])
const kalurahanOptions = ref([])

const form = ref({
  tanggal: new Date().toISOString().split('T')[0],
  nik: '',
  nama: '',
  alamat: '',
  provinsi_id: '',
  kabupaten_id: '',
  kecamatan_id: '',
  kalurahan_id: '',
  no_telepon: '',
  bank: '',
  no_rekening: '',
  luas_lahan: '',
  alamat_lahan: '',
  potensi_panen: '',
  komoditi: '',
  foto_ktp: null,
  foto_petani: null,
  foto_komoditi: null,
  kwitansi_pembayaran: null,
  surat_pernyataan: null
})

const previews = ref({
  foto_ktp: null,
  foto_petani: null,
  foto_komoditi: null,
  kwitansi_pembayaran: null,
  surat_pernyataan: null
})

const filteredPetani = computed(() => {
  let result = petaniList.value
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(p => 
      p.nama.toLowerCase().includes(query) || 
      p.nik.includes(query)
    )
  }
  
  return result
})

const fetchPetani = async () => {
  try {
    console.log('Fetching petani data...')
    const response = await api.get('/petani')
    console.log('Response:', response)
    petaniList.value = response.data.data
  } catch (error) {
    console.error('Error fetching petani:', error)
    alert('Gagal mengambil data petani')
  }
}

const applyFilter = async () => {
  try {
    const params = {}
    if (filterKabupaten.value) params.kabupaten = filterKabupaten.value
    if (filterTanggalDari.value) params.tanggal_dari = filterTanggalDari.value
    if (filterTanggalSampai.value) params.tanggal_sampai = filterTanggalSampai.value
    
    const response = await api.get('/petani', { params })
    petaniList.value = response.data.data
  } catch (error) {
    alert('Gagal menerapkan filter')
  }
}

const resetFilter = () => {
  filterKabupaten.value = ''
  filterTanggalDari.value = ''
  filterTanggalSampai.value = ''
  searchQuery.value = ''
  fetchPetani()
}

const checkNikDuplicate = () => {
  if (nikCheckTimeout.value) clearTimeout(nikCheckTimeout.value)
  
  const nik = form.value.nik
  
  if (nik.length < 16) {
    nikStatus.value = { message: '', class: '', isDuplicate: false }
    return
  }

  nikStatus.value = { message: 'Mengecek...', class: 'checking', isDuplicate: false }
  
  nikCheckTimeout.value = setTimeout(async () => {
    try {
      const existingPetani = petaniList.value.find(p => 
        p.nik === nik && (!selectedPetani.value || p.id !== selectedPetani.value.id)
      )
      
      if (existingPetani) {
        nikStatus.value = { 
          message: `⚠ NIK sudah terdaftar atas nama ${existingPetani.nama}`, 
          class: 'duplicate', 
          isDuplicate: true 
        }
      } else {
        nikStatus.value = { message: '✓ NIK tersedia', class: 'available', isDuplicate: false }
      }
    } catch (error) {
      nikStatus.value = { message: '', class: '', isDuplicate: false }
    }
  }, 500)
}

const handleFileUpload = (event, fieldName) => {
  const file = event.target.files[0]
  if (file) {
    form.value[fieldName] = file
    const reader = new FileReader()
    reader.onload = (e) => {
      previews.value[fieldName] = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const submitForm = async () => {
  if (nikStatus.value.isDuplicate) {
    alert('NIK sudah terdaftar! Gunakan NIK yang berbeda.')
    return
  }

  loading.value = true
  try {
    const formData = new FormData()
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== null && form.value[key] !== '') {
        formData.append(key, form.value[key])
      }
    })

    if (showEditModal.value) {
      formData.append('_method', 'PUT')
      await api.post(`/petani/${selectedPetani.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      alert('Data petani berhasil diupdate')
    } else {
      await api.post('/petani', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      alert('Data petani berhasil ditambahkan')
    }

    closeModal()
    fetchPetani()
  } catch (error) {
    alert(error.response?.data?.message || 'Gagal menyimpan data')
  } finally {
    loading.value = false
  }
}

const editPetani = (petani) => {
  selectedPetani.value = petani
  form.value = {
    tanggal: petani.tanggal,
    nik: petani.nik,
    nama: petani.nama,
    alamat: petani.alamat,
    provinsi_id: petani.provinsi_id || '',
    kabupaten_id: petani.kabupaten_id || '',
    kecamatan_id: petani.kecamatan_id || '',
    kalurahan_id: petani.kalurahan_id || '',
    no_telepon: petani.no_telepon,
    bank: petani.bank,
    no_rekening: petani.no_rekening,
    luas_lahan: petani.luas_lahan,
    alamat_lahan: petani.alamat_lahan,
    potensi_panen: petani.potensi_panen,
    komoditi: petani.komoditi,
    foto_ktp: null,
    foto_petani: null,
    foto_komoditi: null,
    kwitansi_pembayaran: null,
    surat_pernyataan: null
  }
  
  // Load cascading options
  if (petani.provinsi_id) {
    kabupatenOptions.value = allKabupaten.value.filter(k => k.provinsi_id == petani.provinsi_id)
  }
  if (petani.kabupaten_id) {
    kecamatanOptions.value = allKecamatan.value.filter(k => k.kabupaten_id == petani.kabupaten_id)
  }
  if (petani.kecamatan_id) {
    kalurahanOptions.value = allKalurahan.value.filter(k => k.kecamatan_id == petani.kecamatan_id)
  }
  
  // Set previews for existing images
  if (petani.foto_ktp) previews.value.foto_ktp = getImageUrl(petani.foto_ktp)
  if (petani.foto_petani) previews.value.foto_petani = getImageUrl(petani.foto_petani)
  if (petani.foto_komoditi) previews.value.foto_komoditi = getImageUrl(petani.foto_komoditi)
  if (petani.kwitansi_pembayaran) previews.value.kwitansi_pembayaran = getImageUrl(petani.kwitansi_pembayaran)
  if (petani.surat_pernyataan) previews.value.surat_pernyataan = getImageUrl(petani.surat_pernyataan)
  
  showEditModal.value = true
}

const deletePetani = async (id) => {
  if (!confirm('Yakin ingin menghapus data petani ini?')) return
  
  try {
    await api.delete(`/petani/${id}`)
    alert('Data petani berhasil dihapus')
    fetchPetani()
  } catch (error) {
    alert('Gagal menghapus data petani')
  }
}

const viewDetail = (petani) => {
  selectedPetani.value = petani
  showDetailModal.value = true
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  selectedPetani.value = null
  nikStatus.value = { message: '', class: '', isDuplicate: false }
  previews.value = {
    foto_ktp: null,
    foto_petani: null,
    foto_komoditi: null,
    kwitansi_pembayaran: null,
    surat_pernyataan: null
  }
  form.value = {
    tanggal: new Date().toISOString().split('T')[0],
    nik: '',
    nama: '',
    alamat: '',
    provinsi_id: '',
    kabupaten_id: '',
    kecamatan_id: '',
    kalurahan_id: '',
    no_telepon: '',
    bank: '',
    no_rekening: '',
    luas_lahan: '',
    alamat_lahan: '',
    potensi_panen: '',
    komoditi: '',
    foto_ktp: null,
    foto_petani: null,
    foto_komoditi: null,
    kwitansi_pembayaran: null,
    surat_pernyataan: null
  }
  kabupatenOptions.value = []
  kecamatanOptions.value = []
  kalurahanOptions.value = []
}

const exportExcel = async () => {
  try {
    loading.value = true
    const params = {}
    if (filterKabupaten.value) params.kabupaten = filterKabupaten.value
    if (filterTanggalDari.value) params.tanggal_dari = filterTanggalDari.value
    if (filterTanggalSampai.value) params.tanggal_sampai = filterTanggalSampai.value
    
    const response = await api.get('/petani/export/excel', {
      params,
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `data_petani_${new Date().getTime()}.xlsx`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    
    alert('Data berhasil diexport!')
  } catch (error) {
    alert('Gagal export data')
  } finally {
    loading.value = false
  }
}

const getImageUrl = (path) => getStorageUrl(path)

const openImage = (url) => {
  window.open(url, '_blank')
}

const openVerifikasiModal = (petani) => {
  verifikasiItem.value = petani
  verifikasiForm.value = {
    status_verifikasi: petani.status_verifikasi || 'pending',
    catatan_verifikasi: petani.catatan_verifikasi || ''
  }
  showVerifikasiModal.value = true
}

const submitVerifikasi = async () => {
  if (!verifikasiItem.value) return
  verifikasiLoading.value = true
  try {
    await api.post(`/petani/${verifikasiItem.value.id}/verifikasi`, verifikasiForm.value)
    alert('Status verifikasi berhasil disimpan')
    showVerifikasiModal.value = false
    fetchPetani()
  } catch (error) {
    console.error('Error verifikasi:', error)
    alert(error.response?.data?.message || 'Gagal menyimpan verifikasi')
  } finally {
    verifikasiLoading.value = false
  }
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
}

const formatNumber = (value) => {
  if (!value) return '0'
  const num = parseFloat(value)
  // Jika angka bulat, tampilkan tanpa desimal
  // Jika ada desimal, tampilkan dengan desimal
  return num % 1 === 0 ? num.toString() : num.toFixed(2)
}

// Wilayah functions
const loadWilayahData = async () => {
  try {
    const [provRes, kabRes, kecRes, kalRes] = await Promise.all([
      api.get('/provinsi'),
      api.get('/kabupaten'),
      api.get('/kecamatan'),
      api.get('/kalurahan')
    ])
    
    provinsiList.value = provRes.data.data
    allKabupaten.value = kabRes.data.data
    allKecamatan.value = kecRes.data.data
    allKalurahan.value = kalRes.data.data
  } catch (error) {
    console.error('Error loading wilayah:', error)
  }
}

const onProvinsiChange = () => {
  form.value.kabupaten_id = ''
  form.value.kecamatan_id = ''
  form.value.kalurahan_id = ''
  
  if (form.value.provinsi_id) {
    kabupatenOptions.value = allKabupaten.value.filter(k => k.provinsi_id == form.value.provinsi_id)
  } else {
    kabupatenOptions.value = []
  }
  kecamatanOptions.value = []
  kalurahanOptions.value = []
}

const onKabupatenChange = () => {
  form.value.kecamatan_id = ''
  form.value.kalurahan_id = ''
  
  if (form.value.kabupaten_id) {
    kecamatanOptions.value = allKecamatan.value.filter(k => k.kabupaten_id == form.value.kabupaten_id)
  } else {
    kecamatanOptions.value = []
  }
  kalurahanOptions.value = []
}

const onKecamatanChange = () => {
  form.value.kalurahan_id = ''
  
  if (form.value.kecamatan_id) {
    kalurahanOptions.value = allKalurahan.value.filter(k => k.kecamatan_id == form.value.kecamatan_id)
  } else {
    kalurahanOptions.value = []
  }
}

onMounted(() => {
  fetchPetani()
  loadWilayahData()
})
</script>

<style scoped>
.petani-list {
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.header h2 {
  margin: 0;
  color: #2e7d32;
}

.header-actions {
  display: flex;
  gap: 10px;
}

.filter-section {
  background: white;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-row {
  display: flex;
  gap: 15px;
  margin-bottom: 15px;
  flex-wrap: wrap;
}

.filter-row:last-child {
  margin-bottom: 0;
}

.search-input {
  flex: 1;
  min-width: 300px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 14px;
}

.filter-select {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 14px;
  min-width: 200px;
}

.date-filter {
  display: flex;
  align-items: center;
  gap: 10px;
}

.date-filter label {
  font-weight: 500;
  color: #495057;
}

.date-input {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 14px;
}

.btn-filter {
  background: #1565c0;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
}

.btn-reset {
  background: #6c757d;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
}

.btn-success {
  background: #1565c0;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
}

.btn-primary {
  background: #1565c0;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
}

.stats-bar {
  display: flex;
  gap: 20px;
  background: linear-gradient(135deg, #1565c0 0%, #42a5f5 100%);
  padding: 15px 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  color: white;
}

.stat-item {
  display: flex;
  gap: 10px;
  align-items: center;
}

.stat-label {
  font-weight: 500;
}

.stat-value {
  font-size: 1.3rem;
  font-weight: 700;
}

.badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.badge-gabah {
  background: #fff3cd;
  color: #856404;
}

.badge-jagung {
  background: #cce5ff;
  color: #004085;
}

.badge-beras {
  background: #d4edda;
  color: #155724;
}

.nik-status {
  font-size: 12px;
  margin-left: 10px;
}

.nik-status.checking {
  color: #17a2b8;
}

.nik-status.available {
  color: #28a745;
}

.nik-status.duplicate {
  color: #dc3545;
  font-weight: 600;
}

/* Table styling yang baru - mengikuti PenggilinganView */
.table-wrapper {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  overflow-x: auto;
  border: 1px solid #e8ecef;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: linear-gradient(135deg, #1565c0 0%, #42a5f5 100%);
  color: white;
}

thead th {
  padding: 13px 14px;
  font-weight: 600;
  font-size: 13px;
  letter-spacing: 0.03em;
  white-space: nowrap;
  border-right: 1px solid rgba(255,255,255,0.15);
  border-bottom: none;
}

thead th:last-child {
  border-right: none;
}

th,
td {
  padding: 11px 14px;
  text-align: left;
}

th {
  font-weight: 600;
  font-size: 13px;
}

td {
  font-size: 13.5px;
  color: #374151;
  border-bottom: 1px solid #edf0f3;
  border-right: 1px solid #edf0f3;
}

td:last-child {
  border-right: none;
}

tbody tr {
  transition: background 0.15s;
}

tbody tr:nth-child(even) {
  background: #f0f7ff;
}

tbody tr:hover {
  background: #dceeff;
}

tbody tr:last-child td {
  border-bottom: none;
}

.text-right {
  text-align: right;
}

.text-center {
  text-align: center;
}

.loading-cell,
.empty-cell {
  text-align: center;
  padding: 40px;
  color: #7f8c8d;
}

.action-buttons {
  display: flex;
  gap: 8px;
  justify-content: center;
}

.verifikasi-cell {
  text-align: center;
  white-space: nowrap;
}

.no-verify {
  color: #aaa;
  font-size: 13px;
}

.btn-view,
.btn-edit,
.btn-delete {
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-view {
  background: #3498db;
  color: white;
}

.btn-view:hover {
  background: #2980b9;
}

.btn-edit {
  background: #f39c12;
  color: white;
}

.btn-edit:hover {
  background: #d68910;
}

.btn-delete {
  background: #e74c3c;
  color: white;
}

.btn-delete:hover {
  background: #c0392b;
}

.btn-view svg,
.btn-edit svg,
.btn-delete svg {
  width: 16px;
  height: 16px;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  overflow-y: auto;
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 8px;
  max-width: 700px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  margin: 20px;
}

.large-modal {
  max-width: 900px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  border-bottom: 2px solid #eee;
  padding-bottom: 15px;
}

.modal-header h3 {
  margin: 0;
  color: #2e7d32;
}

.close-btn {
  background: none;
  border: none;
  font-size: 30px;
  cursor: pointer;
  color: #6c757d;
  line-height: 1;
}

.petani-form .form-section {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.petani-form .form-section h4 {
  margin: 0 0 15px 0;
  color: #495057;
  font-size: 1.1rem;
  border-bottom: 2px solid #dee2e6;
  padding-bottom: 10px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
  color: #495057;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 14px;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.preview-image {
  margin-top: 10px;
  max-width: 150px;
  max-height: 150px;
  border-radius: 5px;
  cursor: pointer;
}

.help-text {
  display: block;
  margin-top: 10px;
  color: #6c757d;
  font-size: 12px;
}

.pdf-indicator {
  color: #28a745;
  font-weight: 600;
  margin-top: 10px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 2px solid #eee;
}

.detail-content {
  padding: 10px 0;
}

.detail-section {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.detail-section h4 {
  margin: 0 0 15px 0;
  color: #495057;
  font-size: 1.1rem;
  border-bottom: 2px solid #dee2e6;
  padding-bottom: 10px;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 15px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.info-item.full-width {
  grid-column: 1 / -1;
}

.info-item strong {
  color: #6c757d;
  font-size: 0.9rem;
}

.info-item span {
  color: #212529;
  font-size: 1rem;
}

.photo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 15px;
}

.photo-item {
  text-align: center;
}

.photo-item label {
  display: block;
  margin-bottom: 10px;
  font-weight: 600;
  color: #495057;
}

.photo-item img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
  transition: transform 0.2s;
}

.photo-item img:hover {
  transform: scale(1.05);
}

/* Hero Banner */
.hero-banner {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  background: linear-gradient(135deg, #1565c0 0%, #42a5f5 100%);
  color: white;
  padding: 1.25rem 1.75rem;
  border-radius: 12px 12px 0 0;
  margin: -20px -20px 20px -20px;
}

.page-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
  padding: 20px;
}

.hero-icon svg {
  width: 48px;
  height: 48px;
  opacity: 0.9;
  flex-shrink: 0;
}

.hero-text h2 {
  margin: 0 0 4px 0;
  font-size: 1.5rem;
  color: white;
}

.hero-text p {
  margin: 0;
  opacity: 0.85;
  font-size: 0.9rem;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .table-wrapper {
    overflow-x: auto;
  }
  
  table {
    min-width: 900px;
  }
}

@media (max-width: 768px) {
  .petani-list {
    padding: 8px;
  }

  .hero-banner {
    padding: 1rem;
    gap: 0.75rem;
    margin: -12px -12px 12px -12px;
  }

  .page-card {
    padding: 12px;
  }

  .hero-icon svg {
    width: 36px;
    height: 36px;
  }

  .hero-text h2 {
    font-size: 1.1rem;
  }

  .hero-text p {
    font-size: 0.8rem;
  }

  .header {
    flex-direction: column;
    gap: 6px;
    align-items: stretch;
    margin-bottom: 10px;
  }

  .header h2 {
    font-size: 16px;
    margin-bottom: 0;
  }

  .header-actions {
    flex-direction: row;
    flex-wrap: wrap;
    gap: 6px;
    width: 100%;
  }

  .header-actions button {
    flex: 1;
    min-width: 0;
    padding: 7px 10px;
    font-size: 12px;
  }

  .filter-section {
    padding: 10px;
    margin-bottom: 10px;
  }

  .filter-row {
    flex-direction: column;
    gap: 8px;
    margin-bottom: 8px;
  }

  .search-input,
  .filter-select {
    width: 100%;
    min-width: 100%;
    padding: 7px 10px;
    font-size: 13px;
  }

  .date-filter {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .date-filter label {
    font-size: 12px;
    white-space: nowrap;
    min-width: 50px;
  }

  .date-input {
    flex: 1;
    padding: 7px 8px;
    font-size: 12px;
  }

  .btn-filter,
  .btn-reset {
    flex: 1;
    padding: 7px 10px;
    font-size: 12px;
  }

  .stats-bar {
    flex-wrap: wrap;
    gap: 6px;
    padding: 8px 10px;
    font-size: 13px;
  }

  .table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  table {
    min-width: 600px;
  }

  .modal {
    padding: 6px;
  }

  .modal-content {
    width: 98%;
    padding: 12px;
    max-height: 94vh;
    overflow-y: auto;
  }

  .large-modal {
    width: 98%;
    max-height: 95vh;
    overflow-y: auto;
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: 8px;
  }

  .info-grid {
    grid-template-columns: 1fr;
    gap: 8px;
  }

  .photo-grid {
    grid-template-columns: 1fr;
  }

  .detail-section {
    padding: 12px;
    margin-bottom: 12px;
  }

  .modal-footer {
    flex-direction: row;
    flex-wrap: wrap;
    gap: 8px;
  }

  .modal-footer button {
    flex: 1;
    padding: 8px 10px;
    font-size: 13px;
  }
}

.icon-inline {
  width: 18px;
  height: 18px;
  display: inline-block;
  vertical-align: middle;
  margin-right: 6px;
}

.btn-success svg,
.btn-filter svg,
.btn-reset svg {
  width: 16px;
  height: 16px;
  margin-right: 4px;
}
/* Badge Status Verifikasi */
.badge-status {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}
.badge-pending {
  background: #fff3cd;
  color: #856404;
}
.badge-disetujui {
  background: #d1e7dd;
  color: #0a3622;
}
.badge-ditolak {
  background: #f8d7da;
  color: #58151c;
}

/* Tombol Verifikasi */
.btn-verify {
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
  color: #0d6efd;
  border-radius: 4px;
  transition: background 0.2s;
}
.btn-verify:hover {
  background: #e7f0ff;
}
.btn-verify svg {
  width: 16px;
  height: 16px;
}

/* Modal Verifikasi */
.modal-verifikasi {
  max-width: 480px;
  width: 95%;
}
.verifikasi-info {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 12px 16px;
  margin-bottom: 16px;
  font-size: 14px;
  line-height: 1.6;
}
.select-status {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 6px;
  font-size: 14px;
}
.textarea-catatan {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 6px;
  font-size: 14px;
  resize: vertical;
  box-sizing: border-box;
}
.modal-footer-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}
.btn-cancel {
  padding: 8px 18px;
  border: 1px solid #ced4da;
  background: #fff;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}
.btn-submit-verifikasi {
  padding: 8px 18px;
  background: #0d6efd;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
}
.btn-submit-verifikasi:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Column Picker */
.col-picker-bar {
  display: flex;
  justify-content: flex-start;
  margin-bottom: 10px;
}
.col-picker-wrapper { position: relative; }
.btn-col-picker {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 7px 14px;
  background: #fff;
  border: 1.5px solid #1565c0;
  border-radius: 8px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
  color: #1565c0;
  transition: all 0.18s;
  box-shadow: 0 1px 4px rgba(21,101,192,0.08);
}
.btn-col-picker:hover, .btn-col-picker.active {
  background: #1565c0;
  color: #fff;
  box-shadow: 0 2px 8px rgba(21,101,192,0.22);
}
.btn-col-picker.active .chevron-icon {
  transform: rotate(180deg);
}
.col-picker-overlay {
  position: fixed;
  inset: 0;
  z-index: 99;
}
.col-picker-dropdown {
  position: absolute;
  left: 0;
  top: calc(100% + 6px);
  background: #fff;
  border: 1px solid #dee2e6;
  border-radius: 10px;
  box-shadow: 0 6px 24px rgba(0,0,0,0.13);
  z-index: 100;
  min-width: 210px;
  padding: 6px 0 8px 0;
  max-height: 340px;
  overflow-y: auto;
}
.col-picker-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 14px 8px 14px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #495057;
  border-bottom: 1px solid #f0f0f0;
  margin-bottom: 4px;
  background: #f5f8ff;
  border-radius: 10px 10px 0 0;
}
.col-picker-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 7px 14px;
  cursor: pointer;
  font-size: 13px;
  color: #343a40;
  transition: background 0.12s;
  border-radius: 0;
}
.col-picker-item:hover { background: #f5f8ff; }
.col-picker-item.col-active {
  background: #e8f0fe;
  color: #0d47a1;
  font-weight: 600;
}
.col-check-box {
  width: 18px;
  height: 18px;
  border-radius: 5px;
  border: 2px solid #ced4da;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.15s;
}
.col-picker-item.col-active .col-check-box {
  background: #1565c0;
  border-color: #1565c0;
  color: #fff;
}
.col-label {
  flex: 1;
  font-family: monospace;
  font-size: 12.5px;
}
</style>