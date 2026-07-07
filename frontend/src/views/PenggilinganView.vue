<template>
  <div class="penggilingan-container" style="padding: 20px;">

    <!-- Hero Banner -->
    <div class="hero-banner orange">
      <div class="hero-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
          <path d="M9 22v-4h6v4"/>
          <path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/>
          <path d="M12 10h.01"/><path d="M16 10h.01"/><path d="M8 10h.01"/>
          <path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 14h.01"/>
        </svg>
      </div>
      <div class="hero-text">
        <h2>Data Makloon GKP</h2>
        <p>Kelola data makloon dan distribusi hasil panen gabah kering panen</p>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon icon-blue">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
            <path d="M9 22v-4h6v4"/>
          </svg>
        </div>
        <div class="stat-info">
          <h3>Total Makloon</h3>
          <p class="stat-val">{{ filteredData.length }}</p>
          <span class="stat-sub">Terdaftar/terfilter</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon icon-green">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
        </div>
        <div class="stat-info">
          <h3>Tonase Diterima</h3>
          <p class="stat-val text-green-val">{{ totalTonaseDiterima }} KG</p>
          <span class="stat-sub">Disetujui oleh admin</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon icon-orange">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
        </div>
        <div class="stat-info">
          <h3>Tonase Pending</h3>
          <p class="stat-val text-orange-val">{{ totalTonasePending }} KG</p>
          <span class="stat-sub">Menunggu verifikasi</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon icon-red">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
        </div>
        <div class="stat-info">
          <h3>Tonase Ditolak</h3>
          <p class="stat-val text-red-val">{{ totalTonaseDitolak }} KG</p>
          <span class="stat-sub">Ditolak oleh admin</span>
        </div>
      </div>
    </div>

    <!-- Main Card Content -->
    <div class="page-card">
      <div class="toolbar">
        <div class="toolbar-left">
          <input
            v-if="!authStore.isPenggilingan"
            v-model="filters.namaPenggilingan"
            @keyup.enter="applyFilters"
            type="text"
            placeholder="Cari nama makloon/MPP..."
            class="search-input"
          />
          <FilterDropdown
            :active-count="penggilinganActiveFilterCount"
            @apply="applyFilters"
            @reset="resetFilters"
          >
            <div class="fd-field">
              <label class="fd-label">Status Verifikasi</label>
              <select v-model="filters.statusVerifikasi" class="fd-select">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
              </select>
            </div>
            <div class="fd-field">
              <label class="fd-label">Tanggal Dari</label>
              <input v-model="filters.tanggalDari" type="date" class="fd-input" />
            </div>
            <div class="fd-field">
              <label class="fd-label">Tanggal Sampai</label>
              <input v-model="filters.tanggalSampai" type="date" class="fd-input" />
            </div>
          </FilterDropdown>
        </div>
        <div class="toolbar-right">
          <button v-if="authStore.canManagePenggilingan" @click="openAddModal" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-inline" style="width: 14px; height: 14px; margin-right: 4px;">
              <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Makloon
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
              <th v-if="visibleCols.tanggal_pengajuan">Tanggal Pengajuan</th>
              <th v-if="visibleCols.nama_penggilingan">Nama Penggilingan</th>
              <th v-if="visibleCols.lokasi_makloon">Lokasi Makloon</th>
              <th v-if="visibleCols.total_tonase">Total Tonase</th>
              <th v-if="visibleCols.jumlah_angkutan">Jumlah Angkutan</th>
              <th v-if="visibleCols.status_verifikasi">Status Verifikasi</th>
              <th v-if="visibleCols.catatan_verifikasi">Catatan Verifikasi</th>
              <th>Aksi</th>
              <th v-if="authStore.canVerify">Verifikasi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td :colspan="colSpan" class="loading-cell">
                <div class="loading-wrap">
                  <div class="spinner"></div>
                  <span>Memuat data makloon...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredData.length === 0">
              <td :colspan="colSpan" class="empty-cell" style="text-align: center; padding: 2rem 0; color: #9ea9b8;">Tidak ada data makloon</td>
            </tr>
            <tr v-else v-for="(item, index) in paginatedData" :key="item.id">
              <td class="td-num">{{ rowNumber(index) }}</td>
              <td v-if="visibleCols.tanggal_pengajuan" class="td-date">{{ formatDate(item.tanggal_pengajuan) }}</td>
              <td v-if="visibleCols.nama_penggilingan" class="td-name">{{ item.nama_penggilingan }}</td>
              <td v-if="visibleCols.lokasi_makloon">{{ item.lokasi_makloon }}</td>
              <td v-if="visibleCols.total_tonase" class="text-right font-medium">{{ formatNumber(item.total_tonase) }} KG</td>
              <td v-if="visibleCols.jumlah_angkutan" class="text-center font-medium">{{ item.jumlah_angkutan }}</td>
              <td v-if="visibleCols.status_verifikasi" class="text-center">
                <span class="badge-status" :class="'badge-' + item.status_verifikasi">
                  {{ item.status_verifikasi === 'disetujui' ? 'Disetujui' : item.status_verifikasi === 'ditolak' ? 'Ditolak' : 'Pending' }}
                </span>
              </td>
              <td v-if="visibleCols.catatan_verifikasi">{{ item.catatan_verifikasi || '-' }}</td>
              <td class="td-actions">
                <button @click="viewDetail(item)" class="btn-icon btn-view" title="Lihat Detail">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
                <button @click="downloadMakloonGKP(item.id)" class="btn-icon btn-view" title="Download Form GKP Maklon" style="background: #ecfdf5; border-color: #a7f3d0; color: #059669;">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                  </svg>
                </button>
                <button v-if="authStore.canManagePenggilingan" @click="editItem(item)" class="btn-icon btn-edit" title="Edit">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </button>
                <button v-if="authStore.canManagePenggilingan" @click="deleteItem(item.id)" class="btn-icon btn-del" title="Hapus">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                  </svg>
                </button>
              </td>
              <td v-if="authStore.canVerify" class="text-center verifikasi-cell">
                <button @click="openVerifikasiModal(item)" class="btn-icon btn-view btn-verify" title="Verifikasi" style="background: #e0f2fe; border-color: #bae6fd; color: #0284c7;">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px; height:14px;">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-bar" v-if="filteredData.length">
        <div class="pagination-info">
          Menampilkan {{ pageStart }}-{{ pageEnd }} dari {{ filteredData.length }} data
        </div>
        <div class="pagination-controls">
          <label for="makloon-per-page">Baris:</label>
          <select id="makloon-per-page" v-model="perPage" class="per-page-select">
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

    <!-- Modal Form -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal detail-modal">
        <div class="modal-header">
          <h3>{{ isEditing ? 'Edit' : 'Tambah' }} Makloon</h3>
          <button @click="closeModal" class="btn-close">&times;</button>
        </div>
        <form @submit.prevent="submitForm" class="modal-form" style="max-height: 80vh; overflow-y: auto;">
          <!-- Basic Info -->
          <div class="form-section">
            <h3>Informasi Pengajuan</h3>
            <div class="form-row">
              <div class="form-group">
                <label>Tanggal Pengajuan <span class="required">*</span></label>
                <input type="date" v-model="form.tanggal_pengajuan" required />
              </div>
              <div class="form-group">
                <label>Nama Makloon/MPP <span class="required">*</span></label>
                <input
                  v-if="!authStore.isPenggilingan"
                  type="text"
                  v-model="form.nama_penggilingan"
                  required
                  placeholder="Contoh: UD Sumber Rezeki"
                />
                <!-- Untuk role penggilingan: nama dikunci sesuai akun -->
                <input
                  v-else
                  type="text"
                  :value="authStore.namaPenggilingan"
                  readonly
                  class="input-readonly"
                />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Lokasi Makloon <span class="required">*</span></label>
                <select v-model="form.lokasi_makloon" required>
                  <option value="">Pilih Kabupaten</option>
                  <option v-for="kab in kabupatenList" :key="kab.id" :value="kab.nama">{{ kab.nama }}</option>
                </select>
              </div>
            </div>
          </div>

          <!-- GKP Photos -->
          <div class="form-section">
            <h3>
              <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
              </svg>
              Upload Foto & Dokumen
            </h3>
            <div class="form-row">
              <div class="form-group">
                <label>Foto GKP 1 (Di Sawah)</label>
                <img v-if="form.foto_gkp_1_preview" :src="form.foto_gkp_1_preview" class="preview-image" alt="Preview Foto GKP 1" />
                <input type="file" @change="handleFileUpload($event, 'foto_gkp_1')" accept="image/*" />
              </div>
              <div class="form-group">
                <label>Foto GKP 2 (Di Makloon)</label>
                <img v-if="form.foto_gkp_2_preview" :src="form.foto_gkp_2_preview" class="preview-image" alt="Preview Foto GKP 2" />
                <input type="file" @change="handleFileUpload($event, 'foto_gkp_2')" accept="image/*" />
              </div>
            </div>
            <small class="help-text">* Format: JPG, PNG, PDF. Max: 5MB (gambar akan dikompres otomatis)</small>
          </div>

          <!-- Transport Entries -->
          <div class="form-section">
            <div class="section-header">
              <h3>Data Transportasi</h3>
              <button
                type="button"
                @click="addTransport"
                class="btn-add-transport"
                :disabled="form.transports.length >= 20"
              >
                + Tambah Angkutan
              </button>
            </div>
            <div class="transport-list">
              <div
                v-for="(transport, index) in form.transports"
                :key="index"
                class="transport-item"
              >
                <div class="transport-header">
                  <h4>Angkutan {{ index + 1 }}</h4>
                  <button
                    type="button"
                    @click="removeTransport(index)"
                    class="btn-remove-transport"
                    :disabled="form.transports.length === 1"
                  >
                    Hapus
                  </button>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label>Nama Pengemudi <span class="required">*</span></label>
                    <input
                      type="text"
                      v-model="transport.nama_pengemudi"
                      required
                      placeholder="Contoh: Budi Santoso"
                    />
                  </div>
                  <div class="form-group">
                    <label>Nopol <span class="required">*</span></label>
                    <input
                      type="text"
                      v-model="transport.nopol"
                      required
                      placeholder="Contoh: G 1234 AB"
                    />
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label>Kuantum (dalam KG) <span class="required">*</span></label>
                    <input
                      type="number"
                      step="0.001"
                      v-model="transport.kuantum"
                      required
                      placeholder="Contoh: 2.500"
                    />
                  </div>
                  <div class="form-group">
                    <label>Upload Nota Timbang</label>
                    <img v-if="transport.nota_timbang_preview && !transport.nota_timbang_is_pdf" :src="transport.nota_timbang_preview" class="preview-image-small" alt="Preview Nota Timbang" />
                    <p v-if="transport.nota_timbang_preview && transport.nota_timbang_is_pdf" class="pdf-indicator">PDF telah dipilih</p>
                    <input
                      type="file"
                      @change="handleTransportFileUpload($event, index, 'nota_timbang')"
                      accept="image/*,application/pdf"
                    />
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label>Upload Surat Jalan</label>
                    <img v-if="transport.surat_jalan_preview && !transport.surat_jalan_is_pdf" :src="transport.surat_jalan_preview" class="preview-image-small" alt="Preview Surat Jalan" />
                    <p v-if="transport.surat_jalan_preview && transport.surat_jalan_is_pdf" class="pdf-indicator">PDF telah dipilih</p>
                    <input
                      type="file"
                      @change="handleTransportFileUpload($event, index, 'surat_jalan')"
                      accept="image/*,application/pdf"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Resume -->
          <div class="form-section resume-section">
            <h3>Resume</h3>
            <div class="resume-grid">
              <div class="resume-item">
                <span class="resume-label">Total Tonase (dalam KG):</span>
                <span class="resume-value">{{ calculateTotalTonase() }} KG</span>
              </div>
              <div class="resume-item">
                <span class="resume-label">Jumlah Angkutan:</span>
                <span class="resume-value">{{ form.transports.length }}</span>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" @click="closeModal" class="btn-cancel">Batal</button>
            <button type="submit" class="btn-submit">
              {{ isEditing ? 'Update' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Detail Modal -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="showDetailModal = false">
      <div class="modal-content modal-detail">
        <div class="modal-header">
          <h2>Detail Makloon</h2>
          <button @click="showDetailModal = false" class="btn-close">×</button>
        </div>
        <div class="modal-body detail-body" v-if="selectedItem">
          <div class="detail-section">
            <h3>Informasi Pengajuan</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Tanggal Pengajuan:</span>
                <span class="detail-value">{{ formatDate(selectedItem.tanggal_pengajuan) }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Nama Makloon/MPP:</span>
                <span class="detail-value">{{ selectedItem.nama_penggilingan }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Lokasi Makloon:</span>
                <span class="detail-value">{{ selectedItem.lokasi_makloon }}</span>
              </div>
            </div>
          </div>



          <div class="detail-section">
            <h3>Data Transportasi</h3>
            <div class="transport-table-wrapper">
              <table class="transport-table">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Pengemudi</th>
                    <th>Nopol</th>
                    <th>Kuantum (dalam KG)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="transport in selectedItem.transports"
                    :key="transport.id"
                  >
                    <td>{{ transport.urutan }}</td>
                    <td>{{ transport.nama_pengemudi }}</td>
                    <td>{{ transport.nopol }}</td>
                    <td class="text-right">{{ parseFloat(String(transport.kuantum)) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="detail-section">
            <h4>
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
              </svg>
              Foto & Dokumen
            </h4>
            <div class="photo-grid">
              <div class="photo-item" v-if="selectedItem.foto_gkp_1">
                <label>Foto GKP 1 (Di Sawah)</label>
                <img :src="getImageUrl(selectedItem.foto_gkp_1)" alt="Foto GKP 1" @click="openImage(getImageUrl(selectedItem.foto_gkp_1))" />
              </div>
              <div class="photo-item" v-if="selectedItem.foto_gkp_2">
                <label>Foto GKP 2 (Di Makloon)</label>
                <img :src="getImageUrl(selectedItem.foto_gkp_2)" alt="Foto GKP 2" @click="openImage(getImageUrl(selectedItem.foto_gkp_2))" />
              </div>
              <div
                class="photo-item"
                v-for="transport in selectedItem.transports.filter(t => t.nota_timbang)"
                :key="`nota-${transport.id}`"
              >
                <label>Nota Timbang - Angkutan #{{ transport.urutan }} ({{ transport.nama_pengemudi }})</label>
                <img v-if="!isPdfPath(transport.nota_timbang)" :src="getImageUrl(transport.nota_timbang)" :alt="`Nota Timbang ${transport.nama_pengemudi}`" @click="openImage(getImageUrl(transport.nota_timbang))" />
                <button v-else type="button" class="btn-doc-open" @click="openImage(getImageUrl(transport.nota_timbang))">Buka Dokumen PDF</button>
              </div>
              <div
                class="photo-item"
                v-for="transport in selectedItem.transports.filter(t => t.surat_jalan)"
                :key="`surat-${transport.id}`"
              >
                <label>Surat Jalan - Angkutan #{{ transport.urutan }} ({{ transport.nama_pengemudi }})</label>
                <img v-if="!isPdfPath(transport.surat_jalan)" :src="getImageUrl(transport.surat_jalan)" :alt="`Surat Jalan ${transport.nama_pengemudi}`" @click="openImage(getImageUrl(transport.surat_jalan))" />
                <button v-else type="button" class="btn-doc-open" @click="openImage(getImageUrl(transport.surat_jalan))">Buka Dokumen PDF</button>
              </div>
            </div>
            <p v-if="!selectedItem.foto_gkp_1 && !selectedItem.foto_gkp_2 && selectedItem.transports.filter(t => t.nota_timbang).length === 0 && selectedItem.transports.filter(t => t.surat_jalan).length === 0" class="no-data">
              Tidak ada foto atau dokumen
            </p>
          </div>

          <div class="detail-section resume-section">
            <h3>Resume</h3>
            <div class="resume-grid">
              <div class="resume-item">
                <span class="resume-label">Total Tonase (dalam KG):</span>
                <span class="resume-value">
                  {{ formatNumber(selectedItem.total_tonase) }} KG
                </span>
              </div>
              <div class="resume-item">
                <span class="resume-label">Jumlah Angkutan:</span>
                <span class="resume-value">{{ selectedItem.jumlah_angkutan }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Verifikasi -->
    <div v-if="showVerifikasiModal" class="modal-overlay" @click.self="showVerifikasiModal = false">
      <div class="modal-content modal-verifikasi">
        <div class="modal-header">
          <h2>Verifikasi Data Makloon</h2>
          <button @click="showVerifikasiModal = false" class="btn-close">×</button>
        </div>
        <div class="modal-body">
          <p class="verifikasi-info">
            <strong>{{ verifikasiItem?.nama_penggilingan }}</strong><br>
            <span>{{ verifikasiItem?.lokasi_makloon }}</span>
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
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import api, { getStorageUrl } from '../services/api'
import { useAuthStore } from '../stores/auth'
import FilterDropdown from '@/components/FilterDropdown.vue'

const authStore = useAuthStore()

// Type definitions removed (JavaScript mode)

const data = ref([])
const filteredData = ref([])
const kabupatenList = ref([])
const loading = ref(false)
const currentPage = ref(1)
const perPage = ref('10')

const showColPicker = ref(false)
const allColDefs = [
  { key: 'tanggal_pengajuan', label: 'Tanggal Pengajuan', adminOnly: false },
  { key: 'nama_penggilingan', label: 'Nama Penggilingan', adminOnly: false },
  { key: 'lokasi_makloon', label: 'Lokasi Makloon', adminOnly: false },
  { key: 'total_tonase', label: 'Total Tonase', adminOnly: false },
  { key: 'jumlah_angkutan', label: 'Jumlah Angkutan', adminOnly: false },
  { key: 'status_verifikasi', label: 'Status Verifikasi', adminOnly: false },
  { key: 'catatan_verifikasi', label: 'Catatan Verifikasi', adminOnly: false },
]
// Kolom verifikasi hanya tampil di col-picker untuk admin & superadmin
const colDefs = computed(() =>
  allColDefs.filter(c => !c.adminOnly || authStore.canVerify)
)
const visibleCols = ref({
  tanggal_pengajuan: true, nama_penggilingan: true,
  lokasi_makloon: true, total_tonase: true, jumlah_angkutan: true,
  status_verifikasi: true, catatan_verifikasi: false
})
const colSpan = computed(() => 2 + Object.values(visibleCols.value).filter(Boolean).length + (authStore.canVerify ? 1 : 0))

const showModal = ref(false)
const showDetailModal = ref(false)
const isEditing = ref(false)
const selectedItem = ref(null)

const showVerifikasiModal = ref(false)
const verifikasiItem = ref(null)
const verifikasiLoading = ref(false)
const verifikasiForm = ref({
  status_verifikasi: 'pending',
  catatan_verifikasi: ''
})

const filters = ref({
  tanggalDari: '',
  tanggalSampai: '',
  namaPenggilingan: '',
  statusVerifikasi: '',
})

const penggilinganActiveFilterCount = computed(() => {
  let count = 0
  if (filters.value.tanggalDari) count++
  if (filters.value.tanggalSampai) count++
  if (filters.value.statusVerifikasi) count++
  return count
})

const form = ref({
  id: null,
  tanggal_pengajuan: '',
  nama_penggilingan: '',
  lokasi_makloon: '',
  foto_gkp_1: null,
  foto_gkp_2: null,
  foto_gkp_1_preview: '',
  foto_gkp_2_preview: '',
  old_foto_gkp_1: '',
  old_foto_gkp_2: '',
  transports: [
    {
      nama_pengemudi: '',
      nopol: '',
      kuantum: '',
      nota_timbang: null,
      nota_timbang_preview: '',
      nota_timbang_is_pdf: false,
      old_nota_timbang: '',
      surat_jalan: null,
      surat_jalan_preview: '',
      surat_jalan_is_pdf: false,
      old_surat_jalan: '',
    },
  ],
})

const totalTonaseDiterima = computed(() => {
  const sum = filteredData.value
    .filter(item => item.status_verifikasi === 'disetujui')
    .reduce((sum, item) => sum + parseFloat(String(item.total_tonase)), 0)
  return sum.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
})

const totalTonasePending = computed(() => {
  const sum = filteredData.value
    .filter(item => !item.status_verifikasi || item.status_verifikasi === 'pending')
    .reduce((sum, item) => sum + parseFloat(String(item.total_tonase)), 0)
  return sum.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
})

const totalTonaseDitolak = computed(() => {
  const sum = filteredData.value
    .filter(item => item.status_verifikasi === 'ditolak')
    .reduce((sum, item) => sum + parseFloat(String(item.total_tonase)), 0)
  return sum.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
})

const totalPages = computed(() => {
  if (perPage.value === 'all') return 1
  const size = Number(perPage.value) || 10
  return Math.max(1, Math.ceil(filteredData.value.length / size))
})

const paginatedData = computed(() => {
  if (perPage.value === 'all') return filteredData.value
  const size = Number(perPage.value) || 10
  const start = (currentPage.value - 1) * size
  return filteredData.value.slice(start, start + size)
})

const pageStart = computed(() => {
  if (!filteredData.value.length) return 0
  if (perPage.value === 'all') return 1
  return (currentPage.value - 1) * (Number(perPage.value) || 10) + 1
})

const pageEnd = computed(() => {
  if (!filteredData.value.length) return 0
  if (perPage.value === 'all') return filteredData.value.length
  return Math.min(currentPage.value * (Number(perPage.value) || 10), filteredData.value.length)
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

watch([filteredData, perPage], () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = totalPages.value
  }
  if (currentPage.value < 1) {
    currentPage.value = 1
  }
})


const getImageUrl = (path) => getStorageUrl(path)

const openImage = (url) => {
  window.open(url, '_blank')
}

const isPdfPath = (path) => typeof path === 'string' && path.toLowerCase().endsWith('.pdf')

onMounted(() => {
  fetchData()
  fetchKabupaten()
})

const fetchKabupaten = async () => {
  try {
    const response = await api.get('/kabupaten')
    kabupatenList.value = response.data.data
  } catch (error) {
    console.error('Error fetching kabupaten:', error)
  }
}

const fetchData = async () => {
  loading.value = true
  try {
    const response = await api.get('/penggilingan')
    data.value = response.data.data
    filteredData.value = response.data.data
    currentPage.value = 1
  } catch (error) {
    console.error('Error fetching data:', error)
    alert('Gagal memuat data')
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  let filtered = [...data.value]

  if (filters.value.tanggalDari) {
    filtered = filtered.filter(
      (item) => new Date(item.tanggal_pengajuan) >= new Date(filters.value.tanggalDari)
    )
  }

  if (filters.value.tanggalSampai) {
    filtered = filtered.filter(
      (item) => new Date(item.tanggal_pengajuan) <= new Date(filters.value.tanggalSampai)
    )
  }

  if (filters.value.namaPenggilingan) {
    filtered = filtered.filter((item) =>
      item.nama_penggilingan
        .toLowerCase()
        .includes(filters.value.namaPenggilingan.toLowerCase())
    )
  }

  if (filters.value.statusVerifikasi) {
    filtered = filtered.filter((item) => item.status_verifikasi === filters.value.statusVerifikasi)
  }

  filteredData.value = filtered
  currentPage.value = 1
}

const resetFilters = () => {
  filters.value = {
    tanggalDari: '',
    tanggalSampai: '',
    namaPenggilingan: '',
    statusVerifikasi: '',
  }
  filteredData.value = [...data.value]
  currentPage.value = 1
}

const handleFileUpload = (event, field) => {
  const target = event.target
  const file = target.files?.[0]
  if (file) {
    form.value[field] = file
    if (field === 'foto_gkp_1') {
      form.value.foto_gkp_1_preview = URL.createObjectURL(file)
    } else {
      form.value.foto_gkp_2_preview = URL.createObjectURL(file)
    }
  }
}

const removeImage = (field) => {
  form.value[field] = null
  if (field === 'foto_gkp_1') {
    form.value.foto_gkp_1_preview = ''
  } else {
    form.value.foto_gkp_2_preview = ''
  }
}

const handleTransportFileUpload = (event, index, fileType = 'nota_timbang') => {
  const target = event.target
  const file = target.files?.[0]
  if (file && form.value.transports[index]) {
    if (fileType === 'nota_timbang') {
      form.value.transports[index].nota_timbang = file
      form.value.transports[index].nota_timbang_preview = URL.createObjectURL(file)
      form.value.transports[index].nota_timbang_is_pdf = file.type === 'application/pdf'
    } else if (fileType === 'surat_jalan') {
      form.value.transports[index].surat_jalan = file
      form.value.transports[index].surat_jalan_preview = URL.createObjectURL(file)
      form.value.transports[index].surat_jalan_is_pdf = file.type === 'application/pdf'
    }
  }
}

const removeTransportImage = (index) => {
  if (form.value.transports[index]) {
    form.value.transports[index].nota_timbang = null
    form.value.transports[index].nota_timbang_preview = ''
    form.value.transports[index].nota_timbang_is_pdf = false
  }
}

const addTransport = () => {
  if (form.value.transports.length < 20) {
    form.value.transports.push({
      nama_pengemudi: '',
      nopol: '',
      kuantum: '',
      nota_timbang: null,
      nota_timbang_preview: '',
      nota_timbang_is_pdf: false,
      old_nota_timbang: '',
      surat_jalan: null,
      surat_jalan_preview: '',
      surat_jalan_is_pdf: false,
      old_surat_jalan: '',
    })
  }
}

const removeTransport = (index) => {
  if (form.value.transports.length > 1) {
    form.value.transports.splice(index, 1)
  }
}

const calculateTotalTonase = () => {
  const total = form.value.transports.reduce((sum, transport) => {
    return sum + (parseFloat(String(transport.kuantum)) || 0)
  }, 0)
  return total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const submitForm = async () => {
  try {
    // Validasi client-side dulu
    if (!form.value.tanggal_pengajuan) {
      alert('Tanggal pengajuan harus diisi')
      return
    }
    if (!authStore.isPenggilingan && !form.value.nama_penggilingan) {
      alert('Nama penggilingan harus diisi')
      return
    }
    if (!form.value.lokasi_makloon) {
      alert('Lokasi makloon harus diisi')
      return
    }
    if (form.value.transports.length === 0) {
      alert('Minimal harus ada 1 data transport')
      return
    }

    // Validasi setiap transport
    for (let i = 0; i < form.value.transports.length; i++) {
      const transport = form.value.transports[i]
      if (!transport) continue
      
      if (!transport.nama_pengemudi) {
        alert(`Nama pengemudi transport ${i + 1} harus diisi`)
        return
      }
      if (!transport.nopol) {
        alert(`Nopol transport ${i + 1} harus diisi`)
        return
      }
      if (!transport.kuantum || parseFloat(String(transport.kuantum)) <= 0) {
        alert(`Kuantum transport ${i + 1} harus diisi dan lebih dari 0`)
        return
      }
    }

    const formData = new FormData()
    formData.append('tanggal_pengajuan', form.value.tanggal_pengajuan)
    formData.append('nama_penggilingan', form.value.nama_penggilingan)
    formData.append('lokasi_makloon', form.value.lokasi_makloon)

    if (form.value.foto_gkp_1) {
      formData.append('foto_gkp_1', form.value.foto_gkp_1)
    } else if (form.value.old_foto_gkp_1) {
      formData.append('old_foto_gkp_1', form.value.old_foto_gkp_1)
    }
    if (form.value.foto_gkp_2) {
      formData.append('foto_gkp_2', form.value.foto_gkp_2)
    } else if (form.value.old_foto_gkp_2) {
      formData.append('old_foto_gkp_2', form.value.old_foto_gkp_2)
    }

    form.value.transports.forEach((transport, index) => {
      formData.append(`transports[${index}][nama_pengemudi]`, transport.nama_pengemudi)
      formData.append(`transports[${index}][nopol]`, transport.nopol)
      formData.append(`transports[${index}][kuantum]`, String(transport.kuantum))
      // Only append nota_timbang if it's a valid File object
      if (transport.nota_timbang && transport.nota_timbang instanceof File) {
        formData.append(`transports[${index}][nota_timbang]`, transport.nota_timbang)
      } else if (transport.old_nota_timbang) {
        formData.append(`transports[${index}][old_nota_timbang]`, transport.old_nota_timbang)
      }
      // Only append surat_jalan if it's a valid File object
      if (transport.surat_jalan && transport.surat_jalan instanceof File) {
        formData.append(`transports[${index}][surat_jalan]`, transport.surat_jalan)
      } else if (transport.old_surat_jalan) {
        formData.append(`transports[${index}][old_surat_jalan]`, transport.old_surat_jalan)
      }
    })

    if (isEditing.value && form.value.id) {
      formData.append('_method', 'PUT')
      await api.post(`/penggilingan/${form.value.id}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      alert('Data berhasil diupdate')
    } else {
      await api.post('/penggilingan', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      alert('Data berhasil ditambahkan')
    }

    closeModal()
    fetchData()
  } catch (error) {
    console.error('Error submitting form:', error)
    console.error('Error response:', error.response?.data)
    
    // Tampilkan error yang lebih detail
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const errorMessages = Object.values(errors).flat().join('\n')
      alert('Validasi gagal:\n' + errorMessages)
    } else {
      alert(error.response?.data?.message || 'Gagal menyimpan data')
    }
  }
}

const editItem = (item) => {
  isEditing.value = true
  
  // Format tanggal ke YYYY-MM-DD untuk input type="date"
  let tanggalFormatted = ''
  if (item.tanggal_pengajuan) {
    const date = new Date(item.tanggal_pengajuan)
    tanggalFormatted = date.toISOString().split('T')[0]
  }
  
  form.value = {
    id: item.id,
    tanggal_pengajuan: tanggalFormatted,
    nama_penggilingan: item.nama_penggilingan,
    lokasi_makloon: item.lokasi_makloon,
    foto_gkp_1: null,
    foto_gkp_2: null,
    foto_gkp_1_preview: item.foto_gkp_1
      ? `http://localhost:8000/storage/${item.foto_gkp_1}`
      : '',
    foto_gkp_2_preview: item.foto_gkp_2
      ? `http://localhost:8000/storage/${item.foto_gkp_2}`
      : '',
    old_foto_gkp_1: item.foto_gkp_1 || '',
    old_foto_gkp_2: item.foto_gkp_2 || '',
    transports: item.transports.map((t) => ({
      nama_pengemudi: t.nama_pengemudi,
      nopol: t.nopol,
      kuantum: parseFloat(t.kuantum), // Remove trailing zeros
      nota_timbang: null,
      nota_timbang_preview: t.nota_timbang
        ? `http://localhost:8000/storage/${t.nota_timbang}`
        : '',
      nota_timbang_is_pdf: isPdfPath(t.nota_timbang),
      old_nota_timbang: t.nota_timbang || '',
      surat_jalan: null,
      surat_jalan_preview: t.surat_jalan
        ? `http://localhost:8000/storage/${t.surat_jalan}`
        : '',
      surat_jalan_is_pdf: isPdfPath(t.surat_jalan),
      old_surat_jalan: t.surat_jalan || '',
    })),
  }
  showModal.value = true
}

const deleteItem = async (id) => {
  if (!confirm('Yakin ingin menghapus data ini?')) return

  try {
    await api.delete(`/penggilingan/${id}`)
    alert('Data berhasil dihapus')
    fetchData()
  } catch (error) {
    console.error('Error deleting data:', error)
    alert('Gagal menghapus data')
  }
}

const viewDetail = (item) => {
  selectedItem.value = item
  showDetailModal.value = true
}

const openAddModal = () => {
  showModal.value = true
  // Untuk role penggilingan, langsung pre-fill nama penggilingan dari profil
  if (authStore.isPenggilingan) {
    form.value.nama_penggilingan = authStore.namaPenggilingan || ''
  }
}

const closeModal = () => {
  showModal.value = false
  isEditing.value = false
  form.value = {
    id: null,
    tanggal_pengajuan: '',
    // Pre-fill untuk role penggilingan
    nama_penggilingan: authStore.isPenggilingan ? (authStore.namaPenggilingan || '') : '',
    lokasi_makloon: '',
    foto_gkp_1: null,
    foto_gkp_2: null,
    foto_gkp_1_preview: '',
    foto_gkp_2_preview: '',
    old_foto_gkp_1: '',
    old_foto_gkp_2: '',
    transports: [
      {
        nama_pengemudi: '',
        nopol: '',
        kuantum: '',
        nota_timbang: null,
        nota_timbang_preview: '',
        nota_timbang_is_pdf: false,
        old_nota_timbang: '',
        surat_jalan: null,
        surat_jalan_preview: '',
        surat_jalan_is_pdf: false,
        old_surat_jalan: '',
      },
    ],
  }
}

const openVerifikasiModal = (item) => {
  verifikasiItem.value = item
  verifikasiForm.value = {
    status_verifikasi: item.status_verifikasi || 'pending',
    catatan_verifikasi: item.catatan_verifikasi || ''
  }
  showVerifikasiModal.value = true
}

const submitVerifikasi = async () => {
  if (!verifikasiItem.value) return
  verifikasiLoading.value = true
  try {
    await api.post(`/penggilingan/${verifikasiItem.value.id}/verifikasi`, verifikasiForm.value)
    alert('Status verifikasi berhasil disimpan')
    showVerifikasiModal.value = false
    fetchData()
  } catch (error) {
    console.error('Error verifikasi:', error)
    alert(error.response?.data?.message || 'Gagal menyimpan verifikasi')
  } finally {
    verifikasiLoading.value = false
  }
}

const formatNumber = (value) => {
  if (!value) return '0'
  const num = parseFloat(String(value))
  return num % 1 === 0
    ? num.toLocaleString('id-ID')
    : num.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  })
}

const downloadMakloonGKP = async (penggilinganId) => {
  try {
    const response = await api.get(`/penggilingan/${penggilinganId}/export-makloon-gkp`, {
      responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `form_gkp_maklon_${penggilinganId}_${new Date().getFullYear()}.xlsx`)
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Error downloading:', error)
    alert('Gagal download Form GKP Maklon')
  }
}

</script>
<style scoped>
.penggilingan-container {
  padding: 20px;
}

/* Hero Banner */
.hero-banner.orange {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
  border-radius: 16px;
  padding: 1.75rem 2rem;
  color: #fff;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 20px rgba(234, 88, 12, 0.15);
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
.icon-red { background: #fef2f2; color: #ef4444; }
.text-green-val { color: #10b981; font-weight: 700; }
.text-orange-val { color: #f97316; font-weight: 700; }
.text-red-val { color: #ef4444; font-weight: 700; }

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
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
}

/* Buttons */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #f97316, #ea580c);
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
  margin-top: 0.5rem;
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
.badge-status {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 5px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}
.badge-pending { background: #fef3c7; color: #d97706; }
.badge-disetujui { background: #d1e7dd; color: #0f5132; }
.badge-ditolak { background: #f8d7da; color: #842029; }

/* Action Buttons */
.td-actions {
  width: 160px;
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
.btn-view { background: #f0fdf4; border-color: #bbf7d0; color: #16a34a; }
.btn-view:hover { background: #dcfce7; }
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
  border-top-color: #f97316;
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
.detail-modal {
  max-width: 680px;
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
  border: 1px solid #e2e8f0;
}
.form-section h3 {
  font-size: 0.88rem;
  font-weight: 700;
  color: #1e3a8a;
  margin-bottom: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  display: flex;
  align-items: center;
  gap: 6px;
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
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
}
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
.required { color: #ef4444; }

.modal-footer {
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
.detail-section h3, .detail-section h4 {
  font-size: 0.88rem;
  font-weight: 700;
  color: #1e3a8a;
  margin-bottom: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  display: flex;
  align-items: center;
  gap: 6px;
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
.detail-label {
  font-size: 0.78rem;
  color: #8a96a8;
  font-weight: 500;
}
.detail-value {
  font-size: 0.88rem;
  color: #1f2937;
  font-weight: 600;
}

/* Specific to Penggilingan */
.preview-image, .preview-image-small {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin-top: 8px;
  border: 1px solid #cbd5e1;
}
.preview-image { max-width: 200px; }
.preview-image-small { max-width: 120px; }

.photo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 15px;
}
.photo-item {
  text-align: center;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 10px;
}
.photo-item label {
  display: block;
  font-size: 0.75rem;
  font-weight: 600;
  color: #4b5563;
  margin-bottom: 6px;
}
.photo-item img {
  width: 100%;
  height: 150px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
  transition: transform 0.2s;
}
.photo-item img:hover {
  transform: scale(1.03);
}

.transport-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 0.5rem;
}
.transport-item {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.25rem;
}
.transport-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 0.5rem;
}
.transport-header h4 {
  font-size: 0.9rem;
  font-weight: 700;
  color: #1e3a8a;
  margin: 0;
}
.btn-add-transport {
  background: #eff6ff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}
.btn-add-transport:hover {
  background: #dbeafe;
}
.btn-remove-transport {
  background: #fef2f2;
  color: #ef4444;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 4px 10px;
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
}
.btn-remove-transport:hover {
  background: #fee2e2;
}

.transport-table-wrapper {
  overflow-x: auto;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
}
.transport-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.85rem;
}
.transport-table th {
  background: #f8fafc;
  padding: 8px 12px;
  border-bottom: 1px solid #e2e8f0;
  font-weight: 600;
  color: #4b5563;
}
.transport-table td {
  padding: 8px 12px;
  border-bottom: 1px solid #f1f5f9;
}

.resume-section {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 12px;
  padding: 1rem;
}
.resume-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
.resume-item {
  display: flex;
  flex-direction: column;
}
.resume-label {
  font-size: 0.75rem;
  color: #1e3a8a;
  font-weight: 600;
}
.resume-value {
  font-size: 1.1rem;
  font-weight: 700;
  color: #15803d;
}

.btn-doc-open {
  background: #f8fafc;
  color: #1d4ed8;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 0.85rem;
  cursor: pointer;
}
.btn-doc-open:hover {
  background: #eef2ff;
}

/* Verification Modal */
.modal-verifikasi {
  max-width: 460px;
}
.verifikasi-info {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 12px;
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
}
.modal-footer-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}
.btn-submit-verifikasi {
  padding: 8px 18px;
  background: #1565c0;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
}
.btn-submit-verifikasi:hover { background: #0d47a1; }
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
  border: 1.5px solid #f97316;
  border-radius: 8px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
  color: #f97316;
  transition: all 0.18s;
  box-shadow: 0 1px 4px rgba(249,115,22,0.08);
}
.btn-col-picker:hover, .btn-col-picker.active {
  background: #f97316;
  color: #fff;
  box-shadow: 0 2px 8px rgba(249,115,22,0.22);
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
  background: #fff7ed;
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
.col-picker-item:hover { background: #fff7ed; }
.col-picker-item.col-active {
  background: #ffedd5;
  color: #7c2d12;
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
  background: #f97316;
  border-color: #f97316;
  color: #fff;
}
.col-label {
  flex: 1;
  font-family: monospace;
  font-size: 12px;
}

@media (max-width: 640px) {
  .modal-content {
    overflow-y: auto;
    padding: 12px;
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: 8px;
  }

  .detail-grid {
    grid-template-columns: 1fr;
    gap: 8px;
  }

  .photo-grid {
    grid-template-columns: 1fr;
  }

  .resume-grid {
    grid-template-columns: 1fr;
  }

  .modal-footer {
    flex-direction: row;
    flex-wrap: wrap;
    gap: 6px;
  }

  .modal-footer button {
    flex: 1;
    padding: 8px 10px;
    font-size: 12px;
  }
}
</style>
