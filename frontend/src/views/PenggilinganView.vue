<template>
  <div class="penggilingan-container">
    <div class="header">
      <h1>Data Makloon GKP</h1>
      <button v-if="authStore.canManagePenggilingan" @click="showModal = true" class="btn-add">
        <span>+</span> Tambah Makloon
      </button>
    </div>

    <!-- Stats Bar -->
    <div class="stats-bar">
      <div class="stat-item">
        <span class="stat-label">Total Makloon:</span>
        <span class="stat-value">{{ filteredData.length }}</span>
      </div>
      <div class="stat-item">
        <span class="stat-label">Total Tonase (dalam KG):</span>
        <span class="stat-value">{{ totalTonase}} KG</span>
      </div>
      <div class="stat-item">
        <span class="stat-label">Total Angkutan:</span>
        <span class="stat-value">{{ totalAngkutan }}</span>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
      <div class="filter-row">
        <div class="filter-item">
          <label>Tanggal Dari:</label>
          <input type="date" v-model="filters.tanggalDari" @change="applyFilters" />
        </div>
        <div class="filter-item">
          <label>Tanggal Sampai:</label>
          <input type="date" v-model="filters.tanggalSampai" @change="applyFilters" />
        </div>
        <div class="filter-item">
          <label>Nama Makloon/MPP:</label>
          <input
            type="text"
            v-model="filters.namaPenggilingan"
            @input="applyFilters"
            placeholder="Cari nama makloon/MPP..."
          />
        </div>
        <div class="filter-item">
          <button @click="resetFilters" class="btn-reset">Reset Filter</button>
        </div>
      </div>
    </div>

    <!-- Table -->
    <!-- Column Picker -->
    <div class="col-picker-bar">
      <div class="col-picker-wrapper">
        <div v-if="showColPicker" class="col-picker-overlay" @click="showColPicker = false"></div>
        <button @click="showColPicker = !showColPicker" class="btn-col-picker">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
            <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
          </svg>
          Tampilkan Kolom
        </button>
        <div v-if="showColPicker" class="col-picker-dropdown">
          <div class="col-picker-header">Pilih Kolom</div>
          <label v-for="col in colDefs" :key="col.key" class="col-picker-item">
            <input type="checkbox" v-model="visibleCols[col.key]" />
            <span>{{ col.label }}</span>
          </label>
        </div>
      </div>
    </div>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th v-if="visibleCols.tanggal_pengajuan">tanggal_pengajuan</th>
            <th v-if="visibleCols.nama_petani">nama_petani</th>
            <th v-if="visibleCols.nama_penggilingan">nama_penggilingan</th>
            <th v-if="visibleCols.lokasi_makloon">lokasi_makloon</th>
            <th v-if="visibleCols.total_tonase">total_tonase</th>
            <th v-if="visibleCols.jumlah_angkutan">jumlah_angkutan</th>
            <th v-if="visibleCols.status_verifikasi">status_verifikasi</th>
            <th v-if="visibleCols.catatan_verifikasi">catatan_verifikasi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td :colspan="colSpan" class="loading-cell">Loading...</td>
          </tr>
          <tr v-else-if="filteredData.length === 0">
            <td :colspan="colSpan" class="empty-cell">Tidak ada data</td>
          </tr>
          <tr v-else v-for="(item, index) in filteredData" :key="item.id">
            <td>{{ index + 1 }}</td>
            <td v-if="visibleCols.tanggal_pengajuan">{{ formatDate(item.tanggal_pengajuan) }}</td>
            <td v-if="visibleCols.nama_petani">{{ item.nama_petani || '-' }}</td>
            <td v-if="visibleCols.nama_penggilingan">{{ item.nama_penggilingan }}</td>
            <td v-if="visibleCols.lokasi_makloon">{{ item.lokasi_makloon }}</td>
            <td v-if="visibleCols.total_tonase" class="text-right">{{ parseFloat(String(item.total_tonase)) }} KG</td>
            <td v-if="visibleCols.jumlah_angkutan" class="text-center">{{ item.jumlah_angkutan }}</td>
            <td v-if="visibleCols.status_verifikasi" class="text-center">
              <span :class="['badge-status', 'badge-' + item.status_verifikasi]">
                {{ item.status_verifikasi === 'disetujui' ? 'Disetujui' : item.status_verifikasi === 'ditolak' ? 'Ditolak' : 'Pending' }}
              </span>
            </td>
            <td v-if="visibleCols.catatan_verifikasi">{{ item.catatan_verifikasi || '-' }}</td>
            <td class="action-buttons">
              <button @click="viewDetail(item)" class="btn-view" title="Lihat Detail">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
              <button @click="downloadMakloonGKP(item.id)" class="btn-download" title="Download Form GKP Maklon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                  <polyline points="7 10 12 15 17 10"/>
                  <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
              </button>
              <button v-if="authStore.canManagePenggilingan" @click="editItem(item)" class="btn-edit" title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
              </button>
              <button v-if="authStore.canManagePenggilingan" @click="deleteItem(item.id)" class="btn-delete" title="Hapus">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="3 6 5 6 21 6"/>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
              </button>
              <button v-if="authStore.canVerify" @click="openVerifikasiModal(item)" class="btn-verify" :title="'Verifikasi'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                  <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ isEditing ? 'Edit' : 'Tambah' }} Makloon</h2>
          <button @click="closeModal" class="btn-close">×</button>
        </div>
        <form @submit.prevent="submitForm" class="modal-body">
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
                  type="text"
                  v-model="form.nama_penggilingan"
                  required
                  placeholder="Contoh: UD Sumber Rezeki"
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
            <small class="help-text">* Format: JPG, PNG. Max: 5MB (akan dikompres otomatis)</small>
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
                    <img v-if="transport.nota_timbang_preview" :src="transport.nota_timbang_preview" class="preview-image-small" alt="Preview Nota Timbang" />
                    <input
                      type="file"
                      @change="handleTransportFileUpload($event, index, 'nota_timbang')"
                      accept="image/*"
                    />
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label>Upload Surat Jalan</label>
                    <img v-if="transport.surat_jalan_preview" :src="transport.surat_jalan_preview" class="preview-image-small" alt="Preview Surat Jalan" />
                    <input
                      type="file"
                      @change="handleTransportFileUpload($event, index, 'surat_jalan')"
                      accept="image/*"
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
                <img :src="getImageUrl(transport.nota_timbang)" :alt="`Nota Timbang ${transport.nama_pengemudi}`" @click="openImage(getImageUrl(transport.nota_timbang))" />
              </div>
              <div
                class="photo-item"
                v-for="transport in selectedItem.transports.filter(t => t.surat_jalan)"
                :key="`surat-${transport.id}`"
              >
                <label>Surat Jalan - Angkutan #{{ transport.urutan }} ({{ transport.nama_pengemudi }})</label>
                <img :src="getImageUrl(transport.surat_jalan)" :alt="`Surat Jalan ${transport.nama_pengemudi}`" @click="openImage(getImageUrl(transport.surat_jalan))" />
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
                  {{ parseFloat(String(selectedItem.total_tonase)) }} KG
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
import { ref, onMounted, computed } from 'vue'
import api, { getStorageUrl } from '../services/api'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()

