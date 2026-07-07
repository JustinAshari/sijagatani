<template>
  <div class="user-management" style="padding: 20px;">

    <!-- Hero Banner -->
    <div class="hero-banner purple">
      <div class="hero-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <div class="hero-text">
        <h2>Kelola Akun</h2>
        <p>Kelola akun pengguna dan hak akses dalam sistem</p>
      </div>
    </div>

    <!-- Main Card Content -->
    <div class="page-card">
      <div class="toolbar">
        <div class="toolbar-left">
          <h3 style="font-size: 1.1rem; font-weight: 700; color: #6b21a8; margin: 0;">Daftar Pengguna</h3>
        </div>
        <div class="toolbar-right">
          <button @click="showModal = true" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-inline" style="width: 14px; height: 14px; margin-right: 4px; display: inline-block; vertical-align: middle;">
              <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah User
          </button>
        </div>
      </div>

      <!-- Data Table -->
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Username</th>
              <th>Role</th>
              <th>Nama Penggilingan</th>
              <th>Dibuat</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="7" class="loading-cell">
                <div class="loading-wrap">
                  <div class="spinner"></div>
                  <span>Memuat data user...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="users.length === 0">
              <td colspan="7" class="empty-cell" style="text-align: center; padding: 2rem 0; color: #9ea9b8;">Tidak ada data user</td>
            </tr>
            <tr v-else v-for="(user, index) in paginatedUsers" :key="user.id">
              <td class="td-num">{{ rowNumber(index) }}</td>
              <td class="font-semibold">{{ user.name }}</td>
              <td class="font-medium">{{ user.username }}</td>
              <td>
                <span class="badge" :class="'badge-' + user.role">
                  {{ getRoleLabel(user.role) }}
                </span>
              </td>
              <td>
                <span v-if="user.role === 'penggilingan'" class="font-semibold" style="color: #6b21a8;">
                  {{ user.nama_penggilingan || '-' }}
                </span>
                <span v-else class="text-muted">-</span>
              </td>
              <td class="td-date">{{ formatDate(user.created_at) }}</td>
              <td class="td-actions">
                <button 
                  v-if="user.role !== 'superadmin'" 
                  @click="editUser(user)" 
                  class="btn-icon btn-edit" 
                  title="Edit">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </button>
                <button 
                  v-if="user.role !== 'superadmin'" 
                  @click="deleteUser(user.id)" 
                  class="btn-icon btn-del" 
                  title="Hapus">
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
      <div class="pagination-bar" v-if="users.length">
        <div class="pagination-info">
          Menampilkan {{ pageStart }}-{{ pageEnd }} dari {{ users.length }} data
        </div>
        <div class="pagination-controls">
          <label for="user-per-page">Baris:</label>
          <select id="user-per-page" v-model="perPage" class="per-page-select">
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

    <!-- Modal Add/Edit User -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h3>{{ isEdit ? 'Edit User' : 'Tambah User' }}</h3>
          <button @click="closeModal" class="btn-close">&times;</button>
        </div>
        <form @submit.prevent="submitForm" class="modal-form">
          <div class="form-group">
            <label>Nama Lengkap <span class="required">*</span></label>
            <input v-model="form.name" type="text" required placeholder="Contoh: Ahmad Fauzi" />
          </div>
          <div class="form-group">
            <label>Username <span class="required">*</span></label>
            <input v-model="form.username" type="text" required placeholder="Contoh: fauzi123" />
          </div>
          <div class="form-group">
            <label>Password <span v-if="isEdit" style="font-weight: normal; color: #6b7280;">(kosongkan jika tidak diubah)</span><span v-else class="required">*</span></label>
            <input 
              v-model="form.password" 
              type="password" 
              :required="!isEdit"
              minlength="6"
              placeholder="Minimal 6 karakter"
            />
          </div>
          <div class="form-group">
            <label>Role <span class="required">*</span></label>
            <select v-model="form.role" required>
              <option value="">Pilih Role</option>
              <option value="admin">Administrator</option>
              <option value="lapangan">Admin Lapangan Bulog</option>
              <option value="penggilingan">Penggilingan/Makloon</option>
            </select>
          </div>
          <!-- Nama penggilingan hanya untuk role penggilingan -->
          <div v-if="form.role === 'penggilingan'" class="form-group">
            <label>Nama Penggilingan <span class="required">*</span></label>
            <input 
              v-model="form.nama_penggilingan" 
              type="text" 
              placeholder="Contoh: Penggilingan Maju Bersama"
              :required="form.role === 'penggilingan'"
            />
            <small class="form-hint" style="display: block; margin-top: 4px; color: #6b7280; font-size: 0.75rem;">Akun ini hanya dapat mengelola data dengan nama penggilingan tersebut.</small>
          </div>
          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn-cancel">Batal</button>
            <button type="submit" class="btn-primary">{{ isEdit ? 'Update' : 'Simpan' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '../services/api'

const users = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const currentPage = ref(1)
const perPage = ref('10')
const form = ref({
  id: null,
  name: '',
  username: '',
  password: '',
  role: '',
  nama_penggilingan: ''
})

const totalPages = computed(() => {
  if (perPage.value === 'all') return 1
  const size = Number(perPage.value) || 10
  return Math.max(1, Math.ceil(users.value.length / size))
})

const paginatedUsers = computed(() => {
  if (perPage.value === 'all') return users.value
  const size = Number(perPage.value) || 10
  const start = (currentPage.value - 1) * size
  return users.value.slice(start, start + size)
})

const pageStart = computed(() => {
  if (!users.value.length) return 0
  if (perPage.value === 'all') return 1
  return (currentPage.value - 1) * (Number(perPage.value) || 10) + 1
})

const pageEnd = computed(() => {
  if (!users.value.length) return 0
  if (perPage.value === 'all') return users.value.length
  return Math.min(currentPage.value * (Number(perPage.value) || 10), users.value.length)
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

watch([users, perPage], () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = totalPages.value
  }
  if (currentPage.value < 1) {
    currentPage.value = 1
  }
})

const fetchUsers = async () => {
  loading.value = true
  try {
    const response = await api.get('/users')
    if (response.data.success) {
      users.value = response.data.data
      currentPage.value = 1
    }
  } catch (error) {
    console.error('Error fetching users:', error)
    alert('Gagal memuat data users')
  } finally {
    loading.value = false
  }
}

const editUser = (user) => {
  isEdit.value = true
  form.value = {
    id: user.id,
    name: user.name,
    username: user.username,
    password: '',
    role: user.role,
    nama_penggilingan: user.nama_penggilingan || ''
  }
  showModal.value = true
}

const submitForm = async () => {
  try {
    const data = {
      name: form.value.name,
      username: form.value.username,
      role: form.value.role
    }
    
    if (form.value.password) {
      data.password = form.value.password
    }

    if (form.value.role === 'penggilingan') {
      data.nama_penggilingan = form.value.nama_penggilingan
    }

    if (isEdit.value) {
      await api.put(`/users/${form.value.id}`, data)
      alert('User berhasil diupdate')
    } else {
      await api.post('/users', data)
      alert('User berhasil ditambahkan')
    }
    
    closeModal()
    fetchUsers()
  } catch (error) {
    console.error('Error saving user:', error)
    alert(error.response?.data?.message || 'Gagal menyimpan user')
  }
}

const deleteUser = async (id) => {
  if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) return
  
  try {
    await api.delete(`/users/${id}`)
    alert('User berhasil dihapus')
    fetchUsers()
  } catch (error) {
    console.error('Error deleting user:', error)
    alert(error.response?.data?.message || 'Gagal menghapus user')
  }
}

const closeModal = () => {
  showModal.value = false
  isEdit.value = false
  form.value = {
    id: null,
    name: '',
    username: '',
    password: '',
    role: '',
    nama_penggilingan: ''
  }
}

const getRoleLabel = (role) => {
  const labels = {
    superadmin: 'Super Admin',
    admin: 'Administrator',
    lapangan: 'Admin Lapangan Bulog',
    penggilingan: 'Penggilingan'
  }
  return labels[role] || role
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
}

onMounted(() => {
  fetchUsers()
})
</script>

<style scoped>
.user-management {
  padding: 20px;
}

/* Hero Banner */
.hero-banner.purple {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  background: linear-gradient(135deg, #6b21a8 0%, #a855f7 100%);
  border-radius: 16px;
  padding: 1.75rem 2rem;
  color: #fff;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 20px rgba(107, 33, 168, 0.15);
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
}
.toolbar-left {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex: 1;
}

/* Buttons */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #6b21a8, #7e22ce);
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
.font-semibold { font-weight: 600; }
.font-medium { font-weight: 500; }
.td-date { color: #6b7280; font-size: 0.82rem; }

/* Status Badges */
.badge {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 5px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}
.badge-superadmin { background: #eff6ff; color: #1e40af; }
.badge-admin { background: #faf5ff; color: #6b21a8; }
.badge-lapangan { background: #f0fdf4; color: #166534; }
.badge-penggilingan { background: #fff7ed; color: #9a3412; }

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
  border-top-color: #6b21a8;
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
  border-color: #6b21a8;
  box-shadow: 0 0 0 3px rgba(107, 33, 168, 0.12);
}
.required { color: #ef4444; }

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

@media (max-width: 640px) {
  .hero-banner.purple { padding: 1.25rem; }
  .toolbar { flex-direction: column; align-items: stretch; }
}
</style>
