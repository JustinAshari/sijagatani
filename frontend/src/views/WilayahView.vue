<template>
  <div class="wilayah-container" style="padding: 20px;">

    <!-- Hero Banner -->
    <div class="hero-banner teal">
      <div class="hero-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polygon points="3 11 22 2 13 21 11 13 3 11"/>
        </svg>
      </div>
      <div class="hero-text">
        <h2>Data Wilayah</h2>
        <p>Kelola data wilayah administratif: provinsi, kabupaten, kecamatan, kalurahan</p>
      </div>
    </div>

    <!-- Main Card Content -->
    <div class="page-card">
      <div class="toolbar">
        <div class="toolbar-left">
          <!-- Tabs inside toolbar left for clean alignment -->
          <div class="tabs">
            <button 
              v-for="tab in tabs" 
              :key="tab.id"
              :class="['tab', { active: activeTab === tab.id }]"
              @click="changeTab(tab.id)">
              {{ tab.label }}
            </button>
          </div>
        </div>
        <div class="toolbar-right">
          <button @click="exportAllData" class="btn-secondary" style="margin-right: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-inline" style="width: 14px; height: 14px; margin-right: 4px; display: inline-block; vertical-align: middle;">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Export Semua Data
          </button>
          <button @click="showImportModal = true" class="btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-inline" style="width: 14px; height: 14px; margin-right: 4px; display: inline-block; vertical-align: middle;">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            Import Data
          </button>
        </div>
      </div>

      <!-- Content for each tab -->
      <div class="tab-content">
        <!-- PROVINSI -->
        <div v-if="activeTab === 'provinsi'" class="content-section">
          <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0d9488; margin: 0;">Data Provinsi</h3>
            <button @click="openModal('provinsi')" class="btn-primary">+ Tambah Provinsi</button>
          </div>
          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Provinsi</th>
                  <th>Jumlah Kabupaten</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="4" class="loading-cell">
                    <div class="loading-wrap">
                      <div class="spinner"></div>
                      <span>Memuat data provinsi...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="provinsiList.length === 0">
                  <td colspan="4" class="empty-cell" style="text-align: center; padding: 2rem 0; color: #9ea9b8;">Tidak ada data provinsi</td>
                </tr>
                <tr v-else v-for="(item, index) in paginatedActiveData" :key="item.id">
                  <td class="td-num">{{ rowNumber(index) }}</td>
                  <td class="font-semibold">{{ item.nama }}</td>
                  <td class="font-medium">{{ item.kabupaten_count || 0 }}</td>
                  <td class="td-actions">
                    <button @click="editItem('provinsi', item)" class="btn-icon btn-edit" title="Edit">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                      </svg>
                    </button>
                    <button @click="deleteItem('provinsi', item.id)" class="btn-icon btn-del" title="Hapus">
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
        </div>

        <!-- KABUPATEN -->
        <div v-if="activeTab === 'kabupaten'" class="content-section">
          <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0d9488; margin: 0;">Data Kabupaten</h3>
            <button @click="openModal('kabupaten')" class="btn-primary">+ Tambah Kabupaten</button>
          </div>
          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Kabupaten</th>
                  <th>Provinsi</th>
                  <th>Jumlah Kecamatan</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="5" class="loading-cell">
                    <div class="loading-wrap">
                      <div class="spinner"></div>
                      <span>Memuat data kabupaten...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="kabupatenList.length === 0">
                  <td colspan="5" class="empty-cell" style="text-align: center; padding: 2rem 0; color: #9ea9b8;">Tidak ada data kabupaten</td>
                </tr>
                <tr v-else v-for="(item, index) in paginatedActiveData" :key="item.id">
                  <td class="td-num">{{ rowNumber(index) }}</td>
                  <td class="font-semibold">{{ item.nama }}</td>
                  <td>{{ item.provinsi?.nama || '-' }}</td>
                  <td class="font-medium">{{ item.kecamatan_count || 0 }}</td>
                  <td class="td-actions">
                    <button @click="editItem('kabupaten', item)" class="btn-icon btn-edit" title="Edit">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                      </svg>
                    </button>
                    <button @click="deleteItem('kabupaten', item.id)" class="btn-icon btn-del" title="Hapus">
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
        </div>

        <!-- KECAMATAN -->
        <div v-if="activeTab === 'kecamatan'" class="content-section">
          <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0d9488; margin: 0;">Data Kecamatan</h3>
            <button @click="openModal('kecamatan')" class="btn-primary">+ Tambah Kecamatan</button>
          </div>
          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Kecamatan</th>
                  <th>Kabupaten</th>
                  <th>Provinsi</th>
                  <th>Jumlah Kalurahan</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="6" class="loading-cell">
                    <div class="loading-wrap">
                      <div class="spinner"></div>
                      <span>Memuat data kecamatan...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="kecamatanList.length === 0">
                  <td colspan="6" class="empty-cell" style="text-align: center; padding: 2rem 0; color: #9ea9b8;">Tidak ada data kecamatan</td>
                </tr>
                <tr v-else v-for="(item, index) in paginatedActiveData" :key="item.id">
                  <td class="td-num">{{ rowNumber(index) }}</td>
                  <td class="font-semibold">{{ item.nama }}</td>
                  <td>{{ item.kabupaten?.nama || '-' }}</td>
                  <td>{{ item.kabupaten?.provinsi?.nama || '-' }}</td>
                  <td class="font-medium">{{ item.kalurahan_count || 0 }}</td>
                  <td class="td-actions">
                    <button @click="editItem('kecamatan', item)" class="btn-icon btn-edit" title="Edit">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                      </svg>
                    </button>
                    <button @click="deleteItem('kecamatan', item.id)" class="btn-icon btn-del" title="Hapus">
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
        </div>

        <!-- KALURAHAN -->
        <div v-if="activeTab === 'kalurahan'" class="content-section">
          <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0d9488; margin: 0;">Data Kalurahan/Desa</h3>
            <button @click="openModal('kalurahan')" class="btn-primary">+ Tambah Kalurahan</button>
          </div>
          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Kalurahan/Desa</th>
                  <th>Kecamatan</th>
                  <th>Kabupaten</th>
                  <th>Provinsi</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="6" class="loading-cell">
                    <div class="loading-wrap">
                      <div class="spinner"></div>
                      <span>Memuat data kalurahan...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="kalurahanList.length === 0">
                  <td colspan="6" class="empty-cell" style="text-align: center; padding: 2rem 0; color: #9ea9b8;">Tidak ada data kalurahan/desa</td>
                </tr>
                <tr v-else v-for="(item, index) in paginatedActiveData" :key="item.id">
                  <td class="td-num">{{ rowNumber(index) }}</td>
                  <td class="font-semibold">{{ item.nama }}</td>
                  <td>{{ item.kecamatan?.nama || '-' }}</td>
                  <td>{{ item.kecamatan?.kabupaten?.nama || '-' }}</td>
                  <td>{{ item.kecamatan?.kabupaten?.provinsi?.nama || '-' }}</td>
                  <td class="td-actions">
                    <button @click="editItem('kalurahan', item)" class="btn-icon btn-edit" title="Edit">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                      </svg>
                    </button>
                    <button @click="deleteItem('kalurahan', item.id)" class="btn-icon btn-del" title="Hapus">
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
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination-bar" v-if="activeData.length">
        <div class="pagination-info">
          Menampilkan {{ pageStart }}-{{ pageEnd }} dari {{ activeData.length }} data
        </div>
        <div class="pagination-controls">
          <label for="wilayah-per-page">Baris:</label>
          <select id="wilayah-per-page" v-model="perPage" class="per-page-select">
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
      <div class="modal">
        <div class="modal-header">
          <h3>{{ isEdit ? 'Edit' : 'Tambah' }} {{ modalTitle }}</h3>
          <button @click="closeModal" class="btn-close">&times;</button>
        </div>
        <form @submit.prevent="submitForm" class="modal-form">
          <!-- Provinsi form -->
          <div v-if="modalType === 'provinsi'">
            <div class="form-group">
              <label>Nama Provinsi <span class="required">*</span></label>
              <input v-model="form.nama" type="text" required placeholder="Contoh: Jawa Tengah" />
            </div>
          </div>

          <!-- Kabupaten form -->
          <div v-if="modalType === 'kabupaten'">
            <div class="form-group">
              <label>Provinsi <span class="required">*</span></label>
              <select v-model="form.provinsi_id" required>
                <option value="">Pilih Provinsi</option>
                <option v-for="p in provinsiList" :key="p.id" :value="p.id">{{ p.nama }}</option>
              </select>
            </div>
            <div class="form-group">
              <label>Nama Kabupaten <span class="required">*</span></label>
              <input v-model="form.nama" type="text" required placeholder="Contoh: Pemalang" />
            </div>
          </div>

          <!-- Kecamatan form -->
          <div v-if="modalType === 'kecamatan'">
            <div class="form-group">
              <label>Kabupaten <span class="required">*</span></label>
              <select v-model="form.kabupaten_id" required>
                <option value="">Pilih Kabupaten</option>
                <option v-for="k in kabupatenList" :key="k.id" :value="k.id">
                  {{ k.nama }} ({{ k.provinsi?.nama }})
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Nama Kecamatan <span class="required">*</span></label>
              <input v-model="form.nama" type="text" required placeholder="Contoh: Comal" />
            </div>
          </div>

          <!-- Kalurahan form -->
          <div v-if="modalType === 'kalurahan'">
            <div class="form-group">
              <label>Kecamatan <span class="required">*</span></label>
              <select v-model="form.kecamatan_id" required>
                <option value="">Pilih Kecamatan</option>
                <option v-for="k in kecamatanList" :key="k.id" :value="k.id">
                  {{ k.nama }} ({{ k.kabupaten?.nama }})
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Nama Kalurahan/Desa <span class="required">*</span></label>
              <input v-model="form.nama" type="text" required placeholder="Contoh: Purwosari" />
            </div>
          </div>

          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn-cancel">Batal</button>
            <button type="submit" class="btn-primary">{{ isEdit ? 'Update' : 'Simpan' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Import Modal -->
    <div v-if="showImportModal" class="modal-overlay" @click.self="closeImportModal">
      <div class="modal" style="max-width: 520px;">
        <div class="modal-header">
          <h3>Import Data Wilayah</h3>
          <button @click="closeImportModal" class="btn-close">&times;</button>
        </div>
        <div class="modal-body" style="padding: 1.25rem 1.5rem;">
          <div class="import-section" style="margin-bottom: 1.25rem;">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.5rem; text-transform: uppercase;">1. Download Template</h4>
            <p style="font-size: 0.85rem; color: #4b5563; margin-bottom: 0.75rem;">Download template Excel dengan format standard untuk import:</p>
            <button @click="downloadTemplate" class="btn-secondary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;">
              📄 Download Template Data Wilayah
            </button>
          </div>

          <div class="import-section" style="margin-bottom: 1.25rem; border-top: 1px solid #f0f3f7; padding-top: 1.25rem;">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.5rem; text-transform: uppercase;">2. Upload File Excel</h4>
            <p style="font-size: 0.85rem; color: #4b5563; margin-bottom: 0.75rem;">Pilih file Excel (.xlsx, .xls, atau .csv):</p>
            <input 
              type="file" 
              @change="handleFileSelect" 
              accept=".xlsx,.xls,.csv"
              ref="fileInput"
              class="file-input"
              style="display: block; width: 100%; font-size: 0.875rem;"
            />
            <p v-if="selectedFile" class="file-name" style="margin-top: 8px; font-weight: 600; color: #0d9488; font-size: 0.85rem;">📎 {{ selectedFile.name }}</p>
          </div>

          <div class="import-info" style="background: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 8px; padding: 12px; font-size: 0.82rem; color: #9d174d;">
            <strong style="display: block; margin-bottom: 4px;">⚠️ Catatan Penting:</strong>
            <ul style="margin: 0; padding-left: 16px;">
              <li>Format: NO, PROVINSI, KABUPATEN, KECAMATAN, KELURAHAN/DESA</li>
              <li>Sistem akan otomatis membuat Provinsi, Kabupaten, Kecamatan jika belum ada</li>
              <li>Data duplikat akan diabaikan</li>
            </ul>
          </div>
        </div>
        <div class="modal-actions" style="padding: 0 1.5rem 1.5rem;">
          <button type="button" @click="closeImportModal" class="btn-cancel">Batal</button>
          <button @click="importData" class="btn-primary" :disabled="!selectedFile || importing">
            {{ importing ? 'Mengimport...' : 'Import Sekarang' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import api from '../services/api'

const tabs = [
  { id: 'provinsi', label: 'Provinsi' },
  { id: 'kabupaten', label: 'Kabupaten' },
  { id: 'kecamatan', label: 'Kecamatan' },
  { id: 'kalurahan', label: 'Kalurahan/Desa' }
]

const activeTab = ref('provinsi')
const loading = ref(false)
const showModal = ref(false)
const showImportModal = ref(false)
const isEdit = ref(false)
const modalType = ref('')
const form = ref({})
const importing = ref(false)
const selectedFile = ref(null)
const fileInput = ref(null)
const currentPage = ref(1)
const perPage = ref('10')

const provinsiList = ref([])
const kabupatenList = ref([])
const kecamatanList = ref([])
const kalurahanList = ref([])

const modalTitle = computed(() => {
  const titles = {
    provinsi: 'Provinsi',
    kabupaten: 'Kabupaten',
    kecamatan: 'Kecamatan',
    kalurahan: 'Kalurahan/Desa'
  }
  return titles[modalType.value] || ''
})

const activeData = computed(() => {
  if (activeTab.value === 'provinsi') return provinsiList.value
  if (activeTab.value === 'kabupaten') return kabupatenList.value
  if (activeTab.value === 'kecamatan') return kecamatanList.value
  return kalurahanList.value
})

const totalPages = computed(() => {
  if (perPage.value === 'all') return 1
  const size = Number(perPage.value) || 10
  return Math.max(1, Math.ceil(activeData.value.length / size))
})

const paginatedActiveData = computed(() => {
  if (perPage.value === 'all') return activeData.value
  const size = Number(perPage.value) || 10
  const start = (currentPage.value - 1) * size
  return activeData.value.slice(start, start + size)
})

const pageStart = computed(() => {
  if (!activeData.value.length) return 0
  if (perPage.value === 'all') return 1
  return (currentPage.value - 1) * (Number(perPage.value) || 10) + 1
})

const pageEnd = computed(() => {
  if (!activeData.value.length) return 0
  if (perPage.value === 'all') return activeData.value.length
  return Math.min(currentPage.value * (Number(perPage.value) || 10), activeData.value.length)
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

watch([activeData, perPage, activeTab], () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = totalPages.value
  }
  if (currentPage.value < 1) {
    currentPage.value = 1
  }
})

const changeTab = (tabId) => {
  activeTab.value = tabId
  currentPage.value = 1
  loadData(tabId)
}

const loadData = async (type) => {
  loading.value = true
  try {
    const response = await api.get(`/${type}`)
    if (response.data.success) {
      if (type === 'provinsi') provinsiList.value = response.data.data
      else if (type === 'kabupaten') kabupatenList.value = response.data.data
      else if (type === 'kecamatan') kecamatanList.value = response.data.data
      else if (type === 'kalurahan') kalurahanList.value = response.data.data
    }
  } catch (error) {
    console.error('Error loading data:', error)
    alert('Gagal memuat data')
  } finally {
    loading.value = false
  }
}

const loadAllData = async () => {
  await loadData('provinsi')
  await loadData('kabupaten')
  await loadData('kecamatan')
  await loadData('kalurahan')
}

const openModal = (type) => {
  modalType.value = type
  isEdit.value = false
  form.value = { nama: '', provinsi_id: '', kabupaten_id: '', kecamatan_id: '' }
  showModal.value = true
}

const editItem = (type, item) => {
  modalType.value = type
  isEdit.value = true
  form.value = {
    id: item.id,
    nama: item.nama,
    provinsi_id: item.provinsi_id || '',
    kabupaten_id: item.kabupaten_id || '',
    kecamatan_id: item.kecamatan_id || ''
  }
  showModal.value = true
}

const submitForm = async () => {
  try {
    const endpoint = `/${modalType.value}${isEdit.value ? `/${form.value.id}` : ''}`
    const method = isEdit.value ? 'put' : 'post'
    
    const data = { nama: form.value.nama }
    if (modalType.value === 'kabupaten') data.provinsi_id = form.value.provinsi_id
    if (modalType.value === 'kecamatan') data.kabupaten_id = form.value.kabupaten_id
    if (modalType.value === 'kalurahan') data.kecamatan_id = form.value.kecamatan_id

    await api[method](endpoint, data)
    alert(`${modalTitle.value} berhasil ${isEdit.value ? 'diupdate' : 'ditambahkan'}`)
    closeModal()
    loadAllData()
  } catch (error) {
    console.error('Error saving:', error)
    alert(error.response?.data?.message || 'Gagal menyimpan data')
  }
}

const deleteItem = async (type, id) => {
  if (!confirm(`Yakin ingin menghapus data ini? Semua data turunannya juga akan terhapus.`)) return
  
  try {
    await api.delete(`/${type}/${id}`)
    alert('Data berhasil dihapus')
    loadAllData()
  } catch (error) {
    console.error('Error deleting:', error)
    alert('Gagal menghapus data')
  }
}

const closeModal = () => {
  showModal.value = false
  isEdit.value = false
  form.value = {}
}

const exportAllData = async () => {
  try {
    const response = await api.get('/wilayah/export', {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `data_wilayah_${new Date().getTime()}.xlsx`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    
    alert('Data wilayah berhasil diexport!')
  } catch (error) {
    console.error('Error exporting:', error)
    alert('Gagal export data')
  }
}

const downloadTemplate = async () => {
  try {
    const response = await api.get('/wilayah/template', {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `template_data_wilayah.xlsx`)
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Error downloading template:', error)
    alert('Gagal download template')
  }
}

const handleFileSelect = (event) => {
  selectedFile.value = event.target.files[0]
}

const importData = async () => {
  if (!selectedFile.value) {
    alert('Pilih file terlebih dahulu')
    return
  }

  importing.value = true
  try {
    const formData = new FormData()
    formData.append('file', selectedFile.value)

    const response = await api.post('/wilayah/import', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    alert('Data wilayah berhasil diimport!')
    closeImportModal()
    loadAllData()
  } catch (error) {
    console.error('Error importing:', error)
    alert(error.response?.data?.message || 'Gagal import data')
  } finally {
    importing.value = false
  }
}

const closeImportModal = () => {
  showImportModal.value = false
  selectedFile.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

onMounted(() => {
  loadAllData()
})
</script>

<style scoped>
.wilayah-container {
  padding: 20px;
}

/* Hero Banner */
.hero-banner.teal {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
  border-radius: 16px;
  padding: 1.75rem 2rem;
  color: #fff;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 20px rgba(13, 148, 136, 0.15);
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
  border-bottom: 1px solid #f0f3f7;
  padding-bottom: 0.5rem;
}
.toolbar-left {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex: 1;
}

/* Tabs */
.tabs {
  display: flex;
  gap: 0.5rem;
}
.tab {
  padding: 8px 16px;
  border: none;
  background: none;
  font-size: 0.875rem;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  border-bottom: 2px solid transparent;
  transition: all 0.15s;
}
.tab:hover {
  color: #0f172a;
}
.tab.active {
  color: #0d9488;
  border-bottom-color: #0d9488;
}

/* Buttons */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #0d9488, #0f766e);
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
  display: inline-flex;
  align-items: center;
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
.font-semibold { font-weight: 600; }
.font-medium { font-weight: 500; }

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
  border-top-color: #0d9488;
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
.form-group select {
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
.form-group select:focus {
  border-color: #0d9488;
  box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
}
.required { color: #ef4444; }

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

/* Specific to Import */
.import-section h4 {
  font-size: 0.88rem;
  font-weight: 700;
  color: #1e3a8a;
  margin-bottom: 0.5rem;
  text-transform: uppercase;
}

@media (max-width: 640px) {
  .hero-banner.teal { padding: 1.25rem; }
  .toolbar { flex-direction: column; align-items: stretch; }
  .toolbar-left { overflow-x: auto; }
}
</style>
