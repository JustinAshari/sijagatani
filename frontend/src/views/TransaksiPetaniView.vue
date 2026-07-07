<template>
  <div class="transaksi-petani-page">

    <!-- Hero Banner -->
    <div class="hero-banner">
      <div class="hero-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="1" x2="12" y2="23"></line>
          <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
        </svg>
      </div>
      <div class="hero-text">
        <h2>Transaksi Petani</h2>
        <p>Kelola dan pantau transaksi pembelian atau operasional petani terdaftar</p>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon icon-blue">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/>
            <line x1="12" y1="4" x2="12" y2="20"/>
            <line x1="2" y1="10" x2="22" y2="10"/>
          </svg>
        </div>
        <div class="stat-info">
          <h3>Total Transaksi</h3>
          <p class="stat-val">{{ statistics.totalCount }}</p>
          <span class="stat-sub">Semua status transaksi</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon icon-blue">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
        </div>
        <div class="stat-info">
          <h3>Total Nominal</h3>
          <p class="stat-val">{{ formatRupiah(statistics.totalNominal) }}</p>
          <span class="stat-sub">Semua transaksi</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon icon-green">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
        </div>
        <div class="stat-info">
          <h3>Nominal Sudah Transfer</h3>
          <p class="stat-val text-green-val">{{ formatRupiah(statistics.nominalSudah) }}</p>
          <span class="stat-sub">Transaksi berhasil ditransfer</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon icon-red">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
        </div>
        <div class="stat-info">
          <h3>Nominal Belum Transfer</h3>
          <p class="stat-val text-red-val">{{ formatRupiah(statistics.nominalBelum) }}</p>
          <span class="stat-sub">Belum ditransfer</span>
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
          <p class="stat-val text-green-val">{{ formatVolume(statistics.volumeDiterima) }} KG</p>
          <span class="stat-sub">Transaksi disetujui</span>
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
          <p class="stat-val text-orange-val">{{ formatVolume(statistics.volumePending) }} KG</p>
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
          <p class="stat-val text-red-val">{{ formatVolume(statistics.volumeDitolak) }} KG</p>
          <span class="stat-sub">Transaksi ditolak</span>
        </div>
      </div>
    </div>

    <!-- Main Card Content -->
    <div class="page-card">
      <div class="toolbar">
        <div class="toolbar-left">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari nama petani atau NIK..."
            class="search-input"
          />
          <FilterDropdown
            :active-count="activeFilterCount"
            @apply="applyFilters"
            @reset="resetFilters"
          >
            <div class="fd-field">
              <label class="fd-label">Status Transfer</label>
              <select v-model="filterStatus" class="fd-select">
                <option value="">Semua Status</option>
                <option value="belum">Belum Transfer</option>
                <option value="sudah">Sudah Transfer</option>
                <option value="ditolak">Ditolak</option>
              </select>
            </div>
            <div class="fd-field">
              <label class="fd-label">Status Verifikasi</label>
              <select v-model="filterStatusVerifikasi" class="fd-select">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
              </select>
            </div>
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
              <label class="fd-label">Tanggal Dari</label>
              <input v-model="filterTanggalDari" type="date" class="fd-input" />
            </div>
            <div class="fd-field">
              <label class="fd-label">Tanggal Sampai</label>
              <input v-model="filterTanggalSampai" type="date" class="fd-input" />
            </div>
          </FilterDropdown>
        </div>
        <button v-if="authStore.canManagePetani" @click="openAdd" class="btn-primary">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-inline">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          Tambah Transaksi
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-wrap">
        <div class="spinner"></div>
        <span>Memuat data transaksi...</span>
      </div>

      <!-- Empty State -->
      <div v-else-if="!filteredTransaksi.length" class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <p>Belum ada data transaksi</p>
        <small>Silakan tambahkan data transaksi baru atau sesuaikan filter Anda.</small>
      </div>

      <!-- Data Table -->
      <div v-else class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Petani</th>
              <th>NIK</th>
              <th>Komoditas</th>
              <th>Tanggal Transaksi</th>
              <th>Volume (KG)</th>
              <th>Harga/KG (Rp)</th>
              <th>Nominal (Rp)</th>
              <th>Status Transfer</th>
              <th>Status Verifikasi</th>
              <th>Catatan Verifikasi</th>
              <th>Aksi</th>
              <th v-if="authStore.canVerify">Verifikasi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(tx, idx) in paginatedTransaksi" :key="tx.id">
              <td class="td-num">{{ rowNumber(idx) }}</td>
              <td class="td-name">{{ tx.petani?.nama || '-' }}</td>
              <td><code class="nik-code">{{ tx.petani?.nik || '-' }}</code></td>
              <td>
                <span class="badge" :class="tx.komoditas ? `badge-${tx.komoditas.toLowerCase()}` : ''">
                  {{ tx.komoditas || '-' }}
                </span>
              </td>
              <td class="td-date">{{ formatDate(tx.tanggal_transaksi) }}</td>
              <td class="text-right font-medium">{{ formatVolume(tx.volume_kg) }} KG</td>
              <td class="text-right font-medium">{{ formatRupiah(tx.harga_per_kg) }}</td>
              <td class="text-right font-semibold text-blue">{{ formatRupiah(tx.nominal) }}</td>
              <td class="text-center">
                <span class="badge-status" :class="'badge-' + tx.status_transaksi">
                  {{ tx.status_transaksi === 'sudah' ? 'Sudah Transfer' : tx.status_transaksi === 'ditolak' ? 'Ditolak' : 'Belum Transfer' }}
                </span>
              </td>
              <td class="text-center">
                <span class="badge-status" :class="'badge-' + (tx.status_verifikasi || 'pending')">
                  {{ tx.status_verifikasi === 'disetujui' ? 'Disetujui' : tx.status_verifikasi === 'ditolak' ? 'Ditolak' : 'Pending' }}
                </span>
              </td>
              <td>{{ tx.catatan_verifikasi || '-' }}</td>
              <td class="td-actions">
                <button @click="viewDetail(tx)" class="btn-icon btn-view" title="Detail">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
                <button v-if="authStore.canManagePetani" @click="openEdit(tx)" class="btn-icon btn-edit" title="Edit">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <button v-if="authStore.canManagePetani" @click="confirmDelete(tx)" class="btn-icon btn-del" title="Hapus">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
              </td>
              <td v-if="authStore.canVerify" class="text-center verifikasi-cell">
                <button @click="openVerifikasiModal(tx)" class="btn-icon btn-view btn-verify" title="Verifikasi" style="background: #e0f2fe; border-color: #bae6fd; color: #0284c7;">
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

      <div class="pagination-bar" v-if="filteredTransaksi.length">
        <div class="pagination-info">
          Menampilkan {{ pageStart }}-{{ pageEnd }} dari {{ filteredTransaksi.length }} data
        </div>
        <div class="pagination-controls">
          <label for="tx-per-page">Baris:</label>
          <select id="tx-per-page" v-model="perPage" class="per-page-select">
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
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h3>{{ isEdit ? 'Edit Transaksi Petani' : 'Tambah Transaksi Petani' }}</h3>
          <button class="btn-close" @click="closeModal">&times;</button>
        </div>

        <form @submit.prevent="submitForm" class="modal-form">
          <div class="form-group">
            <label>Petani <span class="req">*</span></label>
            <div ref="selectPetaniContainer" class="searchable-select">
              <input
                type="text"
                v-model="searchPetaniQuery"
                @focus="showPetaniDropdown = true"
                placeholder="Cari nama atau NIK petani..."
                class="searchable-select-input"
                :disabled="isEdit"
              />
              <div v-if="showPetaniDropdown && !isEdit" class="searchable-select-dropdown">
                <div
                  v-for="p in filteredPetaniOptions"
                  :key="p.id"
                  @click="selectPetaniOption(p)"
                  class="searchable-select-option"
                >
                  {{ p.nama }} (NIK: {{ p.nik }})
                </div>
                <div v-if="filteredPetaniOptions.length === 0" class="searchable-select-no-results">
                  Tidak ada hasil
                </div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Tanggal Transaksi <span class="req">*</span></label>
            <input v-model="form.tanggal_transaksi" type="date" required />
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Komoditas <span class="req">*</span></label>
              <select v-model="form.komoditas" required>
                <option value="Gabah">Gabah</option>
                <option value="Beras">Beras</option>
                <option value="Jagung">Jagung</option>
              </select>
            </div>

            <div class="form-group">
              <label>Volume (KG) <span class="req">*</span></label>
              <input v-model="form.volume_kg" type="number" step="0.01" min="0.01" placeholder="Contoh: 1500" required />
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Harga per KG (Rp) <span class="req">*</span></label>
              <input v-model="form.harga_per_kg" type="number" step="0.01" min="0.01" placeholder="Contoh: 7000" required />
            </div>

            <div class="form-group">
              <label>Total Nominal (Rp) <span class="req">*</span></label>
              <input v-model="form.nominal" type="number" min="0" placeholder="Otomatis dihitung..." readonly class="input-readonly" />
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label>Bank</label>
              <input v-model="form.bank" type="text" placeholder="Contoh: BRI, Mandiri" />
            </div>

            <div class="form-group">
              <label>Nomor Rekening</label>
              <input v-model="form.no_rekening" type="text" placeholder="Contoh: 1234567890" />
            </div>
          </div>

          <div class="form-group" v-if="authStore.canVerify">
            <label>Status Transfer <span class="req">*</span></label>
            <select v-model="form.status_transaksi" required>
              <option value="belum">Belum Transfer</option>
              <option value="sudah">Sudah Transfer</option>
              <option value="ditolak">Ditolak</option>
            </select>
          </div>

          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
            <button type="submit" class="btn-primary" :disabled="saving">
              <span v-if="saving" class="btn-spinner"></span>
              {{ isEdit ? 'Simpan Perubahan' : 'Tambah Transaksi' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Detail -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="showDetailModal = false">
      <div class="modal detail-modal">
        <div class="modal-header">
          <h3>Detail Transaksi Petani</h3>
          <button class="btn-close" @click="showDetailModal = false">&times;</button>
        </div>

        <div class="modal-body" v-if="selectedTx">
          <div class="detail-section">
            <h4>Informasi Transaksi</h4>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="det-label">Tanggal Transaksi</span>
                <span class="det-val">{{ formatDate(selectedTx.tanggal_transaksi) }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Status Transfer</span>
                <span class="badge-status" :class="'badge-' + selectedTx.status_transaksi">
                  {{ selectedTx.status_transaksi === 'sudah' ? 'Sudah Transfer' : selectedTx.status_transaksi === 'ditolak' ? 'Ditolak' : 'Belum Transfer' }}
                </span>
              </div>
              <div class="detail-item">
                <span class="det-label">Bank</span>
                <span class="det-val">{{ selectedTx.bank || '-' }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Nomor Rekening</span>
                <span class="det-val">{{ selectedTx.no_rekening || '-' }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Komoditas</span>
                <span class="badge" :class="selectedTx.komoditas ? `badge-${selectedTx.komoditas.toLowerCase()}` : ''">
                  {{ selectedTx.komoditas || '-' }}
                </span>
              </div>
              <div class="detail-item">
                <span class="det-label">Volume</span>
                <span class="det-val font-medium">{{ formatVolume(selectedTx.volume_kg) }} KG</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Harga per KG</span>
                <span class="det-val font-medium">{{ formatRupiah(selectedTx.harga_per_kg) }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Total Nominal</span>
                <span class="det-val font-semibold text-blue">{{ formatRupiah(selectedTx.nominal) }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Status Verifikasi</span>
                <span class="badge-status" :class="'badge-' + (selectedTx.status_verifikasi || 'pending')">
                  {{ selectedTx.status_verifikasi === 'disetujui' ? 'Disetujui' : selectedTx.status_verifikasi === 'ditolak' ? 'Ditolak' : 'Pending' }}
                </span>
              </div>
              <div class="detail-item">
                <span class="det-label">Catatan Verifikasi</span>
                <span class="det-val">{{ selectedTx.catatan_verifikasi || '-' }}</span>
              </div>
              <div class="detail-item" v-if="selectedTx.verifier">
                <span class="det-label">Diverifikasi Oleh</span>
                <span class="det-val">{{ selectedTx.verifier?.name || '-' }}</span>
              </div>
              <div class="detail-item" v-if="selectedTx.verified_at">
                <span class="det-label">Waktu Verifikasi</span>
                <span class="det-val">{{ formatDate(selectedTx.verified_at) }}</span>
              </div>
            </div>
          </div>

          <div class="detail-section border-top">
            <h4>Informasi Petani</h4>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="det-label">Nama Petani</span>
                <span class="det-val font-semibold">{{ selectedTx.petani?.nama || '-' }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">NIK</span>
                <span class="det-val"><code class="nik-code">{{ selectedTx.petani?.nik || '-' }}</code></span>
              </div>
              <div class="detail-item">
                <span class="det-label">No. Telepon</span>
                <span class="det-val">{{ selectedTx.petani?.no_telepon || '-' }}</span>
              </div>
              <div class="detail-item">
                <span class="det-label">Komoditi Terdaftar</span>
                <span class="badge" :class="selectedTx.petani?.komoditi ? `badge-${selectedTx.petani.komoditi.toLowerCase()}` : ''">
                  {{ selectedTx.petani?.komoditi || '-' }}
                </span>
              </div>
              <div class="detail-item full-width">
                <span class="det-label">Alamat Petani</span>
                <span class="det-val">{{ selectedTx.petani?.alamat || '-' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal Verifikasi -->
    <div v-if="showVerifikasiModal" class="modal-overlay" @click.self="showVerifikasiModal = false">
      <div class="modal">
        <div class="modal-header">
          <h3>Verifikasi Transaksi Petani</h3>
          <button class="btn-close" @click="showVerifikasiModal = false">&times;</button>
        </div>
        <div class="modal-body">
          <p class="verifikasi-info" style="margin-bottom: 1.2rem; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.88rem;">
            <strong>Nama Petani:</strong> {{ verifikasiItem?.petani?.nama }}<br>
            <strong>Nominal:</strong> {{ formatRupiah(verifikasiItem?.nominal) }}<br>
            <strong>Volume:</strong> {{ formatVolume(verifikasiItem?.volume_kg) }} KG
          </p>
          <div class="form-group">
            <label>Status Verifikasi <span class="req">*</span></label>
            <select v-model="verifikasiForm.status_verifikasi" class="select-status" style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem;">
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
              style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.875rem; resize: vertical;"
            ></textarea>
          </div>
          <div class="modal-actions" style="margin-top: 1.5rem;">
            <button @click="showVerifikasiModal = false" class="btn-secondary">Batal</button>
            <button @click="submitVerifikasi" class="btn-primary" :disabled="verifikasiLoading" style="background: linear-gradient(135deg, #10b981, #059669); border: none;">
              {{ verifikasiLoading ? 'Menyimpan...' : 'Simpan Verifikasi' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'
import FilterDropdown from '../components/FilterDropdown.vue'

const authStore = useAuthStore()

const transaksiList = ref([])
const petaniList = ref([])
const loading = ref(true)
const saving = ref(false)
const showModal = ref(false)
const showDetailModal = ref(false)
const isEdit = ref(false)
const selectedTx = ref(null)

const emptyForm = () => ({
  id: null,
  petani_id: '',
  komoditas: 'Gabah',
  tanggal_transaksi: new Date().toISOString().split('T')[0],
  volume_kg: '',
  harga_per_kg: '',
  nominal: '',
  status_transaksi: 'belum',
  bank: '',
  no_rekening: ''
})

const form = ref(emptyForm())

const searchPetaniQuery = ref('')
const showPetaniDropdown = ref(false)
const selectPetaniContainer = ref(null)
const currentPage = ref(1)
const perPage = ref('10')

const filteredPetaniOptions = computed(() => {
  const query = searchPetaniQuery.value.toLowerCase().trim()
  if (!query) return petaniList.value
  return petaniList.value.filter(p => 
    p.nama.toLowerCase().includes(query) || 
    p.nik.includes(query)
  )
})

const selectPetaniOption = (p) => {
  form.value.petani_id = p.id
  searchPetaniQuery.value = `${p.nama} (NIK: ${p.nik})`
  showPetaniDropdown.value = false
  
  // Auto-populate bank and account number from selected farmer profile
  form.value.bank = p.bank || ''
  form.value.no_rekening = p.no_rekening || ''

  // Auto-populate komoditas from selected farmer profile
  form.value.komoditas = p.komoditi || 'Gabah'
}

const handleDocumentClick = (e) => {
  if (selectPetaniContainer.value && !selectPetaniContainer.value.contains(e.target)) {
    showPetaniDropdown.value = false
  }
}

watch(searchPetaniQuery, (newVal) => {
  if (!newVal) {
    form.value.petani_id = ''
  }
})

// Auto-calculate nominal based on volume_kg and harga_per_kg
watch(() => [form.value.volume_kg, form.value.harga_per_kg], ([vol, price]) => {
  const v = parseFloat(vol) || 0
  const p = parseFloat(price) || 0
  form.value.nominal = (v * p).toFixed(2)
})

// Filters
const searchQuery = ref('')
const filterStatus = ref('')
const filterStatusVerifikasi = ref('')
const filterTanggalDari = ref('')
const filterTanggalSampai = ref('')
const filterKomoditi = ref('')
const filterProvinsi = ref('')
const filterKabupatenId = ref('')
const filterKecamatan = ref('')
const filterKalurahan = ref('')

// Region lists
const provinsiList = ref([])
const allKabupaten = ref([])
const allKecamatan = ref([])
const allKalurahan = ref([])

// Dynamic filtered options
const filterKabupatenOptions = ref([])
const filterKecamatanOptions = ref([])
const filterKalurahanOptions = ref([])

const activeFilterCount = computed(() => {
  let count = 0
  if (filterStatus.value) count++
  if (filterStatusVerifikasi.value) count++
  if (filterKomoditi.value) count++
  if (filterProvinsi.value) count++
  if (filterKabupatenId.value) count++
  if (filterKecamatan.value) count++
  if (filterKalurahan.value) count++
  if (filterTanggalDari.value) count++
  if (filterTanggalSampai.value) count++
  return count
})

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

// Statistics computed
const statistics = computed(() => {
  let count = 0
  let totalNominal = 0
  let nominalSudah = 0
  let nominalBelum = 0
  let volumeDiterima = 0
  let volumePending = 0
  let volumeDitolak = 0

  transaksiList.value.forEach(tx => {
    count++
    const nom = parseFloat(tx.nominal) || 0
    totalNominal += nom
    
    if (tx.status_transaksi === 'sudah') {
      nominalSudah += nom
    } else if (tx.status_transaksi === 'belum') {
      nominalBelum += nom
    }
    
    const vol = parseFloat(tx.volume_kg) || 0
    if (tx.status_verifikasi === 'disetujui') {
      volumeDiterima += vol
    } else if (!tx.status_verifikasi || tx.status_verifikasi === 'pending') {
      volumePending += vol
    } else if (tx.status_verifikasi === 'ditolak') {
      volumeDitolak += vol
    }
  })

  return {
    totalCount: count,
    totalNominal,
    nominalSudah,
    nominalBelum,
    volumeDiterima,
    volumePending,
    volumeDitolak
  }
})

// Fetch all transactions
const fetchTransaksi = async () => {
  loading.value = true
  try {
    const params = {}
    if (filterStatus.value) params.status_transaksi = filterStatus.value
    if (filterStatusVerifikasi.value) params.status_verifikasi = filterStatusVerifikasi.value
    if (filterKomoditi.value) params.komoditas = filterKomoditi.value
    if (filterProvinsi.value) params.provinsi_id = filterProvinsi.value
    if (filterKabupatenId.value) params.kabupaten_id = filterKabupatenId.value
    if (filterKecamatan.value) params.kecamatan_id = filterKecamatan.value
    if (filterKalurahan.value) params.kalurahan_id = filterKalurahan.value
    if (filterTanggalDari.value) params.tanggal_dari = filterTanggalDari.value
    if (filterTanggalSampai.value) params.tanggal_sampai = filterTanggalSampai.value

    const res = await api.get('/transaksi-petani', { params })
    transaksiList.value = res.data.data || []
  } catch {
    alert('Gagal memuat data transaksi')
  } finally {
    loading.value = false
  }
}

// Fetch all petani for dropdown selection
const fetchPetaniList = async () => {
  try {
    const res = await api.get('/petani')
    petaniList.value = res.data.data || []
  } catch {
    console.error('Gagal mengambil daftar petani')
  }
}

const applyFilters = async () => {
  currentPage.value = 1
  await fetchTransaksi()
}

const resetFilters = async () => {
  filterStatus.value = ''
  filterStatusVerifikasi.value = ''
  filterKomoditi.value = ''
  filterProvinsi.value = ''
  filterKabupatenId.value = ''
  filterKecamatan.value = ''
  filterKalurahan.value = ''
  filterTanggalDari.value = ''
  filterTanggalSampai.value = ''
  searchQuery.value = ''
  filterKabupatenOptions.value = []
  filterKecamatanOptions.value = []
  filterKalurahanOptions.value = []
  currentPage.value = 1
  await fetchTransaksi()
}

// Filtered list based on search query
const filteredTransaksi = computed(() => {
  let result = transaksiList.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(tx => 
      tx.petani?.nama?.toLowerCase().includes(query) ||
      tx.petani?.nik?.includes(query)
    )
  }

  return result
})

const totalPages = computed(() => {
  if (perPage.value === 'all') return 1
  const size = Number(perPage.value) || 10
  return Math.max(1, Math.ceil(filteredTransaksi.value.length / size))
})

const paginatedTransaksi = computed(() => {
  if (perPage.value === 'all') return filteredTransaksi.value
  const size = Number(perPage.value) || 10
  const start = (currentPage.value - 1) * size
  return filteredTransaksi.value.slice(start, start + size)
})

const pageStart = computed(() => {
  if (!filteredTransaksi.value.length) return 0
  if (perPage.value === 'all') return 1
  return (currentPage.value - 1) * (Number(perPage.value) || 10) + 1
})

const pageEnd = computed(() => {
  if (!filteredTransaksi.value.length) return 0
  if (perPage.value === 'all') return filteredTransaksi.value.length
  return Math.min(currentPage.value * (Number(perPage.value) || 10), filteredTransaksi.value.length)
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

watch([filteredTransaksi, perPage], () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = totalPages.value
  }
  if (currentPage.value < 1) {
    currentPage.value = 1
  }
})

const openAdd = () => {
  isEdit.value = false
  form.value = emptyForm()
  searchPetaniQuery.value = ''
  showPetaniDropdown.value = false
  showModal.value = true
}

const openEdit = (tx) => {
  isEdit.value = true
  form.value = {
    id: tx.id,
    petani_id: tx.petani_id,
    komoditas: tx.komoditas || 'Gabah',
    tanggal_transaksi: tx.tanggal_transaksi ? tx.tanggal_transaksi.split('T')[0] : '',
    volume_kg: tx.volume_kg,
    harga_per_kg: tx.harga_per_kg,
    nominal: tx.nominal,
    status_transaksi: tx.status_transaksi,
    bank: tx.bank || '',
    no_rekening: tx.no_rekening || ''
  }
  const p = petaniList.value.find(item => item.id === tx.petani_id) || tx.petani
  searchPetaniQuery.value = p ? `${p.nama} (NIK: ${p.nik})` : ''
  showPetaniDropdown.value = false
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const submitForm = async () => {
  saving.value = true
  try {
    const payload = { ...form.value }
    
    if (isEdit.value) {
      await api.put(`/transaksi-petani/${form.value.id}`, payload)
      alert('Transaksi berhasil diperbarui')
    } else {
      await api.post('/transaksi-petani', payload)
      alert('Transaksi berhasil ditambahkan')
    }
    
    closeModal()
    await fetchTransaksi()
  } catch (err) {
    const msg = err.response?.data?.message || 'Terjadi kesalahan'
    const errors = err.response?.data?.errors
    if (errors) {
      const detail = Object.values(errors).flat().join('\n')
      alert(msg + '\n' + detail)
    } else {
      alert(msg)
    }
  } finally {
    saving.value = false
  }
}

const confirmDelete = async (tx) => {
  const name = tx.petani?.nama || '-'
  if (!confirm(`Hapus transaksi untuk petani "${name}" senilai Rp ${formatNumber(tx.nominal)}? Tindakan ini tidak dapat dibatalkan.`)) return
  try {
    await api.delete(`/transaksi-petani/${tx.id}`)
    transaksiList.value = transaksiList.value.filter(item => item.id !== tx.id)
    alert('Transaksi berhasil dihapus')
  } catch {
    alert('Gagal menghapus transaksi')
  }
}

const viewDetail = (tx) => {
  selectedTx.value = tx
  showDetailModal.value = true
}

// Helpers
const formatDate = (dt) => {
  if (!dt) return '-'
  return new Date(dt).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const formatNumber = (num) => {
  if (!num) return '0'
  return new Intl.NumberFormat('id-ID').format(num)
}

const formatVolume = (vol) => {
  if (!vol) return '0'
  return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(vol)
}

const formatRupiah = (val) => {
  if (val === null || val === undefined) return 'Rp 0'
  return 'Rp ' + formatNumber(val)
}

const showVerifikasiModal = ref(false)
const verifikasiItem = ref(null)
const verifikasiLoading = ref(false)
const verifikasiForm = ref({
  status_verifikasi: 'pending',
  catatan_verifikasi: ''
})

const openVerifikasiModal = (tx) => {
  verifikasiItem.value = tx
  verifikasiForm.value = {
    status_verifikasi: tx.status_verifikasi || 'pending',
    catatan_verifikasi: tx.catatan_verifikasi || ''
  }
  showVerifikasiModal.value = true
}

const submitVerifikasi = async () => {
  if (!verifikasiItem.value) return
  verifikasiLoading.value = true
  try {
    await api.post(`/transaksi-petani/${verifikasiItem.value.id}/verifikasi`, verifikasiForm.value)
    alert('Status verifikasi berhasil disimpan')
    showVerifikasiModal.value = false
    await fetchTransaksi()
  } catch (error) {
    console.error('Error verifikasi:', error)
    alert(error.response?.data?.message || 'Gagal menyimpan verifikasi')
  } finally {
    verifikasiLoading.value = false
  }
}

onMounted(async () => {
  document.addEventListener('click', handleDocumentClick)
  await Promise.all([
    fetchTransaksi(),
    fetchPetaniList(),
    loadWilayahData()
  ])
})

onUnmounted(() => {
  document.removeEventListener('click', handleDocumentClick)
})
</script>

<style scoped>
.transaksi-petani-page {
  padding: 20px;
}

/* Hero Banner */
.hero-banner {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  border-radius: 16px;
  padding: 1.75rem 2rem;
  color: #fff;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 20px rgba(59, 130, 246, 0.15);
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
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
.text-green-val { color: #10b981; }
.text-orange-val { color: #f97316; }
.text-red-val { color: #ef4444; }

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
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
}

/* Buttons */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
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
.nik-code {
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  border-radius: 5px;
  padding: 2px 6px;
  font-size: 0.8rem;
  font-family: monospace;
}
.td-date { color: #6b7280; font-size: 0.82rem; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.font-medium { font-weight: 500; }
.font-semibold { font-weight: 600; }
.text-blue { color: #2563eb; }

/* Status Badges */
.badge-status {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 0.78rem;
  font-weight: 600;
  text-align: center;
}
.badge-pending {
  background: #fef3c7;
  border: 1px solid #fde68a;
  color: #92400e;
}
.badge-sukses {
  background: #d1fae5;
  border: 1px solid #a7f3d0;
  color: #065f46;
}
.badge-gagal {
  background: #fee2e2;
  border: 1px solid #fecaca;
  color: #991b1b;
}
.badge-belum {
  background: #fee2e2;
  border: 1px solid #fecaca;
  color: #991b1b;
}
.badge-sudah {
  background: #d1fae5;
  border: 1px solid #a7f3d0;
  color: #065f46;
}
.badge-disetujui {
  background: #d1fae5;
  border: 1px solid #a7f3d0;
  color: #065f46;
}
.badge-ditolak {
  background: #fee2e2;
  border: 1px solid #fecaca;
  color: #991b1b;
}

/* Komoditi Badges */
.badge {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 5px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}
.badge-gabah { background: #fef3c7; color: #d97706; }
.badge-jagung { background: #ffe4e6; color: #e11d48; }
.badge-beras { background: #e0f2fe; color: #0284c7; }

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
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Empty state */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 1rem;
  text-align: center;
  color: #9ea9b8;
}
.empty-state svg { width: 56px; height: 56px; margin-bottom: 1rem; }
.empty-state p { font-size: 1.05rem; font-weight: 600; color: #374151; margin-bottom: 0.4rem; }
.empty-state small { font-size: 0.82rem; }

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
  max-width: 560px;
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
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
}
.select-petani {
  appearance: none;
  background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='none'%3E%3Cpath stroke='%236b7280' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
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
  color: #1e3a8a;
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

.btn-spinner {
  display: inline-block;
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255, 255, 255, 0.4);
  border-top-color: #ffffff;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
  margin-right: 4px;
}

@media (max-width: 640px) {
  .hero-banner { padding: 1.25rem; }
  .stats-grid { grid-template-columns: 1fr; }
  .form-row-2 { grid-template-columns: 1fr; gap: 0; }
  .detail-grid { grid-template-columns: 1fr; }
  .detail-item.full-width { grid-column: span 1; }
  .data-table th:nth-child(3),
  .data-table td:nth-child(3),
  .data-table th:nth-child(4),
  .data-table td:nth-child(4) {
    display: none;
  }
}

/* Searchable Select Dropdown */
.searchable-select {
  position: relative;
  width: 100%;
}
.searchable-select-input {
  width: 100%;
  padding: 0.6rem 0.85rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  color: #0f172a;
  outline: none;
  box-sizing: border-box;
  background-color: #fff;
  cursor: pointer;
  transition: border-color 0.15s;
}
.searchable-select-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
}
.searchable-select-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  z-index: 2010;
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  margin-top: 4px;
  max-height: 200px;
  overflow-y: auto;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
.searchable-select-option {
  padding: 0.6rem 0.85rem;
  font-size: 0.875rem;
  color: #1f2937;
  cursor: pointer;
  text-align: left;
  transition: background-color 0.15s;
}
.searchable-select-option:hover {
  background-color: #f3f4f6;
  color: #1e3a8a;
}
.searchable-select-no-results {
  padding: 0.6rem 0.85rem;
  font-size: 0.875rem;
  color: #9ca3af;
  text-align: center;
}
.input-readonly {
  background-color: #f3f4f6 !important;
  color: #6b7280 !important;
  cursor: not-allowed !important;
}
</style>