// Type definitions removed (JavaScript mode)

const data = ref([])
const filteredData = ref([])
const kabupatenList = ref([])
const loading = ref(false)

const showColPicker = ref(false)
const colDefs = [
  { key: 'tanggal_pengajuan', label: 'tanggal_pengajuan' },
  { key: 'nama_petani', label: 'nama_petani' },
  { key: 'nama_penggilingan', label: 'nama_penggilingan' },
  { key: 'lokasi_makloon', label: 'lokasi_makloon' },
  { key: 'total_tonase', label: 'total_tonase' },
  { key: 'jumlah_angkutan', label: 'jumlah_angkutan' },
  { key: 'status_verifikasi', label: 'status_verifikasi' },
  { key: 'catatan_verifikasi', label: 'catatan_verifikasi' },
]
const visibleCols = ref({
  tanggal_pengajuan: true, nama_petani: false, nama_penggilingan: true,
  lokasi_makloon: true, total_tonase: true, jumlah_angkutan: true,
  status_verifikasi: true, catatan_verifikasi: false
})
const colSpan = computed(() => 2 + Object.values(visibleCols.value).filter(Boolean).length)

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
      old_nota_timbang: '',
      surat_jalan: null,
      surat_jalan_preview: '',
      old_surat_jalan: '',
    },
  ],
})

const totalTonase = computed(() => {
  return filteredData.value.reduce((sum, item) => sum + parseFloat(String(item.total_tonase)), 0)
})

const totalAngkutan = computed(() => {
  return filteredData.value.reduce((sum, item) => sum + item.jumlah_angkutan, 0)
})

const getImageUrl = (path) => getStorageUrl(path)

const openImage = (url) => {
  window.open(url, '_blank')
}

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

  filteredData.value = filtered
}

const resetFilters = () => {
  filters.value = {
    tanggalDari: '',
    tanggalSampai: '',
    namaPenggilingan: '',
  }
  filteredData.value = [...data.value]
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
    } else if (fileType === 'surat_jalan') {
      form.value.transports[index].surat_jalan = file
      form.value.transports[index].surat_jalan_preview = URL.createObjectURL(file)
    }
  }
}

