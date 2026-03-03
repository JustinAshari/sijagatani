<template>
  <div class="surat-pernyataan-view">
    <div class="page-card">

    <!-- Hero Banner -->
    <div class="hero-banner blue">
      <div class="hero-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
          <polyline points="10 9 9 9 8 9"/>
        </svg>
      </div>
      <div class="hero-text">
        <h2>Surat Pernyataan</h2>
        <p>Kelola dokumen surat pernyataan yang telah ditandatangani petugas</p>
      </div>
    </div>

    <div class="page-header">
      <div class="header-left">
        <h1>
          <svg class="icon-title" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
          </svg>
          Surat Pernyataan Petani
        </h1>
        <p class="subtitle">Kelola surat pernyataan yang telah ditandatangani petugas</p>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-container">
      <div class="stat-card green">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statsData.sudahUpload }}</div>
          <div class="stat-label">Sudah Upload</div>
        </div>
      </div>
      <div class="stat-card orange">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statsData.belumUpload }}</div>
          <div class="stat-label">Belum Upload</div>
        </div>
      </div>
      <div class="stat-card blue">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value">{{ statsData.total }}</div>
          <div class="stat-label">Total Petani</div>
        </div>
      </div>
    </div>

    <!-- Search & Filter -->
    <div class="toolbar">
      <div class="search-box">
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/>
          <path d="m21 21-4.35-4.35"/>
        </svg>
        <input v-model="searchQuery" type="text" placeholder="Cari nama atau NIK petani..." />
      </div>
      
      <div class="filter-group">
        <label>Status:</label>
        <select v-model="filterStatus">
          <option value="">Semua</option>
          <option value="sudah">Sudah Upload</option>
          <option value="belum">Belum Upload</option>
        </select>
      </div>
    </div>

    <!-- Table -->
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
    <div class="table-container">
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
            <th v-if="visibleCols.surat_pernyataan">Surat Pernyataan</th>
            <th v-if="visibleCols.status_verifikasi">Status Verifikasi</th>
            <th v-if="visibleCols.catatan_verifikasi">Catatan Verifikasi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td :colspan="colSpan" class="text-center">
              <div class="loading">Memuat data...</div>
            </td>
          </tr>
          <tr v-else-if="filteredPetani.length === 0">
            <td :colspan="colSpan" class="text-center">Tidak ada data</td>
          </tr>
          <tr v-else v-for="(petani, index) in filteredPetani" :key="petani.id">
            <td>{{ index + 1 }}</td>
            <td v-if="visibleCols.tanggal">{{ formatDate(petani.tanggal) }}</td>
            <td v-if="visibleCols.nik">{{ petani.nik }}</td>
            <td v-if="visibleCols.nama">{{ petani.nama }}</td>
            <td v-if="visibleCols.luas_lahan">{{ formatNumber(petani.luas_lahan) }} HA</td>
            <td v-if="visibleCols.alamat_lahan">{{ petani.alamat_lahan || '-' }}</td>
            <td v-if="visibleCols.potensi_panen">{{ formatNumber(petani.potensi_panen) }} KG</td>
            <td v-if="visibleCols.komoditi">{{ petani.komoditi || '-' }}</td>
            <td v-if="visibleCols.alamat">{{ petani.alamat || '-' }}</td>
            <td v-if="visibleCols.provinsi_id">{{ petani.provinsi?.nama || '-' }}</td>
            <td v-if="visibleCols.kabupaten_id">{{ petani.kabupaten?.nama || '-' }}</td>
            <td v-if="visibleCols.kecamatan_id">{{ petani.kecamatan?.nama || '-' }}</td>
            <td v-if="visibleCols.kalurahan_id">{{ petani.kalurahan?.nama || '-' }}</td>
            <td v-if="visibleCols.no_telepon">{{ petani.no_telepon || '-' }}</td>
            <td v-if="visibleCols.bank">{{ petani.bank || '-' }}</td>
            <td v-if="visibleCols.no_rekening">{{ petani.no_rekening || '-' }}</td>
            <td v-if="visibleCols.surat_pernyataan">
              <span v-if="petani.surat_pernyataan" class="badge badge-success">
                <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                Sudah Upload
              </span>
              <span v-else class="badge badge-warning">
                <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"/>
                  <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Belum Upload
              </span>
            </td>
            <td v-if="visibleCols.status_verifikasi" class="text-center">{{ petani.status_verifikasi || 'pending' }}</td>
            <td v-if="visibleCols.catatan_verifikasi">{{ petani.catatan_verifikasi || '-' }}</td>
            <td>
              <div class="action-buttons">
                <button v-if="petani.surat_pernyataan" @click="viewSurat(petani)" class="btn-view" title="Lihat Surat">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
                <button @click="uploadSurat(petani)" class="btn-upload" :title="petani.surat_pernyataan ? 'Ganti Surat' : 'Upload Surat'">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Upload -->
    <div v-if="showUploadModal" class="modal" @click.self="closeModal">
      <div class="modal-content modal-small">
        <div class="modal-header">
          <h3>Upload Surat Pernyataan</h3>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>
        <form @submit.prevent="submitUpload">
          <div class="modal-body">
            <div class="petani-info">
              <p><strong>NIK:</strong> {{ selectedPetani?.nik }}</p>
              <p><strong>Nama:</strong> {{ selectedPetani?.nama }}</p>
            </div>
            
            <div class="form-group">
              <label>Surat Pernyataan (TTD Petugas) *</label>
              <input type="file" @change="handleFileUpload" accept="image/*" required />
              <small class="help-text">Format: JPG, PNG. Max: 5MB</small>
            </div>

            <div v-if="preview" class="preview-container">
              <img :src="preview" alt="Preview" class="preview-image" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
            <button type="submit" class="btn-primary" :disabled="uploading">
              {{ uploading ? 'Mengupload...' : 'Upload' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal View Image -->
    <div v-if="showImageModal" class="modal modal-image" @click.self="showImageModal = false">
      <div class="modal-content modal-large">
        <div class="modal-header">
          <h3>Surat Pernyataan - {{ viewingPetani?.nama }}</h3>
          <button @click="showImageModal = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <img :src="getImageUrl(viewingPetani?.surat_pernyataan)" alt="Surat Pernyataan" class="full-image" />
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
const filterStatus = ref('')
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
  { key: 'surat_pernyataan', label: 'Surat Pernyataan' },
  { key: 'status_verifikasi', label: 'Status Verifikasi' },
  { key: 'catatan_verifikasi', label: 'Catatan Verifikasi' },
]
const visibleCols = ref({
  tanggal: true, nik: true, nama: true, luas_lahan: false, alamat_lahan: false,
  potensi_panen: false, komoditi: false, alamat: false, provinsi_id: false,
  kabupaten_id: false, kecamatan_id: true, kalurahan_id: true,
  no_telepon: true, bank: false, no_rekening: false,
  surat_pernyataan: true, status_verifikasi: false, catatan_verifikasi: false
})
const colSpan = computed(() => 2 + Object.values(visibleCols.value).filter(Boolean).length)

const showUploadModal = ref(false)
const showImageModal = ref(false)
const selectedPetani = ref(null)
const viewingPetani = ref(null)
const uploadFile = ref(null)
const preview = ref(null)
const uploading = ref(false)

const statsData = computed(() => {
  const total = petaniList.value.length
  const sudahUpload = petaniList.value.filter(p => p.surat_pernyataan).length
  const belumUpload = total - sudahUpload
  
  return { total, sudahUpload, belumUpload }
})

const filteredPetani = computed(() => {
  let result = petaniList.value
  
  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(p => 
      p.nama.toLowerCase().includes(query) || 
      p.nik.includes(query)
    )
  }
  
  // Filter by status
  if (filterStatus.value === 'sudah') {
    result = result.filter(p => p.surat_pernyataan)
  } else if (filterStatus.value === 'belum') {
    result = result.filter(p => !p.surat_pernyataan)
  }
  
  return result
})

