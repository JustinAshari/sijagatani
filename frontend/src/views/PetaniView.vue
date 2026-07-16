<template>
  <div class="petani-list" style="padding: 20px;">

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

    <!-- Statistics Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon icon-blue">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        <div class="stat-info">
          <h3>Total Petani</h3>
          <p class="stat-val">{{ statistics.totalCount }}</p>
          <span class="stat-sub">Terdaftar/terfilter</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon icon-green">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <line x1="9" y1="3" x2="9" y2="21"/>
            <line x1="15" y1="3" x2="15" y2="21"/>
            <line x1="3" y1="9" x2="21" y2="9"/>
            <line x1="3" y1="15" x2="21" y2="15"/>
          </svg>
        </div>
        <div class="stat-info">
          <h3>Total Luas Lahan</h3>
          <p class="stat-val text-green-val">{{ formatVolume(statistics.totalLahan) }} HA</p>
          <span class="stat-sub">Lahan terdaftar</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon icon-orange">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
        </div>
        <div class="stat-info">
          <h3>Potensi Panen</h3>
          <p class="stat-val text-orange-val">{{ formatVolume(statistics.totalPotensi) }} KG</p>
          <span class="stat-sub">Estimasi hasil panen</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon icon-blue">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/>
            <line x1="12" y1="4" x2="12" y2="20"/>
            <line x1="2" y1="10" x2="22" y2="10"/>
          </svg>
        </div>
        <div class="stat-info">
          <h3>G / B / J</h3>
          <p class="stat-val" style="font-size: 1.15rem; margin-top: 0.1rem;">
            {{ statistics.countGabah }} / {{ statistics.countBeras }} / {{ statistics.countJagung }}
          </p>
          <span class="stat-sub">Jumlah Gabah/Beras/Jagung</span>
        </div>
      </div>
    </div>

    <!-- Main Page Content -->
    <div class="page-card">
      <div class="toolbar">
        <div class="toolbar-left">
          <input
            v-model="searchQuery"
            @keyup.enter="applyFilter"
            type="text"
            placeholder="Cari nama atau NIK petani..."
            class="search-input"
          />
          <FilterDropdown
            :active-count="petaniActiveFilterCount"
            @apply="applyFilter"
            @reset="resetFilter"
          >
            <div class="fd-field">
              <label class="fd-label">Komoditi</label>
              <select v-model="filterKomoditi" class="fd-select">
                <option value="">Semua Komoditi</option>
                <option value="Gabah">Gabah</option>
                <option value="Jagung">Jagung</option>
                <option value="Beras">Beras</option>
              </select>
            </div>

            <div class="fd-field">
              <label class="fd-label">Provinsi</label>
              <select v-model="filterProvinsi" @change="onFilterProvinsiChange" class="fd-select">
                <option value="">Semua Provinsi</option>
                <option v-for="prov in provinsiList" :key="prov.id" :value="prov.id">{{ prov.nama }}</option>
              </select>
            </div>

            <div class="fd-field">
              <label class="fd-label">Kabupaten</label>
              <select v-model="filterKabupatenId" @change="onFilterKabupatenChange" :disabled="!filterProvinsi" class="fd-select">
                <option value="">Semua Kabupaten</option>
                <option v-for="kab in filterKabupatenOptions" :key="kab.id" :value="kab.id">{{ kab.nama }}</option>
              </select>
            </div>

            <div class="fd-field">
              <label class="fd-label">Kecamatan</label>
              <select v-model="filterKecamatan" @change="onFilterKecamatanChange" :disabled="!filterKabupatenId" class="fd-select">
                <option value="">Semua Kecamatan</option>
                <option v-for="kec in filterKecamatanOptions" :key="kec.id" :value="kec.id">{{ kec.nama }}</option>
              </select>
            </div>

            <div class="fd-field">
              <label class="fd-label">Kelurahan/Desa</label>
              <select v-model="filterKalurahan" :disabled="!filterKecamatan" class="fd-select">
                <option value="">Semua Kelurahan/Desa</option>
                <option v-for="kal in filterKalurahanOptions" :key="kal.id" :value="kal.id">{{ kal.nama }}</option>
              </select>
            </div>

            <div class="fd-field">
              <label class="fd-label">Dari Bulan</label>
              <input v-model="filterDariBulan" type="month" class="fd-input" />
            </div>
            <div class="fd-field">
              <label class="fd-label">Sampai Bulan</label>
              <input v-model="filterSampaiBulan" type="month" class="fd-input" />
            </div>
          </FilterDropdown>
        </div>
        <div class="toolbar-right" style="display: flex; gap: 0.5rem; align-items: center;">
          <button v-if="authStore.canManagePetani" @click="triggerImportFile" class="btn-primary" :disabled="loading" style="background: linear-gradient(135deg, #3b82f6, #2563eb); border: none;">
            <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; margin-right: 4px;">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="17 8 12 3 7 8"/>
              <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            Import Excel/CSV
          </button>
          <input type="file" ref="importFileInput" @change="handleImportFile" accept=".xlsx,.xls,.csv" style="display: none;" />
          <button @click="exportExcel" class="btn-primary" :disabled="loading" style="background: linear-gradient(135deg, #10b981, #059669); border: none;">
            <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; margin-right: 4px;">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="7 10 12 15 17 10"/>
              <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Export Excel
          </button>
          <button v-if="authStore.canManagePetani" @click="showAddModal = true" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-inline" style="width: 14px; height: 14px; margin-right: 4px;">
              <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Petani
          </button>
        </div>
      </div>

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

      <!-- Data Table -->
      <div class="table-wrap">
        <table class="data-table">
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
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td :colspan="colSpan" class="loading-cell">
                <div class="loading-wrap">
                  <div class="spinner"></div>
                  <span>Memuat data petani...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredPetani.length === 0">
              <td :colspan="colSpan" class="empty-cell" style="text-align: center; padding: 2rem 0; color: #9ea9b8;">Tidak ada data petani</td>
            </tr>
            <tr v-else v-for="(petani, index) in paginatedPetani" :key="petani.id">
              <td class="td-num">{{ rowNumber(index) }}</td>
              <td v-if="visibleCols.tanggal" class="td-date">{{ formatDate(petani.tanggal) }}</td>
              <td v-if="visibleCols.nik"><code>{{ petani.nik }}</code></td>
              <td v-if="visibleCols.nama" class="td-name">{{ petani.nama }}</td>
              <td v-if="visibleCols.luas_lahan" class="text-right font-medium">{{ formatNumber(petani.luas_lahan) }} HA</td>
              <td v-if="visibleCols.alamat_lahan">{{ petani.alamat_lahan || '-' }}</td>
              <td v-if="visibleCols.potensi_panen" class="text-right font-medium">{{ formatNumber(petani.potensi_panen) }} KG</td>
              <td v-if="visibleCols.komoditi" class="text-center">
                <span class="badge" :class="petani.komoditi ? `badge-${petani.komoditi.toLowerCase()}` : ''">
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

              <td class="td-actions">
                <button @click="viewDetail(petani)" class="btn-icon btn-view" title="Detail">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
                <button v-if="authStore.canManagePetani" @click="editPetani(petani)" class="btn-icon btn-edit" title="Edit">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </button>
                <button v-if="authStore.canManagePetani" @click="deletePetani(petani.id)" class="btn-icon btn-del" title="Hapus">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-bar" v-if="filteredPetani.length">
        <div class="pagination-info">
          Menampilkan {{ pageStart }}-{{ pageEnd }} dari {{ filteredPetani.length }} data
        </div>
        <div class="pagination-controls">
          <label for="petani-per-page">Baris:</label>
          <select id="petani-per-page" v-model="perPage" class="per-page-select">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <button class="btn-page" @click="prevPage" :disabled="currentPage === 1">&laquo;</button>
          <span class="page-label">{{ currentPage }} / {{ totalPages }}</span>
          <button class="btn-page" @click="nextPage" :disabled="currentPage === totalPages">&raquo;</button>
        </div>
      </div>
    </div>

    <!-- Modal Add/Edit -->
    <div v-if="showAddModal || showEditModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal detail-modal">
        <div class="modal-header">
          <h3>{{ showEditModal ? 'Edit Data Petani' : 'Tambah Data Petani Baru' }}</h3>
          <button @click="closeModal" class="btn-close">&times;</button>
        </div>
        <form @submit.prevent="submitForm" class="modal-form" style="max-height: 80vh; overflow-y: auto;">
          <div class="form-section">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.3px; border-bottom: 1px solid #f0f3f7; padding-bottom: 4px;">
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; margin-right: 4px; vertical-align: middle;">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              Informasi Umum
            </h4>
            <div class="form-row-2">
              <div class="form-group">
                <label>Tanggal Penambahan Data <span class="req">*</span></label>
                <input v-model="form.tanggal" type="date" required />
              </div>
              <div class="form-group">
                <label>NIK <span class="req">*</span> <span class="nik-status" :class="nikStatus.class" style="font-size: 0.72rem; margin-left: 4px;">{{ nikStatus.message }}</span></label>
                <input v-model="form.nik" type="text" maxlength="16" required @input="checkNikDuplicate" />
              </div>
            </div>
            <div class="form-group">
              <label>Nama Lengkap <span class="req">*</span></label>
              <input v-model="form.nama" type="text" required />
            </div>
          </div>

          <div class="form-section" style="margin-top: 1.25rem;">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.3px; border-bottom: 1px solid #f0f3f7; padding-bottom: 4px;">
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; margin-right: 4px; vertical-align: middle;">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              Alamat & Wilayah
            </h4>
            <div class="form-group">
              <label>Alamat Lengkap <span class="req">*</span></label>
              <textarea v-model="form.alamat" rows="2" required style="width: 100%; border-radius: 8px; border: 1px solid #d1d5db; padding: 0.6rem 0.85rem; font-size: 0.875rem;"></textarea>
            </div>
            <div class="form-row-2">
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
            <div class="form-row-2">
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

          <div class="form-section" style="margin-top: 1.25rem;">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.3px; border-bottom: 1px solid #f0f3f7; padding-bottom: 4px;">
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; margin-right: 4px; vertical-align: middle;">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
              Kontak & Bank
            </h4>
            <div class="form-row-2">
              <div class="form-group">
                <label>No. Telepon</label>
                <input v-model="form.no_telepon" type="text" maxlength="15" />
              </div>
              <div class="form-group">
                <label>Bank</label>
                <input v-model="form.bank" type="text" placeholder="Contoh: BRI, BCA, Mandiri" />
              </div>
            </div>
            <div class="form-group">
              <label>No. Rekening</label>
              <input v-model="form.no_rekening" type="text" />
            </div>
          </div>

          <div class="form-section" style="margin-top: 1.25rem;">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.3px; border-bottom: 1px solid #f0f3f7; padding-bottom: 4px;">
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; margin-right: 4px; vertical-align: middle;">
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
              <label>Alamat Lahan <span class="req">*</span></label>
              <textarea v-model="form.alamat_lahan" rows="2" required style="width: 100%; border-radius: 8px; border: 1px solid #d1d5db; padding: 0.6rem 0.85rem; font-size: 0.875rem;"></textarea>
            </div>
            <div class="form-row-2">
              <div class="form-group">
                <label>Luas Lahan (HA) <span class="req">*</span></label>
                <input v-model="form.luas_lahan" type="number" step="0.01" min="0" required />
              </div>
              <div class="form-group">
                <label>Potensi Panen (KG) <span class="req">*</span></label>
                <input v-model="form.potensi_panen" type="number" step="0.01" min="0" required />
              </div>
            </div>
            <div class="form-group">
              <label>Komoditi <span class="req">*</span></label>
              <select v-model="form.komoditi" required>
                <option value="">Pilih Komoditi</option>
                <option value="Gabah">Gabah</option>
                <option value="Jagung">Jagung</option>
                <option value="Beras">Beras</option>
              </select>
            </div>
          </div>

          <div class="form-section" style="margin-top: 1.25rem;">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.3px; border-bottom: 1px solid #f0f3f7; padding-bottom: 4px;">
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; margin-right: 4px; vertical-align: middle;">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
              </svg>
              Upload Foto & Dokumen
            </h4>
            <div class="form-row-2">
              <div class="form-group">
                <label>Foto KTP</label>
                <input type="file" @change="handleFileUpload($event, 'foto_ktp')" accept="image/*" />
                <img v-if="previews.foto_ktp" :src="previews.foto_ktp" class="preview-image" style="max-height: 120px; object-fit: contain; margin-top: 8px; border-radius: 6px; border: 1px solid #e2e8f0;" />
              </div>
              <div class="form-group">
                <label>Foto Petani</label>
                <input type="file" @change="handleFileUpload($event, 'foto_petani')" accept="image/*" />
                <img v-if="previews.foto_petani" :src="previews.foto_petani" class="preview-image" style="max-height: 120px; object-fit: contain; margin-top: 8px; border-radius: 6px; border: 1px solid #e2e8f0;" />
              </div>
            </div>
            <div class="form-row-2" style="margin-top: 0.5rem;">
              <div class="form-group">
                <label>Foto Komoditi</label>
                <input type="file" @change="handleFileUpload($event, 'foto_komoditi')" accept="image/*" />
                <img v-if="previews.foto_komoditi" :src="previews.foto_komoditi" class="preview-image" style="max-height: 120px; object-fit: contain; margin-top: 8px; border-radius: 6px; border: 1px solid #e2e8f0;" />
              </div>
              <div class="form-group">
                <label>Kwitansi Pembayaran</label>
                <input type="file" @change="handleFileUpload($event, 'kwitansi_pembayaran')" accept="image/*,application/pdf" />
                <img v-if="previews.kwitansi_pembayaran && !isPdfPreview('kwitansi_pembayaran')" :src="previews.kwitansi_pembayaran" class="preview-image" style="max-height: 120px; object-fit: contain; margin-top: 8px; border-radius: 6px; border: 1px solid #e2e8f0;" />
                <p v-if="previews.kwitansi_pembayaran && isPdfPreview('kwitansi_pembayaran')" class="pdf-indicator" style="font-size: 0.8rem; color: #dc2626; margin-top: 8px; display: flex; align-items: center; gap: 4px;">
                  <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                  </svg>
                  PDF telah dipilih
                </p>
              </div>
            </div>
            <small class="help-text" style="font-size: 0.72rem; color: #8a96a8; display: block; margin-top: 8px;">* Format: JPG, PNG, PDF. Max: 5MB (akan dikompres otomatis)</small>
          </div>

          <div class="modal-actions" style="margin-top: 1.5rem; border-top: 1px solid #f0f3f7; padding-top: 1rem;">
            <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
            <button type="submit" class="btn-primary" :disabled="loading || nikStatus.isDuplicate">
              {{ loading ? 'Menyimpan...' : 'Simpan Data' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Detail -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="showDetailModal = false">
      <div class="modal detail-modal">
        <div class="modal-header">
          <h3>Detail Data Petani</h3>
          <button @click="showDetailModal = false" class="btn-close">&times;</button>
        </div>
        <div class="modal-body" v-if="selectedPetani" style="max-height: 80vh; overflow-y: auto;">
          <div class="detail-section">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.3px;">Informasi Umum</h4>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="det-label">Tanggal Penambahan Data</span>
                <span class="det-val">{{ formatDate(selectedPetani.tanggal) }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">NIK</span>
                <span class="det-val"><code>{{ selectedPetani.nik }}</code></span>
              </div>
              <div class="detail-item full-width">
                <span class="det-label">Nama Petani</span>
                <span class="det-val font-semibold">{{ selectedPetani.nama }}</span>
              </div>
            </div>
          </div>

          <div class="detail-section border-top">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.3px;">Alamat & Wilayah</h4>
            <div class="detail-grid">
              <div class="detail-item full-width">
                <span class="det-label">Alamat Lengkap</span>
                <span class="det-val">{{ selectedPetani.alamat }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Provinsi</span>
                <span class="det-val">{{ selectedPetani.provinsi?.nama || '-' }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Kabupaten</span>
                <span class="det-val">{{ selectedPetani.kabupaten?.nama || '-' }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Kecamatan</span>
                <span class="det-val">{{ selectedPetani.kecamatan?.nama || '-' }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Kalurahan/Desa</span>
                <span class="det-val">{{ selectedPetani.kalurahan?.nama || '-' }}</span>
              </div>
            </div>
          </div>

          <div class="detail-section border-top">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.3px;">Kontak & Bank</h4>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="det-label">No. Telepon</span>
                <span class="det-val">{{ selectedPetani.no_telepon || '-' }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Bank</span>
                <span class="det-val">{{ selectedPetani.bank || '-' }}</span>
              </div>
              <div class="detail-item full-width">
                <span class="det-label">No. Rekening</span>
                <span class="det-val">{{ selectedPetani.no_rekening || '-' }}</span>
              </div>
            </div>
          </div>

          <div class="detail-section border-top">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.3px;">Informasi Lahan & Panen</h4>
            <div class="detail-grid">
              <div class="detail-item full-width">
                <span class="det-label">Alamat Lahan</span>
                <span class="det-val">{{ selectedPetani.alamat_lahan }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Luas Lahan</span>
                <span class="det-val font-medium">{{ formatNumber(selectedPetani.luas_lahan) }} HA</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Potensi Panen</span>
                <span class="det-val font-medium">{{ formatNumber(selectedPetani.potensi_panen) }} KG</span>
              </div>
              <div class="detail-item full-width">
                <span class="det-label">Komoditi</span>
                <span class="badge" :class="selectedPetani.komoditi ? `badge-${selectedPetani.komoditi.toLowerCase()}` : ''">
                  {{ selectedPetani.komoditi }}
                </span>
              </div>
            </div>
          </div>

          <div class="detail-section border-top">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.3px;">Foto & Dokumen</h4>
            <div class="photo-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-top: 0.5rem;">
              <div class="photo-item" v-if="selectedPetani.foto_ktp" style="display: flex; flex-direction: column; gap: 4px;">
                <span class="det-label">Foto KTP</span>
                <img :src="getImageUrl(selectedPetani.foto_ktp)" alt="Foto KTP" @click="openImage(getImageUrl(selectedPetani.foto_ktp))" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 1px solid #e2e8f0; transition: transform 0.2s;" class="hover-scale" />
              </div>
              <div class="photo-item" v-if="selectedPetani.foto_petani" style="display: flex; flex-direction: column; gap: 4px;">
                <span class="det-label">Foto Petani</span>
                <img :src="getImageUrl(selectedPetani.foto_petani)" alt="Foto Petani" @click="openImage(getImageUrl(selectedPetani.foto_petani))" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 1px solid #e2e8f0; transition: transform 0.2s;" class="hover-scale" />
              </div>
              <div class="photo-item" v-if="selectedPetani.foto_komoditi" style="display: flex; flex-direction: column; gap: 4px;">
                <span class="det-label">Foto Komoditi</span>
                <img :src="getImageUrl(selectedPetani.foto_komoditi)" alt="Foto Komoditi" @click="openImage(getImageUrl(selectedPetani.foto_komoditi))" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 1px solid #e2e8f0; transition: transform 0.2s;" class="hover-scale" />
              </div>
              <div class="photo-item" v-if="selectedPetani.kwitansi_pembayaran" style="display: flex; flex-direction: column; gap: 4px;">
                <span class="det-label">Kwitansi Pembayaran</span>
                <img v-if="!isPdfPath(selectedPetani.kwitansi_pembayaran)" :src="getImageUrl(selectedPetani.kwitansi_pembayaran)" alt="Kwitansi" @click="openImage(getImageUrl(selectedPetani.kwitansi_pembayaran))" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 1px solid #e2e8f0; transition: transform 0.2s;" class="hover-scale" />
                <button v-else type="button" class="btn-secondary" @click="openImage(getImageUrl(selectedPetani.kwitansi_pembayaran))" style="padding: 1rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; height: 120px; display: flex; align-items: center; justify-content: center;">
                  Buka Dokumen PDF
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api, { getStorageUrl } from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import FilterDropdown from '@/components/FilterDropdown.vue'

const authStore = useAuthStore()
const petaniList = ref([])
const searchQuery = ref('')
const currentPage = ref(1)
const perPage = ref('10')
const filterProvinsi = ref('')
const filterKabupatenId = ref('')
const filterKecamatan = ref('')
const filterKalurahan = ref('')
const filterKomoditi = ref('')
const filterDariBulan = ref('')
const filterSampaiBulan = ref('')

const getStartOfMonth = (date) => {
  const y = date.getFullYear()
  const m = String(date.getMonth() + 1).padStart(2, '0')
  return `${y}-${m}-01`
}

const getEndOfMonth = (date) => {
  const y = date.getFullYear()
  const m = date.getMonth() + 1
  const lastDay = new Date(y, m, 0).getDate()
  const mStr = String(m).padStart(2, '0')
  return `${y}-${mStr}-${String(lastDay).padStart(2, '0')}`
}

const filterKabupatenOptions = ref([])
const filterKecamatanOptions = ref([])
const filterKalurahanOptions = ref([])

const onFilterProvinsiChange = () => {
  filterKabupatenId.value = ''
  filterKecamatan.value = ''
  filterKalurahan.value = ''
  if (filterProvinsi.value) {
    filterKabupatenOptions.value = allKabupaten.value.filter(k => k.provinsi_id == filterProvinsi.value)
  } else {
    filterKabupatenOptions.value = []
  }
  filterKecamatanOptions.value = []
  filterKalurahanOptions.value = []
}

const onFilterKabupatenChange = () => {
  filterKecamatan.value = ''
  filterKalurahan.value = ''
  if (filterKabupatenId.value) {
    filterKecamatanOptions.value = allKecamatan.value.filter(k => k.kabupaten_id == filterKabupatenId.value)
  } else {
    filterKecamatanOptions.value = []
  }
  filterKalurahanOptions.value = []
}

const onFilterKecamatanChange = () => {
  filterKalurahan.value = ''
  if (filterKecamatan.value) {
    filterKalurahanOptions.value = allKalurahan.value.filter(k => k.kecamatan_id == filterKecamatan.value)
  } else {
    filterKalurahanOptions.value = []
  }
}

const petaniActiveFilterCount = computed(() => {
  let count = 0
  if (filterProvinsi.value) count++
  if (filterKabupatenId.value) count++
  if (filterKecamatan.value) count++
  if (filterKalurahan.value) count++
  if (filterKomoditi.value) count++
  if (filterDariBulan.value) count++
  if (filterSampaiBulan.value) count++
  return count
})

const statistics = computed(() => {
  let totalLahan = 0
  let totalPotensi = 0
  let countGabah = 0
  let countBeras = 0
  let countJagung = 0
  
  filteredPetani.value.forEach(p => {
    totalLahan += parseFloat(p.luas_lahan) || 0
    totalPotensi += parseFloat(p.potensi_panen) || 0
    if (p.komoditi === 'Gabah') countGabah++
    if (p.komoditi === 'Beras') countBeras++
    if (p.komoditi === 'Jagung') countJagung++
  })

  return {
    totalCount: filteredPetani.value.length,
    totalLahan,
    totalPotensi,
    countGabah,
    countBeras,
    countJagung
  }
})

const formatVolume = (vol) => {
  if (!vol) return '0'
  return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(vol)
}
const showAddModal = ref(false)
const showEditModal = ref(false)
const showDetailModal = ref(false)
const selectedPetani = ref(null)
const loading = ref(false)
const importFileInput = ref(null)

const showColPicker = ref(false)
const allColDefs = [
  { key: 'tanggal', label: 'Tanggal', adminOnly: false },
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
  { key: 'no_rekening', label: 'No Rekening', adminOnly: false },
]
const colDefs = computed(() =>
  allColDefs.filter(c => !c.adminOnly || authStore.canVerify)
)
const visibleCols = ref({
  tanggal: false, nik: true, nama: true, luas_lahan: false, alamat_lahan: false,
  potensi_panen: false, komoditi: true, alamat: false, provinsi_id: false,
  kabupaten_id: true, kecamatan_id: false, kalurahan_id: false,
  no_telepon: false, bank: false, no_rekening: false,
})
const colSpan = computed(() => 2 + Object.values(visibleCols.value).filter(Boolean).length)
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
  kwitansi_pembayaran: null
})

const previews = ref({
  foto_ktp: null,
  foto_petani: null,
  foto_komoditi: null,
  kwitansi_pembayaran: null
})
const docPreviewIsPdf = ref({
  kwitansi_pembayaran: false,
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

const totalPages = computed(() => {
  if (perPage.value === 'all') return 1
  const size = Number(perPage.value) || 10
  return Math.max(1, Math.ceil(filteredPetani.value.length / size))
})

const paginatedPetani = computed(() => {
  if (perPage.value === 'all') return filteredPetani.value
  const size = Number(perPage.value) || 10
  const start = (currentPage.value - 1) * size
  return filteredPetani.value.slice(start, start + size)
})

const pageStart = computed(() => {
  if (!filteredPetani.value.length) return 0
  if (perPage.value === 'all') return 1
  return (currentPage.value - 1) * (Number(perPage.value) || 10) + 1
})

const pageEnd = computed(() => {
  if (!filteredPetani.value.length) return 0
  if (perPage.value === 'all') return filteredPetani.value.length
  return Math.min(currentPage.value * (Number(perPage.value) || 10), filteredPetani.value.length)
})

const rowNumber = (index) => {
  if (perPage.value === 'all') return index + 1
  return (currentPage.value - 1) * (Number(perPage.value) || 10) + index + 1
}

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++
}

watch([filteredPetani, perPage], () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = totalPages.value
  }
  if (currentPage.value < 1) {
    currentPage.value = 1
  }
})

const fetchPetani = async () => {
  loading.value = true
  try {
    const response = await api.get('/petani')
    petaniList.value = response.data.data
    currentPage.value = 1
  } catch (error) {
    console.error('Error fetching petani:', error)
    alert('Gagal mengambil data petani')
  } finally {
    loading.value = false
  }
}

const applyFilter = async () => {
  try {
    const params = {}
    if (filterProvinsi.value) params.provinsi_id = filterProvinsi.value
    if (filterKabupatenId.value) params.kabupaten_id = filterKabupatenId.value
    if (filterKecamatan.value) params.kecamatan_id = filterKecamatan.value
    if (filterKalurahan.value) params.kalurahan_id = filterKalurahan.value
    if (filterKomoditi.value) params.komoditi = filterKomoditi.value
    if (filterDariBulan.value) {
      const parts = filterDariBulan.value.split('-')
      const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, 1)
      params.tanggal_dari = getStartOfMonth(d)
    }
    if (filterSampaiBulan.value) {
      const parts = filterSampaiBulan.value.split('-')
      const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, 1)
      params.tanggal_sampai = getEndOfMonth(d)
    }

    const response = await api.get('/petani', { params })
    petaniList.value = response.data.data
    currentPage.value = 1
  } catch (error) {
    alert('Gagal menerapkan filter')
  }
}

const resetFilter = () => {
  filterProvinsi.value = ''
  filterKabupatenId.value = ''
  filterKecamatan.value = ''
  filterKalurahan.value = ''
  filterKomoditi.value = ''
  filterDariBulan.value = ''
  filterSampaiBulan.value = ''
  searchQuery.value = ''
  filterKabupatenOptions.value = []
  filterKecamatanOptions.value = []
  filterKalurahanOptions.value = []
  currentPage.value = 1
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
    if (fieldName === 'kwitansi_pembayaran') {
      docPreviewIsPdf.value[fieldName] = file.type === 'application/pdf'
    }
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
    kwitansi_pembayaran: null
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
  docPreviewIsPdf.value.kwitansi_pembayaran = isPdfPath(petani.kwitansi_pembayaran)
  
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
    kwitansi_pembayaran: null
  }
  docPreviewIsPdf.value = {
    kwitansi_pembayaran: false
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

const triggerImportFile = () => {
  if (importFileInput.value) {
    importFileInput.value.click()
  }
}

const handleImportFile = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Reset the file input value so same file can be selected again
  event.target.value = ''

  const formData = new FormData()
  formData.append('file', file)

  try {
    loading.value = true
    const response = await api.post('/petani/import', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    if (response.data && response.data.success) {
      const res = response.data.data
      alert(`Import Selesai!\nSukses: ${res.success_count}\nSkip (Duplikat): ${res.skipped_count}\nGagal (Error): ${res.failed_count}\n\n${res.errors && res.errors.length ? 'Detail:\n' + res.errors.slice(0, 5).join('\n') : ''}`)
      // Reload the data
      await fetchPetani()
    } else {
      alert(response.data.message || 'Gagal import data')
    }
  } catch (error) {
    const errorMsg = error.response?.data?.message || 'Terjadi kesalahan saat mengunggah file'
    alert('Gagal import data: ' + errorMsg)
  } finally {
    loading.value = false
  }
}

const exportExcel = async () => {
  try {
    loading.value = true
    const params = {}
    if (filterProvinsi.value) params.provinsi_id = filterProvinsi.value
    if (filterKabupatenId.value) params.kabupaten_id = filterKabupatenId.value
    if (filterKecamatan.value) params.kecamatan_id = filterKecamatan.value
    if (filterKalurahan.value) params.kalurahan_id = filterKalurahan.value
    if (filterKomoditi.value) params.komoditi = filterKomoditi.value
    if (filterDariBulan.value) {
      const parts = filterDariBulan.value.split('-')
      const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, 1)
      params.tanggal_dari = getStartOfMonth(d)
    }
    if (filterSampaiBulan.value) {
      const parts = filterSampaiBulan.value.split('-')
      const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, 1)
      params.tanggal_sampai = getEndOfMonth(d)
    }
    
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

const isPdfPath = (path) => typeof path === 'string' && path.toLowerCase().endsWith('.pdf')

const isPdfPreview = (fieldName) => {
  if (docPreviewIsPdf.value[fieldName]) return true
  const preview = previews.value[fieldName]
  return typeof preview === 'string' && preview.startsWith('data:application/pdf')
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
  return num % 1 === 0
    ? num.toLocaleString('id-ID')
    : num.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
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

/* Hero Banner */
.hero-banner {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  border-radius: 16px;
  padding: 1.75rem 2rem;
  color: #fff;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 20px rgba(16, 185, 129, 0.15);
}
.hero-icon {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.hero-icon svg {
  width: 26px;
  height: 26px;
}
.hero-text h2 {
  font-size: 1.3rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
  color: white;
}
.hero-text p {
  font-size: 0.875rem;
  opacity: 0.85;
}

/* Stats Cards */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}
.stat-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e8ecf0;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.25rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
}
.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.stat-icon svg {
  width: 22px;
  height: 22px;
}
.icon-blue { background: #eff6ff; color: #3b82f6; }
.icon-green { background: #ecfdf5; color: #10b981; }
.icon-orange { background: #fff7ed; color: #f97316; }
.text-green-val { color: #10b981; }
.text-orange-val { color: #f97316; }

.stat-info h3 {
  font-size: 0.82rem;
  color: #6b7280;
  font-weight: 600;
  margin-bottom: 0.25rem;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}
.stat-val {
  font-size: 1.25rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 0.15rem;
}
.stat-sub {
  font-size: 0.72rem;
  color: #9ea9b8;
}

/* Page Card */
.page-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e8ecf0;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
}

.toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.25rem;
  flex-wrap: wrap;
  gap: 0.75rem;
}
.toolbar-left {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex: 1;
  max-width: 500px;
}
.search-input {
  width: 100%;
  padding: 0.55rem 0.9rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  outline: none;
  transition: border-color 0.15s;
}
.search-input:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
}

/* Buttons */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #10b981, #059669);
  color: #ffffff;
  border: none;
  border-radius: 9px;
  padding: 9px 18px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}
.btn-primary:hover {
  filter: brightness(1.08);
  transform: translateY(-1px);
}
.btn-primary svg {
  width: 14px;
  height: 14px;
}
.btn-secondary {
  background: #f3f4f6;
  color: #374151;
  border: 1px solid #e5e7eb;
  border-radius: 9px;
  padding: 9px 18px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
}
.btn-secondary:hover {
  background: #e5e7eb;
}
.btn-cancel {
  background: #f3f4f6;
  color: #4b5563;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  padding: 8px 16px;
  cursor: pointer;
}
.btn-cancel:hover { background: #e5e7eb; }

/* Table Style */
.table-wrap {
  overflow-x: auto;
}

.pagination-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  margin-top: 0.85rem;
  flex-wrap: wrap;
}
.pagination-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.pagination-info,
.page-label {
  color: #64748b;
  font-size: 0.85rem;
}
.per-page-select {
  height: 34px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  padding: 0 8px;
  background-color: #fff;
}
.btn-page {
  width: 32px;
  height: 32px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: #fff;
  cursor: pointer;
}
.btn-page:disabled {
  opacity: 0.45;
  cursor: not-allowed;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
}
.data-table th {
  background: #f8fafc;
  text-align: left;
  padding: 0.85rem 1rem;
  font-size: 0.75rem;
  font-weight: 700;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  border-bottom: 1px solid #e8ecf0;
}
.data-table td {
  padding: 0.85rem 1rem;
  border-bottom: 1px solid #f0f3f7;
  color: #374151;
}
.data-table tbody tr:hover {
  background: #f8fafc;
}

.td-num { color: #9ea9b8; width: 40px; text-align: center; }
.td-name { font-weight: 600; color: #1a2332; }
.td-date { color: #6b7280; font-size: 0.82rem; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.font-medium { font-weight: 500; }
.font-semibold { font-weight: 600; }

/* Status Badges */
.badge {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 5px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  white-space: nowrap;
}
.badge-gabah { background: #fff7ed; color: #c2410c; }
.badge-jagung { background: #eff6ff; color: #1d4ed8; }
.badge-beras { background: #f0fdf4; color: #15803d; }

/* Action Buttons */
.td-actions {
  width: 120px;
  white-space: nowrap;
}
.btn-icon {
  width: 32px;
  height: 32px;
  border-radius: 7px;
  border: 1px solid;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s;
  margin-right: 4px;
}
.btn-icon svg { width: 14px; height: 14px; }
.btn-view { background: #f0fdfa; border-color: #99f6e4; color: #0d9488; }
.btn-view:hover { background: #ccfbf1; }
.btn-edit { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; }
.btn-edit:hover { background: #dbeafe; }
.btn-del { background: #fef2f2; border-color: #fecaca; color: #ef4444; }
.btn-del:hover { background: #fee2e2; }

/* Loading state */
.loading-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 3rem 0;
  color: #6b7280;
  font-size: 0.875rem;
}
.spinner {
  width: 28px;
  height: 28px;
  border: 3px solid #e8ecf0;
  border-top-color: #10b981;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Modal Styles */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  padding: 1rem;
}
.modal {
  background: #ffffff;
  border-radius: 16px;
  width: 100%;
  max-width: 480px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  animation: slideIn 0.2s ease;
  overflow: hidden;
}
.modal.detail-modal {
  max-width: 720px;
}
@keyframes slideIn {
  from { transform: translateY(-16px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f0f3f7;
}
.modal-header h3 {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0f172a;
}
.btn-close {
  background: none;
  border: none;
  font-size: 1.4rem;
  color: #9ea9b8;
  cursor: pointer;
  padding: 2px 6px;
  border-radius: 6px;
  line-height: 1;
}
.btn-close:hover {
  background: #f3f4f6;
  color: #374151;
}

.modal-form {
  padding: 1.25rem 1.5rem 1.5rem;
}
.form-section {
  background: #f8fafc;
  padding: 1.25rem;
  border-radius: 12px;
  margin-bottom: 1.25rem;
  border: 1px solid #e8ecf0;
}
.form-section h4 {
  font-size: 0.88rem;
  font-weight: 700;
  color: #047857;
  margin-bottom: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  border-bottom: 1px solid #f0f3f7;
  padding-bottom: 4px;
}
.form-group {
  margin-bottom: 1.1rem;
}
.form-group label {
  display: block;
  font-size: 0.82rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.4rem;
}
.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.6rem 0.85rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  color: #0f172a;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.15s;
  background-color: #fff;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
}
.form-row-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
.req { color: #ef4444; }

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

/* Detail modal styles */
.modal-body {
  padding: 1.5rem;
}
.detail-section {
  padding-bottom: 1.25rem;
}
.detail-section.border-top {
  border-top: 1px solid #f0f3f7;
  padding-top: 1.25rem;
}
.detail-section h4 {
  font-size: 0.88rem;
  font-weight: 700;
  color: #047857;
  margin-bottom: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}
.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
.detail-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.detail-item.full-width {
  grid-column: span 2;
}
.det-label {
  font-size: 0.78rem;
  color: #8a96a8;
  font-weight: 500;
}
.det-val {
  font-size: 0.88rem;
  color: #1f2937;
}

/* NIK checking indicators */
.nik-status {
  font-size: 0.75rem;
  font-weight: bold;
}
.nik-status.duplicate { color: #ef4444; }
.nik-status.available { color: #10b981; }
.nik-status.checking { color: #f59e0b; }

.preview-image {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin-top: 8px;
  border: 1px solid #cbd5e1;
}

.photo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 15px;
}
.photo-item {
  text-align: center;
}
.photo-item img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
  transition: transform 0.2s;
}
.photo-item img:hover, .hover-scale:hover {
  transform: scale(1.05);
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
  border: 1.5px solid #10b981;
  border-radius: 8px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
  color: #10b981;
  transition: all 0.18s;
  box-shadow: 0 1px 4px rgba(16, 185, 129, 0.08);
}
.btn-col-picker:hover, .btn-col-picker.active {
  background: #10b981;
  color: #fff;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.22);
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
  background: #ecfdf5;
  color: #047857;
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
  background: #10b981;
  border-color: #10b981;
  color: #fff;
}
.col-label {
  flex: 1;
  font-family: monospace;
  font-size: 12px;
}

/* Filter panel dropdown specific adjustments */
.fd-field { display: flex; flex-direction: column; gap: 4px; }
.fd-label { font-size: 0.78rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; }
.fd-select, .fd-input {
  height: 34px; padding: 0 10px; border: 1px solid #d1d5db;
  border-radius: 6px; font-size: 0.875rem; background: #fff; outline: none;
  width: 100%; box-sizing: border-box;
}
.fd-select:focus, .fd-input:focus { border-color: #10b981; }

@media (max-width: 640px) {
  .hero-banner { padding: 1.25rem; }
  .stats-grid { grid-template-columns: 1fr; }
  .form-row-2 { grid-template-columns: 1fr; gap: 0; }
  .detail-grid { grid-template-columns: 1fr; }
  .detail-item.full-width { grid-column: span 1; }
  .toolbar { flex-direction: column; align-items: stretch; }
  .td-date { display: none; }
}
</style>