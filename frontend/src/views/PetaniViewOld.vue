<template>
  <div class="petani-list">
    <div class="header">
      <h2>Data Petani - Jawa Tengah</h2>
      <div class="header-actions">
        <button @click="exportExcel" class="btn-success" :disabled="loading">
          <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Export Excel
        </button>
        <button @click="showAddModal = true" class="btn-primary">
          <span>+</span> Tambah Petani
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
        <span class="stat-label">Kabupaten {{ filterKabupaten }}:</span>
        <span class="stat-value">{{ filteredPetani.length }}</span>
      </div>
    </div>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Foto</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Kabupaten</th>
            <th>Kecamatan</th>
            <th>Alamat</th>
            <th>No. Telepon</th>
            <th>Jenis Kelamin</th>
            <th>Jumlah Lahan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(petani, index) in filteredPetani" :key="petani.id">
            <td>{{ index + 1 }}</td>
            <td>
              <img 
                v-if="petani.foto" 
                :src="getImageUrl(petani.foto)" 
                alt="Foto Petani"
                class="photo-thumb"
              />
              <span v-else class="no-photo">No Photo</span>
            </td>
            <td>{{ petani.nik }}</td>
            <td>{{ petani.nama }}</td>
            <td>{{ petani.kabupaten }}</td>
            <td>{{ petani.kecamatan }}</td>
            <td>{{ petani.alamat }}</td>
            <td>{{ petani.no_telepon || '-' }}</td>
            <td>{{ petani.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            <td>{{ petani.lahan?.length || 0 }}</td>
            <td>
              <div class="action-buttons">
                <button @click="viewDetail(petani)" class="btn-info" title="Detail">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
                <button @click="editPetani(petani)" class="btn-warning" title="Edit">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </button>
                <button @click="deletePetani(petani.id)" class="btn-danger" title="Hapus">
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
      <div class="modal-content">
        <div class="modal-header">
          <h3>{{ showEditModal ? 'Edit Petani' : 'Tambah Petani Baru' }}</h3>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>
        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label>NIK * <span class="nik-status" :class="nikStatus.class">{{ nikStatus.message }}</span></label>
            <input v-model="form.nik" type="text" maxlength="16" required @input="checkNikDuplicate" />
          </div>
          <div class="form-group">
            <label>Nama *</label>
            <input v-model="form.nama" type="text" required />
          </div>
          <div class="form-group">
            <label>Kabupaten *</label>
            <select v-model="form.kabupaten" required>
              <option value="">Pilih Kabupaten</option>
              <option v-for="kab in kabupatenList" :key="kab" :value="kab">
                {{ kab }}
              </option>
            </select>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Kecamatan *</label>
              <input v-model="form.kecamatan" type="text" required />
            </div>
            <div class="form-group">
              <label>Desa</label>
              <input v-model="form.desa" type="text" />
            </div>
          </div>
          <div class="form-group">
            <label>Alamat Lengkap *</label>
            <textarea v-model="form.alamat" rows="3" required></textarea>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>No. Telepon</label>
              <input v-model="form.no_telepon" type="text" maxlength="15" />
            </div>
            <div class="form-group">
              <label>Jenis Kelamin *</label>
              <select v-model="form.jenis_kelamin" required>
                <option value="">Pilih</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Tanggal Lahir</label>
            <input v-model="form.tanggal_lahir" type="date" />
          </div>
          <div class="form-group">
            <label>Foto</label>
            <input type="file" @change="handleFileUpload" accept="image/*" />
            <small>Format: JPG, PNG. Max: 5MB (akan dikompres otomatis ke resolusi 600px)</small>
            <img v-if="previewImage" :src="previewImage" class="preview-image" />
          </div>
          <div class="modal-footer">
            <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
            <button type="submit" class="btn-primary" :disabled="loading || nikStatus.isDuplicate">
              {{ loading ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Detail -->
    <div v-if="showDetailModal" class="modal" @click.self="showDetailModal = false">
      <div class="modal-content modal-detail">
        <div class="modal-header">
          <h3>Detail Petani</h3>
          <button @click="showDetailModal = false" class="close-btn">&times;</button>
        </div>
        <div class="detail-content" v-if="selectedPetani">
          <div class="detail-photo">
            <img 
              v-if="selectedPetani.foto" 
              :src="getImageUrl(selectedPetani.foto)" 
              alt="Foto Petani"
            />
            <div v-else class="no-photo-large">No Photo</div>
          </div>
          <div class="detail-info">
            <div class="info-row">
              <strong>NIK:</strong>
              <span>{{ selectedPetani.nik }}</span>
            </div>
            <div class="info-row">
              <strong>Nama:</strong>
              <span>{{ selectedPetani.nama }}</span>
            </div>
            <div class="info-row">
              <strong>Kabupaten:</strong>
              <span>{{ selectedPetani.kabupaten }}</span>
            </div>
            <div class="info-row">
              <strong>Kecamatan:</strong>
              <span>{{ selectedPetani.kecamatan }}</span>
            </div>
            <div class="info-row">
              <strong>Desa:</strong>
              <span>{{ selectedPetani.desa || '-' }}</span>
            </div>
            <div class="info-row">
              <strong>Alamat:</strong>
              <span>{{ selectedPetani.alamat }}</span>
            </div>
            <div class="info-row">
              <strong>No. Telepon:</strong>
              <span>{{ selectedPetani.no_telepon || '-' }}</span>
            </div>
            <div class="info-row">
              <strong>Jenis Kelamin:</strong>
              <span>{{ selectedPetani.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
            </div>
            <div class="info-row">
              <strong>Tanggal Lahir:</strong>
              <span>{{ formatDate(selectedPetani.tanggal_lahir) }}</span>
            </div>
            <div class="info-row">
              <strong>Jumlah Lahan:</strong>
              <span>{{ selectedPetani.lahan?.length || 0 }} lahan</span>
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
const previewImage = ref(null)
const kabupatenList = ref(kabupatenJawaTengah)
const nikCheckTimeout = ref(null)
const nikStatus = ref({ message: '', class: '', isDuplicate: false })

const form = ref({
  nik: '',
  nama: '',
  alamat: '',
  kabupaten: '',
  kecamatan: '',
  desa: '',
  no_telepon: '',
  jenis_kelamin: '',
  tanggal_lahir: '',
  foto: null
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
    const response = await api.get('/petani')
    petaniList.value = response.data.data
  } catch (error) {
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
      // Check in local list first
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

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.value.foto = file
    const reader = new FileReader()
    reader.onload = (e) => {
      previewImage.value = e.target.result
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
    nik: petani.nik,
    nama: petani.nama,
    alamat: petani.alamat,
    kabupaten: petani.kabupaten,
    kecamatan: petani.kecamatan,
    desa: petani.desa,
    no_telepon: petani.no_telepon,
    jenis_kelamin: petani.jenis_kelamin,
    tanggal_lahir: petani.tanggal_lahir,
    foto: null
  }
  if (petani.foto) {
    previewImage.value = getImageUrl(petani.foto)
  }
  showEditModal.value = true
}

const deletePetani = async (id) => {
  if (!confirm('Yakin ingin menghapus data petani ini? Semua lahan yang dimiliki juga akan terhapus.')) return
  
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
  previewImage.value = null
  nikStatus.value = { message: '', class: '', isDuplicate: false }
  form.value = {
    nik: '',
    nama: '',
    alamat: '',
    kabupaten: '',
    kecamatan: '',
    desa: '',
    no_telepon: '',
    jenis_kelamin: '',
    tanggal_lahir: '',
    foto: null
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
}

.photo-thumb {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 5px;
}

.no-photo {
  display: flex;
  width: 50px;
  height: 50px;
  background: #e9ecef;
  border-radius: 5px;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  color: #6c757d;
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
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 8px;
  max-width: 600px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.close-btn {
  background: none;
  border: none;
  font-size: 30px;
  cursor: pointer;
  color: #6c757d;
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

.form-group small {
  display: block;
  margin-top: 5px;
  color: #6c757d;
  font-size: 12px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.preview-image {
  margin-top: 10px;
  max-width: 200px;
  border-radius: 5px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.detail-content {
  display: grid;
  grid-template-columns: 200px 1fr;
  gap: 30px;
}

.detail-photo img {
  width: 100%;
  border-radius: 8px;
}

.no-photo-large {
  width: 100%;
  height: 250px;
  background: #e9ecef;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6c757d;
}

.info-row {
  display: grid;
  grid-template-columns: 150px 1fr;
  padding: 10px 0;
  border-bottom: 1px solid #eee;
}

.text-center {
  text-align: center;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .table-container {
    overflow-x: auto;
  }
  
  table {
    min-width: 1000px;
  }
}

@media (max-width: 992px) {
  .detail-content {
    grid-template-columns: 1fr;
  }

  .detail-photo img {
    max-width: 300px;
    margin: 0 auto;
    display: block;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .stats-bar {
    flex-wrap: wrap;
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

  .header h2 {
    font-size: 1.5rem;
  }

  .header-actions {
    flex-direction: column;
    width: 100%;
  }

  .header-actions button {
    width: 100%;
  }

  .filter-section {
    padding: 15px;
  }

  .filter-row {
    flex-direction: column;
  }

  .search-input {
    min-width: 100%;
  }

  .filter-select {
    width: 100%;
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

  .stat-item {
    justify-content: space-between;
  }

  .modal-content {
    width: 95%;
    padding: 20px;
    max-height: 95vh;
  }

  .modal-header h3 {
    font-size: 1.2rem;
  }

  .action-buttons {
    flex-direction: column;
  }

  .action-buttons button {
    width: 100%;
  }

  .info-row {
    grid-template-columns: 1fr;
    gap: 5px;
  }

  .info-row strong {
    color: #6c757d;
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .header h2 {
    font-size: 1.2rem;
  }

  th, td {
    padding: 8px;
    font-size: 12px;
  }

  .photo-thumb {
    width: 40px;
    height: 40px;
  }

  .no-photo {
    width: 40px;
    height: 40px;
    font-size: 8px;
  }

  .modal-content {
    padding: 15px;
  }

  .form-group input,
  .form-group textarea,
  .form-group select {
    font-size: 16px; /* Prevents zoom on iOS */
  }

  .btn-primary,
  .btn-secondary,
  .btn-filter,
  .btn-reset,
  .btn-success {
    padding: 12px 20px; /* Larger touch targets */
  }
}
</style>