const fetchPetani = async () => {
  try {
    loading.value = true
    const response = await api.get('/petani')
    petaniList.value = response.data.data
  } catch (error) {
    console.error('Error fetching petani:', error)
    alert('Gagal mengambil data petani')
  } finally {
    loading.value = false
  }
}

const uploadSurat = (petani) => {
  selectedPetani.value = petani
  showUploadModal.value = true
}

const viewSurat = (petani) => {
  viewingPetani.value = petani
  showImageModal.value = true
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    uploadFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      preview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const submitUpload = async () => {
  if (!uploadFile.value) return
  
  try {
    uploading.value = true
    const formData = new FormData()
    
    // Only send surat_pernyataan, other fields remain unchanged
    formData.append('surat_pernyataan', uploadFile.value)
    
    // Send all required fields to satisfy validation
    formData.append('tanggal', selectedPetani.value.tanggal)
    formData.append('nik', selectedPetani.value.nik)
    formData.append('nama', selectedPetani.value.nama)
    formData.append('alamat', selectedPetani.value.alamat)
    formData.append('luas_lahan', selectedPetani.value.luas_lahan)
    formData.append('alamat_lahan', selectedPetani.value.alamat_lahan)
    formData.append('potensi_panen', selectedPetani.value.potensi_panen)
    formData.append('komoditi', selectedPetani.value.komoditi)
    
    if (selectedPetani.value.provinsi_id) formData.append('provinsi_id', selectedPetani.value.provinsi_id)
    if (selectedPetani.value.kabupaten_id) formData.append('kabupaten_id', selectedPetani.value.kabupaten_id)
    if (selectedPetani.value.kecamatan_id) formData.append('kecamatan_id', selectedPetani.value.kecamatan_id)
    if (selectedPetani.value.kalurahan_id) formData.append('kalurahan_id', selectedPetani.value.kalurahan_id)
    if (selectedPetani.value.no_telepon) formData.append('no_telepon', selectedPetani.value.no_telepon)
    if (selectedPetani.value.bank) formData.append('bank', selectedPetani.value.bank)
    if (selectedPetani.value.no_rekening) formData.append('no_rekening', selectedPetani.value.no_rekening)
    
    await api.post(`/petani/${selectedPetani.value.id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    alert('Surat pernyataan berhasil diupload')
    closeModal()
    fetchPetani()
  } catch (error) {
    console.error('Error uploading:', error)
    alert('Gagal mengupload surat pernyataan')
  } finally {
    uploading.value = false
  }
}

const closeModal = () => {
  showUploadModal.value = false
  selectedPetani.value = null
  uploadFile.value = null
  preview.value = null
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const formatNumber = (value) => {
  if (!value) return '0'
  const num = parseFloat(value)
  return num % 1 === 0 ? num.toString() : num.toFixed(2)
}

const getImageUrl = (path) => getStorageUrl(path)

onMounted(() => {
  fetchPetani()
})
</script>

<style scoped>
.surat-pernyataan-view {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2rem;
}

.header-left h1 {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.75rem;
  color: #2c3e50;
  margin: 0 0 0.5rem 0;
}

.icon-title {
  width: 32px;
  height: 32px;
  color: #3498db;
}

.subtitle {
  color: #7f8c8d;
  margin: 0;
}

/* Stats Cards */
.stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.stat-card.green {
  border-left: 4px solid #27ae60;
}

.stat-card.orange {
  border-left: 4px solid #f39c12;
}

.stat-card.blue {
  border-left: 4px solid #3498db;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-card.green .stat-icon {
  background: rgba(39, 174, 96, 0.1);
  color: #27ae60;
}

.stat-card.orange .stat-icon {
  background: rgba(243, 156, 18, 0.1);
  color: #f39c12;
}

.stat-card.blue .stat-icon {
  background: rgba(52, 152, 219, 0.1);
  color: #3498db;
}

.stat-icon svg {
  width: 24px;
  height: 24px;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #2c3e50;
}

.stat-label {
  color: #7f8c8d;
  font-size: 0.9rem;
}

/* Toolbar */
.toolbar {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  margin-bottom: 2rem;
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.search-box {
  flex: 1;
  min-width: 300px;
  position: relative;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  color: #95a5a6;
}

.search-box input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.75rem;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.filter-group label {
  font-weight: 600;
  color: #2c3e50;
}

.filter-group select {
  padding: 0.75rem 1rem;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  min-width: 180px;
}

/* Table */
.table-container {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: linear-gradient(135deg, #1565c0 0%, #42a5f5 100%);
  color: white;
}

th, td {
  padding: 11px 14px;
  text-align: left;
  border-bottom: 1px solid #edf0f3;
  border-right: 1px solid #edf0f3;
}

th {
  font-weight: 600;
  font-size: 13px;
  white-space: nowrap;
  color: white;
  border-right: 1px solid rgba(255,255,255,0.15);
  border-bottom: none;
}

td:last-child, th:last-child {
  border-right: none;
}

tbody tr:nth-child(even) {
  background: #f0f7ff;
}

tbody tr:hover {
  background: #dceeff;
}

.wilayah-info {
  font-size: 0.9rem;
  color: #7f8c8d;
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
}

.badge-success {
  background: rgba(39, 174, 96, 0.1);
  color: #27ae60;
}

.badge-warning {
  background: rgba(243, 156, 18, 0.1);
  color: #f39c12;
}

.icon-inline {
  width: 14px;
  height: 14px;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-view, .btn-upload {
  padding: 0.5rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-view {
  background: #3498db;
  color: white;
}

.btn-view:hover {
  background: #2980b9;
}

.btn-upload {
  background: #27ae60;
  color: white;
}

.btn-upload:hover {
  background: #229954;
}

.btn-view svg, .btn-upload svg {
  width: 18px;
  height: 18px;
}

/* Modal */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-small {
  max-width: 500px;
}

.modal-large {
  max-width: 900px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e0e0e0;
}

.modal-header h3 {
  margin: 0;
  color: #2c3e50;
}

.close-btn {
  background: none;
  border: none;
  font-size: 2rem;
  color: #95a5a6;
  cursor: pointer;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;align-items: center;
  justify-content: center;
}

.close-btn:hover {
  color: #e74c3c;
}

.modal-body {
  padding: 1.5rem;
}

.petani-info {
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.petani-info p {
  margin: 0.5rem 0;
  color: #2c3e50;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #2c3e50;
}

.form-group input[type="file"] {
  width: 100%;
  padding: 0.75rem;
  border: 2px dashed #e0e0e0;
  border-radius: 8px;
  cursor: pointer;
}

.help-text {
  display: block;
  margin-top: 0.5rem;
  color: #7f8c8d;
  font-size: 0.85rem;
}

.preview-container {
  margin-top: 1rem;
}

.preview-image {
  width: 100%;
  max-height: 300px;
  object-fit: contain;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
}

.full-image {
  width: 100%;
  height: auto;
  display: block;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1.5rem;
  border-top: 1px solid #e0e0e0;
}

.btn-primary, .btn-secondary {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: #3498db;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #2980b9;
}

.btn-primary:disabled {
  background: #95a5a6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #ecf0f1;
  color: #2c3e50;
}

.btn-secondary:hover {
  background: #bdc3c7;
}

.loading {
  padding: 2rem;
  text-align: center;
  color: #7f8c8d;
}

.text-center {
  text-align: center;
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

/* ===== HERO BANNER ===== */
.hero-banner {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
  color: white;
  padding: 1.25rem 1.75rem;
  border-radius: 12px 12px 0 0;
  margin: -20px -20px 20px -20px;
}

.hero-banner.blue {
  background: linear-gradient(135deg, #1565c0 0%, #42a5f5 100%);
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

/* ===== MOBILE RESPONSIVE ===== */
@media (max-width: 768px) {
  .surat-pernyataan-view {
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

  .page-header {
    margin-bottom: 8px;
  }

  .header-left h1 {
    font-size: 16px;
  }

  .subtitle {
    font-size: 11px;
  }

  .stats-container {
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
    margin-bottom: 8px;
  }

  .stat-card {
    padding: 10px;
  }

  .stat-card h3 {
    font-size: 11px;
  }

  .stat-number {
    font-size: 20px;
  }

  .toolbar {
    flex-direction: column;
    gap: 8px;
    padding: 10px;
    margin-bottom: 8px;
  }

  .toolbar-left,
  .toolbar-right {
    flex-direction: row;
    flex-wrap: wrap;
    gap: 6px;
    width: 100%;
  }

  .search-box {
    min-width: unset;
    width: 100%;
    flex: 1;
  }

  .filter-group {
    flex: 1;
    min-width: 0;
  }

  .filter-group select {
    width: 100%;
    padding: 7px 8px;
    font-size: 12px;
  }

  .toolbar-right button {
    flex: 1;
    padding: 7px 10px;
    font-size: 12px;
  }

  .table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .table-container table {
    min-width: 520px;
    font-size: 12px;
  }

  .table-container th,
  .table-container td {
    padding: 6px 8px;
  }

  .modal {
    padding: 6px;
  }

  .modal-content {
    width: 98%;
    padding: 12px;
    max-height: 93vh;
    overflow-y: auto;
  }

  .modal-small {
    width: 96%;
  }

  .modal-large {
    width: 98%;
  }
}
</style>