const removeTransportImage = (index) => {
  if (form.value.transports[index]) {
    form.value.transports[index].nota_timbang = null
    form.value.transports[index].nota_timbang_preview = ''
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
      old_nota_timbang: '',
      surat_jalan: null,
      surat_jalan_preview: '',
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
  return total
}

const submitForm = async () => {
  try {
    // Validasi client-side dulu
    if (!form.value.tanggal_pengajuan) {
      alert('Tanggal pengajuan harus diisi')
      return
    }
    if (!form.value.nama_penggilingan) {
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
      old_nota_timbang: t.nota_timbang || '',
      surat_jalan: null,
      surat_jalan_preview: t.surat_jalan
        ? `http://localhost:8000/storage/${t.surat_jalan}`
        : '',
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

const closeModal = () => {
  showModal.value = false
  isEditing.value = false
  form.value = {
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
        old_nota_timbang: '',
        surat_jalan: null,
        surat_jalan_preview: '',
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
  max-width: 1400px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.header h1 {
  font-size: 28px;
  color: #2c3e50;
}

.btn-add {
  background: #27ae60;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-add:hover {
  background: #229954;
}

.stats-bar {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.stat-item {
  background: #ecf0f1;
  padding: 15px 25px;
  border-radius: 8px;
  flex: 1;
  min-width: 200px;
}

.stat-label {
  display: block;
  color: #7f8c8d;
  font-size: 14px;
  margin-bottom: 5px;
}

.stat-value {
  display: block;
  color: #2c3e50;
  font-size: 24px;
  font-weight: bold;
}

.filter-section {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.filter-row {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  align-items: flex-end;
}

.filter-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
  flex: 1;
  min-width: 200px;
}

.filter-item label {
  font-size: 14px;
  color: #555;
  font-weight: 500;
}

.filter-item input,
.filter-item select {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.btn-reset,
.btn-export {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.btn-reset {
  background: #95a5a6;
  color: white;
}

.btn-reset:hover {
  background: #7f8c8d;
}

.btn-export {
  background: #27ae60;
  color: white;
}

.btn-export:hover {
  background: #229954;
}

.table-wrapper {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #34495e;
  color: white;
}

th,
td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  font-weight: 600;
  font-size: 14px;
}

td {
  font-size: 14px;
  color: #2c3e50;
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

.btn-view,
.btn-download,
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

.btn-download {
  background: #27ae60;
  color: white;
}

.btn-download:hover {
  background: #229954;
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

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  overflow-y: auto;
  padding: 20px;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 900px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-detail {
  max-width: 1000px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #ddd;
}

.modal-header h2 {
  margin: 0;
  font-size: 24px;
  color: #2c3e50;
}

.btn-close {
  background: none;
  border: none;
  font-size: 32px;
  cursor: pointer;
  color: #7f8c8d;
  line-height: 1;
}

.btn-close:hover {
  color: #2c3e50;
}

.modal-body {
  padding: 20px;
}

.detail-body {
  max-height: 70vh;
  overflow-y: auto;
}

.form-section {
  margin-bottom: 25px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
}

.detail-section {
  margin-bottom: 25px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
}

.form-section h3,
.detail-section h3 {
  margin: 0 0 15px 0;
  font-size: 18px;
  color: #2c3e50;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.section-header h3 {
  margin: 0;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 15px;
  margin-bottom: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.form-group label {
  font-size: 14px;
  color: #555;
  font-weight: 500;
}

.required {
  color: #e74c3c;
}

.form-group input,
.form-group select {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #3498db;
}

.image-preview {
  margin-top: 10px;
  position: relative;
  width: 200px;
  height: 200px;
  border: 2px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.preview-image {
  margin-top: 10px;
  max-width: 200px;
  max-height: 200px;
  border-radius: 8px;
  border: 2px solid #ddd;
  cursor: pointer;
  display: block;
}

.preview-image-small {
  margin-top: 10px;
  max-width: 150px;
  max-height: 150px;
  border-radius: 5px;
  border: 1px solid #ddd;
  cursor: pointer;
  display: block;
}

.btn-remove-image {
  position: absolute;
  top: 5px;
  right: 5px;
  background: #e74c3c;
  color: white;
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 20px;
  line-height: 1;
}

.btn-remove-image:hover {
  background: #c0392b;
}

.image-preview-small {
  margin-top: 10px;
  position: relative;
  width: 100px;
  height: 100px;
  border: 2px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
}

.image-preview-small img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.btn-remove-image-small {
  position: absolute;
  top: 2px;
  right: 2px;
  background: #e74c3c;
  color: white;
  border: none;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 16px;
  line-height: 1;
}

.btn-remove-image-small:hover {
  background: #c0392b;
}

.btn-add-transport {
  background: #3498db;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-add-transport:hover:not(:disabled) {
  background: #2980b9;
}

.btn-add-transport:disabled {
  background: #95a5a6;
  cursor: not-allowed;
}

.transport-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.transport-item {
  border: 1px solid #ddd;
  padding: 15px;
  border-radius: 8px;
  background: white;
}

.transport-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 1px solid #ddd;
}

.transport-header h4 {
  margin: 0;
  font-size: 16px;
  color: #2c3e50;
}

.btn-remove-transport {
  background: #e74c3c;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
}

.btn-remove-transport:hover:not(:disabled) {
  background: #c0392b;
}

.btn-remove-transport:disabled {
  background: #95a5a6;
  cursor: not-allowed;
}

.resume-section {
  background: #e8f5e9;
}

.transport-photos {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.transport-photos h4 {
  font-size: 16px;
  color: #333;
  margin-bottom: 15px;
}

.no-data {
  text-align: center;
  color: #999;
  padding: 20px;
  font-style: italic;
}

.resume-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.resume-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.resume-label {
  font-size: 14px;
  color: #555;
  font-weight: 500;
}

.resume-value {
  font-size: 20px;
  color: #27ae60;
  font-weight: bold;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 15px;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.detail-label {
  font-size: 12px;
  color: #7f8c8d;
  font-weight: 500;
}

.detail-value {
  font-size: 14px;
  color: #2c3e50;
}

.photo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.photo-item {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.photo-label {
  font-size: 14px;
  color: #555;
  font-weight: 500;
  margin: 0;
}

.photo-item img {
  width: 100%;
  height: 300px;
  object-fit: cover;
  border: 2px solid #ddd;
  border-radius: 8px;
}

.transport-table-wrapper {
  overflow-x: auto;
}

.transport-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
}

.transport-table thead {
  background: #34495e;
  color: white;
}

.transport-table th,
.transport-table td {
  padding: 10px;
  text-align: left;
  border-bottom: 1px solid #ddd;
  font-size: 13px;
}

.link-view {
  color: #3498db;
  text-decoration: none;
}

.link-view:hover {
  text-decoration: underline;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 20px;
  border-top: 1px solid #ddd;
}

.btn-cancel,
.btn-submit {
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-cancel {
  background: #95a5a6;
  color: white;
}

.btn-cancel:hover {
  background: #7f8c8d;
}

.btn-submit {
  background: #27ae60;
  color: white;
}

.btn-submit:hover {
  background: #229954;
}

/* Responsive */
@media (max-width: 768px) {
  .header {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }

  .stats-bar {
    flex-direction: column;
  }

  .filter-row {
    flex-direction: column;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .detail-grid {
    grid-template-columns: 1fr;
  }

  .photo-grid {
    grid-template-columns: 1fr;
  }

  .resume-grid {
    grid-template-columns: 1fr;
  }
}

.icon-inline {
  width: 18px;
  height: 18px;
  display: inline-block;
  vertical-align: middle;
  margin-right: 6px;
}

.help-text {
  display: block;
  margin-top: 10px;
  color: #6c757d;
  font-size: 12px;
}

.btn-view svg,
.btn-download svg,
.btn-edit svg,
.btn-delete svg {
  width: 16px;
  height: 16px;
}

.btn-export svg {
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
  justify-content: flex-end;
  margin-bottom: 8px;
}
.col-picker-wrapper { position: relative; }
.btn-col-picker {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background: #fff;
  border: 1px solid #ced4da;
  border-radius: 6px;
  cursor: pointer;
  font-size: 13px;
  color: #495057;
}
.btn-col-picker:hover { background: #f8f9fa; }
.col-picker-overlay {
  position: fixed;
  inset: 0;
  z-index: 99;
}
.col-picker-dropdown {
  position: absolute;
  right: 0;
  top: calc(100% + 4px);
  background: #fff;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.12);
  z-index: 100;
  min-width: 180px;
  padding: 8px 0;
  max-height: 320px;
  overflow-y: auto;
}
.col-picker-header {
  padding: 6px 14px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: #6c757d;
  border-bottom: 1px solid #dee2e6;
  margin-bottom: 4px;
}
.col-picker-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 5px 14px;
  cursor: pointer;
  font-size: 13px;
  color: #212529;
}
.col-picker-item:hover { background: #f8f9fa; }
.col-picker-item input[type="checkbox"] { width: 14px; height: 14px; cursor: pointer; }
</style>
