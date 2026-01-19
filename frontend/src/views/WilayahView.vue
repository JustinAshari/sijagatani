<template>
  <div class="wilayah-container">
    <div class="header">
      <h2>Data Wilayah</h2>
      <div class="header-actions">
        <button @click="exportAllData" class="btn-export">📥 Export Semua Data</button>
        <button @click="showImportModal = true" class="btn-import">📤 Import Data</button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
      <button 
        v-for="tab in tabs" 
        :key="tab.id"
        :class="['tab', { active: activeTab === tab.id }]"
        @click="changeTab(tab.id)">
        {{ tab.label }}
      </button>
    </div>

    <!-- Content for each tab -->
    <div class="tab-content">
      <!-- PROVINSI -->
      <div v-if="activeTab === 'provinsi'" class="content-section">
        <div class="section-header">
          <h3>Data Provinsi</h3>
          <div class="header-actions">
            <button @click="openModal('provinsi')" class="btn-primary">+ Tambah Provinsi</button>
          </div>
        </div>
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
            <tr v-if="loading"><td colspan="4" class="loading-cell">Loading...</td></tr>
            <tr v-else-if="provinsiList.length === 0"><td colspan="4" class="empty-cell">Tidak ada data</td></tr>
            <tr v-else v-for="(item, index) in provinsiList" :key="item.id">
              <td>{{ index + 1 }}</td>
              <td>{{ item.nama }}</td>
              <td>{{ item.kabupaten_count || 0 }}</td>
              <td class="actions">
                <button @click="editItem('provinsi', item)" class="btn-edit">Edit</button>
                <button @click="deleteItem('provinsi', item.id)" class="btn-delete">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- KABUPATEN -->
      <div v-if="activeTab === 'kabupaten'" class="content-section">
        <div class="section-header">
          <h3>Data Kabupaten</h3>
          <div class="header-actions">
            <button @click="openModal('kabupaten')" class="btn-primary">+ Tambah Kabupaten</button>
          </div>
        </div>
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
            <tr v-if="loading"><td colspan="5" class="loading-cell">Loading...</td></tr>
            <tr v-else-if="kabupatenList.length === 0"><td colspan="5" class="empty-cell">Tidak ada data</td></tr>
            <tr v-else v-for="(item, index) in kabupatenList" :key="item.id">
              <td>{{ index + 1 }}</td>
              <td>{{ item.nama }}</td>
              <td>{{ item.provinsi?.nama || '-' }}</td>
              <td>{{ item.kecamatan_count || 0 }}</td>
              <td class="actions">
                <button @click="editItem('kabupaten', item)" class="btn-edit">Edit</button>
                <button @click="deleteItem('kabupaten', item.id)" class="btn-delete">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- KECAMATAN -->
      <div v-if="activeTab === 'kecamatan'" class="content-section">
        <div class="section-header">
          <h3>Data Kecamatan</h3>
          <div class="header-actions">
            <button @click="openModal('kecamatan')" class="btn-primary">+ Tambah Kecamatan</button>
          </div>
        </div>
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
            <tr v-if="loading"><td colspan="6" class="loading-cell">Loading...</td></tr>
            <tr v-else-if="kecamatanList.length === 0"><td colspan="6" class="empty-cell">Tidak ada data</td></tr>
            <tr v-else v-for="(item, index) in kecamatanList" :key="item.id">
              <td>{{ index + 1 }}</td>
              <td>{{ item.nama }}</td>
              <td>{{ item.kabupaten?.nama || '-' }}</td>
              <td>{{ item.kabupaten?.provinsi?.nama || '-' }}</td>
              <td>{{ item.kalurahan_count || 0 }}</td>
              <td class="actions">
                <button @click="editItem('kecamatan', item)" class="btn-edit">Edit</button>
                <button @click="deleteItem('kecamatan', item.id)" class="btn-delete">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- KALURAHAN -->
      <div v-if="activeTab === 'kalurahan'" class="content-section">
        <div class="section-header">
          <h3>Data Kalurahan/Desa</h3>
          <div class="header-actions">
            <button @click="openModal('kalurahan')" class="btn-primary">+ Tambah Kalurahan</button>
          </div>
        </div>
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
            <tr v-if="loading"><td colspan="6" class="loading-cell">Loading...</td></tr>
            <tr v-else-if="kalurahanList.length === 0"><td colspan="6" class="empty-cell">Tidak ada data</td></tr>
            <tr v-else v-for="(item, index) in kalurahanList" :key="item.id">
              <td>{{ index + 1 }}</td>
              <td>{{ item.nama }}</td>
              <td>{{ item.kecamatan?.nama || '-' }}</td>
              <td>{{ item.kecamatan?.kabupaten?.nama || '-' }}</td>
              <td>{{ item.kecamatan?.kabupaten?.provinsi?.nama || '-' }}</td>
              <td class="actions">
                <button @click="editItem('kalurahan', item)" class="btn-edit">Edit</button>
                <button @click="deleteItem('kalurahan', item.id)" class="btn-delete">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>{{ isEdit ? 'Edit' : 'Tambah' }} {{ modalTitle }}</h3>
          <button @click="closeModal" class="btn-close">&times;</button>
        </div>
        <form @submit.prevent="submitForm">
          <!-- Provinsi form -->
          <div v-if="modalType === 'provinsi'" class="form-body">
            <div class="form-group">
              <label>Nama Provinsi</label>
              <input v-model="form.nama" type="text" required />
            </div>
          </div>

          <!-- Kabupaten form -->
          <div v-if="modalType === 'kabupaten'" class="form-body">
            <div class="form-group">
              <label>Provinsi</label>
              <select v-model="form.provinsi_id" required>
                <option value="">Pilih Provinsi</option>
                <option v-for="p in provinsiList" :key="p.id" :value="p.id">{{ p.nama }}</option>
              </select>
            </div>
            <div class="form-group">
              <label>Nama Kabupaten</label>
              <input v-model="form.nama" type="text" required />
            </div>
          </div>

          <!-- Kecamatan form -->
          <div v-if="modalType === 'kecamatan'" class="form-body">
            <div class="form-group">
              <label>Kabupaten</label>
              <select v-model="form.kabupaten_id" required>
                <option value="">Pilih Kabupaten</option>
                <option v-for="k in kabupatenList" :key="k.id" :value="k.id">
                  {{ k.nama }} ({{ k.provinsi?.nama }})
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Nama Kecamatan</label>
              <input v-model="form.nama" type="text" required />
            </div>
          </div>

          <!-- Kalurahan form -->
          <div v-if="modalType === 'kalurahan'" class="form-body">
            <div class="form-group">
              <label>Kecamatan</label>
              <select v-model="form.kecamatan_id" required>
                <option value="">Pilih Kecamatan</option>
                <option v-for="k in kecamatanList" :key="k.id" :value="k.id">
                  {{ k.nama }} ({{ k.kabupaten?.nama }})
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Nama Kalurahan/Desa</label>
              <input v-model="form.nama" type="text" required />
            </div>
          </div>

          <div class="form-actions">
            <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
            <button type="submit" class="btn-primary">{{ isEdit ? 'Update' : 'Simpan' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Import Modal -->
    <div v-if="showImportModal" class="modal-overlay" @click="closeImportModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Import Data Wilayah</h3>
          <button @click="closeImportModal" class="btn-close">&times;</button>
        </div>
        <div class="import-body">
          <div class="import-section">
            <h4>1. Download Template</h4>
            <p>Download template Excel dengan format: NO, PROVINSI, KABUPATEN, KECAMATAN, KELURAHAN/DESA:</p>
            <button @click="downloadTemplate" class="btn-template">
              📄 Download Template Data Wilayah
            </button>
          </div>

          <div class="import-section">
            <h4>2. Upload File Excel</h4>
            <p>Pilih file Excel (.xlsx, .xls, atau .csv):</p>
            <input 
              type="file" 
              @change="handleFileSelect" 
              accept=".xlsx,.xls,.csv"
              ref="fileInput"
              class="file-input"
            />
            <p v-if="selectedFile" class="file-name">📎 {{ selectedFile.name }}</p>
          </div>

          <div class="import-info">
            <strong>⚠️ Catatan:</strong>
            <ul>
              <li>Format: NO, PROVINSI, KABUPATEN, KECAMATAN, KELURAHAN/DESA</li>
              <li>Setiap baris adalah data kelurahan lengkap dengan hierarkinya</li>
              <li>Sistem akan otomatis membuat Provinsi, Kabupaten, Kecamatan jika belum ada</li>
              <li>Data duplikat akan diabaikan</li>
              <li>Maksimal ukuran: 5 MB</li>
            </ul>
          </div>
        </div>
        <div class="form-actions">
          <button type="button" @click="closeImportModal" class="btn-secondary">Batal</button>
          <button @click="importData" class="btn-primary" :disabled="!selectedFile || importing">
            {{ importing ? 'Mengimport...' : 'Import Sekarang' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
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

const changeTab = (tabId) => {
  activeTab.value = tabId
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

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.header h2 {
  color: #2c3e50;
  margin: 0;
}

.tabs {
  display: flex;
  gap: 5px;
  margin-bottom: 20px;
  border-bottom: 2px solid #e0e0e0;
}

.tab {
  padding: 12px 24px;
  border: none;
  background: transparent;
  cursor: pointer;
  font-weight: 600;
  color: #666;
  border-bottom: 3px solid transparent;
  transition: all 0.3s;
}

.tab:hover {
  color: #007bff;
}

.tab.active {
  color: #007bff;
  border-bottom-color: #007bff;
}

.content-section {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h3 {
  margin: 0;
  color: #2c3e50;
}

.header-actions {
  display: flex;
  gap: 10px;
}

.btn-export {
  background: #28a745;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
}

.btn-export:hover {
  background: #218838;
}

.btn-import {
  background: #17a2b8;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
}

.btn-import:hover {
  background: #138496;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table thead {
  background: #f8f9fa;
}

.data-table th {
  padding: 12px;
  text-align: left;
  font-weight: 600;
  color: #495057;
  border-bottom: 2px solid #dee2e6;
}

.data-table td {
  padding: 12px;
  border-bottom: 1px solid #dee2e6;
}

.data-table tbody tr:hover {
  background: #f8f9fa;
}

.loading-cell, .empty-cell {
  text-align: center;
  padding: 40px !important;
  color: #666;
}

.actions {
  display: flex;
  gap: 8px;
}

.btn-primary {
  background: #007bff;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.btn-primary:hover {
  background: #0056b3;
}

.btn-edit {
  padding: 6px 12px;
  background: #ffc107;
  color: #000;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-edit:hover {
  background: #e0a800;
}

.btn-delete {
  padding: 6px 12px;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-delete:hover {
  background: #c82333;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #dee2e6;
}

.modal-header h3 {
  margin: 0;
}

.btn-close {
  background: none;
  border: none;
  font-size: 28px;
  cursor: pointer;
  color: #999;
}

.btn-close:hover {
  color: #000;
}

.form-body {
  padding: 20px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-weight: 600;
  color: #495057;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 14px;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #007bff;
}

.form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  padding: 20px;
  border-top: 1px solid #dee2e6;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.btn-secondary:hover {
  background: #5a6268;
}

.import-body {
  padding: 20px;
}

.import-section {
  margin-bottom: 20px;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 5px;
}

.import-section h4 {
  margin: 0 0 10px 0;
  color: #2c3e50;
  font-size: 1rem;
}

.import-section p {
  margin: 5px 0;
  color: #666;
  font-size: 14px;
}

.btn-template {
  background: #6c757d;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  margin-top: 10px;
}

.btn-template:hover {
  background: #5a6268;
}

.file-input {
  margin-top: 10px;
  padding: 10px;
  border: 2px dashed #ddd;
  border-radius: 5px;
  width: 100%;
  cursor: pointer;
}

.file-name {
  margin-top: 10px;
  padding: 10px;
  background: #e7f3ff;
  border-radius: 5px;
  color: #004085;
  font-size: 14px;
}

.import-info {
  background: #fff3cd;
  padding: 15px;
  border-radius: 5px;
  border-left: 4px solid #ffc107;
}

.import-info strong {
  color: #856404;
  display: block;
  margin-bottom: 8px;
}

.import-info ul {
  margin: 5px 0 0 20px;
  color: #856404;
}

.import-info li {
  margin: 5px 0;
  font-size: 14px;
}
</style>
