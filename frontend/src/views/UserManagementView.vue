<template>
  <div class="user-management">
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
            <th>Email</th>
            <th>Role</th>
            <th>Dibuat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(user, index) in users" :key="user.id">
            <td>{{ index + 1 }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>
              <span class="badge" :class="'badge-' + user.role">
                {{ getRoleLabel(user.role) }}
              </span>
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
            <label>Email</label>
            <input v-model="form.email" type="email" required />
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
              <option value="petani">Petani</option>
              <option value="penggilingan">Penggilingan/Makloon</option>
            </select>
          </div>
          <div class="form-actions">
            <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
            <button type="submit" class="btn-primary">{{ isEdit ? 'Update' : 'Simpan' }}</button>
          </div>
        </form>
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
  email: '',
  password: '',
  role: ''
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
    email: user.email,
    password: '',
    role: user.role
  }
  showModal.value = true
}

const submitForm = async () => {
  try {
    const data = {
      name: form.value.name,
      email: form.value.email,
      role: form.value.role
    }
    
    if (form.value.password) {
      data.password = form.value.password
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
    email: '',
    password: '',
    role: ''
  }
}

const getRoleLabel = (role) => {
  const labels = {
    superadmin: 'Super Admin',
    admin: 'Administrator',
    petani: 'Petani',
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
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  overflow: hidden;
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

.badge-petani {
  background: #e8f5e9;
  color: #388e3c;
}

.badge-penggilingan {
  background: #fff3e0;
  color: #f57c00;
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
  display: flex;
  align-items: center;
  gap: 5px;
}

.btn-primary:hover {
  background: #0056b3;
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
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
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
</style>
