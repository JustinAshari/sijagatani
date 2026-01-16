<template>
  <div class="petani-list">
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
        <button v-if="authStore.isSuperAdmin" @click="showAddModal = true" class="btn-primary">
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

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Kabupaten</th>
            <th>Kecamatan</th>
            <th>Luas Lahan (HA)</th>
            <th>Potensi Panen (KG)</th>
            <th>Komoditi</th>
            <th>No. Telp</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(petani, index) in filteredPetani" :key="petani.id">
            <td>{{ index + 1 }}</td>
            <td>{{ formatDate(petani.tanggal) }}</td>
            <td>{{ petani.nik }}</td>
            <td>{{ petani.nama }}</td>
            <td>{{ petani.kabupaten }}</td>
            <td>{{ petani.kecamatan }}</td>
            <td>{{ petani.luas_lahan }}</td>
            <td>{{ petani.potensi_panen }}</td>
            <td>
              <span class="badge" :class="`badge-${petani.komoditi.toLowerCase()}`">
                {{ petani.komoditi }}
              </span>
            </td>
            <td>{{ petani.no_telepon || '-' }}</td>
            <td>
              <div class="action-buttons">
                <button @click="viewDetail(petani)" class="btn-info" title="Detail">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
                <button v-if="authStore.isSuperAdmin" @click="editPetani(petani)" class="btn-warning" title="Edit">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </button>
                <button v-if="authStore.isSuperAdmin" @click="deletePetani(petani.id)" class="btn-danger" title="Hapus">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="filteredPetani.length === 0">
            <td colspan="11" class="text-center">Tidak ada data petani</td>
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
                <label>Tanggal *</label>
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
                <label>Kabupaten *</label>
                <select v-model="form.kabupaten" required>
                  <option value="">Pilih Kabupaten</option>
                  <option v-for="kab in kabupatenList" :key="kab" :value="kab">
                    {{ kab }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Kecamatan *</label>
                <input v-model="form.kecamatan" type="text" required />
              </div>
              <div class="form-group">
                <label>Desa</label>
                <input v-model="form.desa" type="text" />
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
                <strong>Tanggal:</strong>
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
                <strong>Kabupaten:</strong>
                <span>{{ selectedPetani.kabupaten }}</span>
              </div>
              <div class="info-item">
                <strong>Kecamatan:</strong>
                <span>{{ selectedPetani.kecamatan }}</span>
              </div>
              <div class="info-item">
                <strong>Desa:</strong>
                <span>{{ selectedPetani.desa || '-' }}</span>
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
                <span>{{ selectedPetani.luas_lahan }} HA</span>
              </div>
              <div class="info-item">
                <strong>Potensi Panen:</strong>
                <span>{{ selectedPetani.potensi_panen }} KG</span>
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
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import kabupatenJawaTengah from '@/data/kabupaten'
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
const kabupatenList = ref(kabupatenJawaTengah)
const nikCheckTimeout = ref(null)
const nikStatus = ref({ message: '', class: '', isDuplicate: false })

const form = ref({
  tanggal: new Date().toISOString().split('T')[0],
  nik: '',
  nama: '',
  alamat: '',
  kabupaten: '',
  kecamatan: '',
  desa: '',
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
    kabupaten: petani.kabupaten,
    kecamatan: petani.kecamatan,
    desa: petani.desa,
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
  
  // Set previews for existing images
  if (petani.foto_ktp) previews.value.foto_ktp = getImageUrl(petani.foto_ktp)
  if (petani.foto_petani) previews.value.foto_petani = getImageUrl(petani.foto_petani)
  if (petani.foto_komoditi) previews.value.foto_komoditi = getImageUrl(petani.foto_komoditi)
  if (petani.kwitansi_pembayaran) previews.value.kwitansi_pembayaran = getImageUrl(petani.kwitansi_pembayaran)
  
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
  form.value = {
    tanggal: new Date().toISOString().split('T')[0],
    nik: '',
    nama: '',
    alamat: '',
    kabupaten: '',
    kecamatan: '',
    desa: '',
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
  }
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

const getImageUrl = (path) => {
  return `http://localhost:8000/storage/${path}`
}

const openImage = (url) => {
  window.open(url, '_blank')
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
}

onMounted(() => {
  fetchPetani()
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
  background: #17a2b8;
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
  background: #28a745;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
}

.btn-primary {
  background: #007bff;
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
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

.table-container {
  overflow-x: auto;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

th {
  background: #f8f9fa;
  font-weight: 600;
  color: #495057;
  position: sticky;
  top: 0;
}

.action-buttons {
  display: flex;
  gap: 5px;
}

.action-buttons button {
  padding: 5px 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

.btn-info {
  background: #17a2b8;
  color: white;
}

.btn-warning {
  background: #ffc107;
  color: #212529;
}

.btn-danger {
  background: #dc3545;
  color: white;
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

.text-center {
  text-align: center;
  padding: 40px;
  color: #6c757d;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .table-container {
    overflow-x: auto;
  }
  
  table {
    min-width: 1200px;
  }
}

@media (max-width: 768px) {
  .petani-list {
    padding: 10px;
  }

  .header {
    flex-direction: column;
    gap: 10px;
    align-items: stretch;
  }

  .header-actions {
    flex-direction: column;
    width: 100%;
  }

  .header-actions button {
    width: 100%;
  }

  .filter-row {
    flex-direction: column;
  }

  .search-input,
  .filter-select {
    width: 100%;
    min-width: 100%;
  }

  .date-filter {
    width: 100%;
  }

  .date-input {
    flex: 1;
  }

  .btn-filter,
  .btn-reset {
    width: 100%;
  }

  .stats-bar {
    flex-direction: column;
    gap: 10px;
  }

  .modal-content {
    width: 95%;
    padding: 20px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }

  .photo-grid {
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

.action-buttons svg {
  width: 16px;
  height: 16px;
}

.btn-success svg,
.btn-filter svg,
.btn-reset svg {
  width: 16px;
  height: 16px;
  margin-right: 4px;
}
</style>
