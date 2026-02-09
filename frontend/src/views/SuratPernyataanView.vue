<template>
  <div class="surat-pernyataan-view">
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
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>NIK</th>
            <th>Nama Petani</th>
            <th>Wilayah</th>
            <th>No. Telepon</th>
            <th>Status Upload</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="8" class="text-center">
              <div class="loading">Memuat data...</div>
            </td>
          </tr>
          <tr v-else-if="filteredPetani.length === 0">
            <td colspan="8" class="text-center">Tidak ada data</td>
          </tr>
          <tr v-else v-for="(petani, index) in filteredPetani" :key="petani.id">
            <td>{{ index + 1 }}</td>
            <td>{{ formatDate(petani.tanggal) }}</td>
            <td>{{ petani.nik }}</td>
            <td>{{ petani.nama }}</td>
            <td>
              <div class="wilayah-info">
                {{ petani.kalurahan?.nama || '-' }}, {{ petani.kecamatan?.nama || '-' }}
              </div>
            </td>
            <td>{{ petani.no_telepon || '-' }}</td>
            <td>
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
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const petaniList = ref([])
const searchQuery = ref('')
const filterStatus = ref('')
const loading = ref(false)
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

const getImageUrl = (path) => {
  if (!path) return ''
  const baseURL = import.meta.env.VITE_API_URL || 'https://bulog.dev.sijagatani.com/api'
  return baseURL.replace('/api', '') + '/storage/' + path
}

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
  background: #f8f9fa;
}

th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e0e0e0;
}

th {
  font-weight: 600;
  color: #2c3e50;
  white-space: nowrap;
}

tbody tr:hover {
  background: #f8f9fa;
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
</style>
