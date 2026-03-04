<template>
  <div class="user-management">
    <div class="page-card">

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

    <div class="header">
      <h2>Kelola Akun Pengguna</h2>
      <button @click="showModal = true" class="btn-primary">
        <span>+</span> Tambah User
      </button>
    </div>

    <div v-if="loading" class="loading">Loading...</div>
    <div v-else class="table-container">
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
          <tr v-for="(user, index) in users" :key="user.id">
            <td>{{ index + 1 }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.username }}</td>
            <td>
              <span class="badge" :class="'badge-' + user.role">
                {{ getRoleLabel(user.role) }}
              </span>
            </td>
            <td>
              <span v-if="user.role === 'penggilingan'" class="penggilingan-name">
                {{ user.nama_penggilingan || '-' }}
              </span>
              <span v-else class="text-muted">-</span>
            </td>
            <td>{{ formatDate(user.created_at) }}</td>
            <td class="actions">
              <button 
                v-if="user.role !== 'superadmin'" 
                @click="editUser(user)" 
                class="btn-warning" 
                title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
              </button>
              <button 
                v-if="user.role !== 'superadmin'" 
                @click="deleteUser(user.id)" 
                class="btn-danger" 
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

    <!-- Modal Add/Edit User -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>{{ isEdit ? 'Edit User' : 'Tambah User' }}</h3>
          <button @click="closeModal" class="btn-close">&times;</button>
        </div>
        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label>Nama Lengkap</label>
            <input v-model="form.name" type="text" required />
          </div>
          <div class="form-group">
            <label>Username</label>
            <input v-model="form.username" type="text" required />
          </div>
          <div class="form-group">
            <label>Password {{ isEdit ? '(kosongkan jika tidak diubah)' : '' }}</label>
            <input 
              v-model="form.password" 
              type="password" 
              :required="!isEdit"
              minlength="6"
            />
          </div>
          <div class="form-group">
            <label>Role</label>
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
            <small class="form-hint">Akun ini hanya dapat mengelola data dengan nama penggilingan tersebut.</small>
          </div>
          <div class="form-actions">
            <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
            <button type="submit" class="btn-primary">{{ isEdit ? 'Update' : 'Simpan' }}</button>
          </div>
        </form>
      </div>
    </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const users = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const form = ref({
  id: null,
  name: '',
  username: '',
  password: '',
  role: '',
  nama_penggilingan: ''
})

const fetchUsers = async () => {
  loading.value = true
  try {
    const response = await api.get('/users')
    if (response.data.success) {
      users.value = response.data.data
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

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.header h2 {
  color: #2c3e50;
  font-size: 24px;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #666;
}

.table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  overflow: hidden;
  border: 1px solid #e8ecef;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table thead {
  background: linear-gradient(135deg, #1565c0 0%, #42a5f5 100%);
}

.data-table th {
  padding: 13px 14px;
  text-align: left;
  font-weight: 600;
  font-size: 13px;
  color: white;
  white-space: nowrap;
  border-right: 1px solid rgba(255,255,255,0.15);
  border-bottom: none;
}

.data-table th:last-child {
  border-right: none;
}

.data-table td {
  padding: 11px 14px;
  border-bottom: 1px solid #edf0f3;
  border-right: 1px solid #edf0f3;
  font-size: 13.5px;
  color: #374151;
}

.data-table td:last-child {
  border-right: none;
}

.data-table tbody tr:nth-child(even) {
  background: #f0f7ff;
}

.data-table tbody tr:hover {
  background: #dceeff;
}

.badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.badge-superadmin {
  background: #e3f2fd;
  color: #1976d2;
}

.badge-admin {
  background: #f3e5f5;
  color: #7b1fa2;
}

.badge-lapangan {
  background: #e8f5e9;
  color: #388e3c;
}

.badge-penggilingan {
  background: #fff3e0;
  color: #f57c00;
}

.penggilingan-name {
  font-weight: 600;
  color: #f57c00;
}

.text-muted {
  color: #aaa;
}

.required {
  color: #dc3545;
  margin-left: 2px;
}

.form-hint {
  display: block;
  margin-top: 4px;
  font-size: 12px;
  color: #6c757d;
  font-style: italic;
}

.actions {
  display: flex;
  gap: 8px;
}

.btn-primary {
  background: #1565c0;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 5px;
}

.btn-primary:hover {
  background: #0d47a1;
}

.btn-warning, .btn-danger {
  padding: 6px 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.btn-warning {
  background: #ffc107;
  color: #000;
}

.btn-warning:hover {
  background: #e0a800;
}

.btn-danger {
  background: #dc3545;
  color: white;
}

.btn-danger:hover {
  background: #c82333;
}

.btn-warning svg, .btn-danger svg {
  width: 16px;
  height: 16px;
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
  padding: 0;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
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
  color: #2c3e50;
}

.btn-close {
  background: none;
  border: none;
  font-size: 28px;
  cursor: pointer;
  color: #999;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-close:hover {
  color: #000;
}

form {
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
  border-color: #1565c0;
  box-shadow: 0 0 0 3px rgba(21,101,192,0.1);
}

.form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  margin-top: 20px;
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

/* ===== HERO BANNER ===== */
.hero-banner {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  background: linear-gradient(135deg, #1565c0 0%, #42a5f5 100%);
  color: white;
  padding: 1.25rem 1.75rem;
  border-radius: 12px 12px 0 0;
  margin: -20px -20px 20px -20px;
}

.hero-banner.purple {
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
  .user-management {
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

  .header {
    flex-direction: column;
    gap: 6px;
    align-items: stretch;
    margin-bottom: 10px;
  }

  .header h2 {
    font-size: 16px;
  }

  .header .btn-primary {
    padding: 7px 12px;
    font-size: 13px;
  }

  .table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .data-table {
    min-width: 480px;
    font-size: 12px;
  }

  .data-table th,
  .data-table td {
    padding: 6px 8px;
  }

  .modal-overlay {
    padding: 6px;
  }

  .modal-content {
    width: 98%;
    padding: 14px;
    max-height: 93vh;
    overflow-y: auto;
  }

  .modal-header h3 {
    font-size: 15px;
  }

  .form-group input,
  .form-group select {
    padding: 7px 10px;
    font-size: 13px;
  }

  .form-actions {
    flex-direction: row;
    flex-wrap: wrap;
    gap: 8px;
  }

  .form-actions button {
    flex: 1;
    padding: 8px 12px;
    font-size: 13px;
  }
}
</style>
