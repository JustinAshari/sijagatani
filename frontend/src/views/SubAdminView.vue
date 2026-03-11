<template>
  <div class="sub-admin-page">

    <!-- Hero -->
    <div class="hero-banner">
      <div class="hero-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <div class="hero-text">
        <h2>Kelola Sub-Admin</h2>
        <p>Anda dapat menambahkan admin lain untuk perusahaan <strong>{{ authStore.namaPenggilingan }}</strong></p>
      </div>
    </div>

    <div class="page-card">
      <div class="toolbar">
        <div class="toolbar-left">
          <span class="count-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            {{ subAdmins.length }} Sub-Admin
          </span>
        </div>
        <button @click="openAdd" class="btn-primary">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Tambah Sub-Admin
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading-wrap">
        <div class="spinner"></div>
        <span>Memuat data...</span>
      </div>

      <!-- Empty -->
      <div v-else-if="!subAdmins.length" class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <line x1="19" y1="8" x2="19" y2="14"/>
          <line x1="22" y1="11" x2="16" y2="11"/>
        </svg>
        <p>Belum ada sub-admin</p>
        <small>Klik <strong>Tambah Sub-Admin</strong> untuk memberikan akses kepada anggota tim Anda</small>
      </div>

      <!-- Table -->
      <div v-else class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Username</th>
              <th>Perusahaan</th>
              <th>Ditambahkan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(sa, idx) in subAdmins" :key="sa.id">
              <td class="td-num">{{ idx + 1 }}</td>
              <td class="td-name">
                <div class="name-cell">
                  <span class="avatar">{{ initials(sa.name) }}</span>
                  {{ sa.name }}
                </div>
              </td>
              <td><code class="username-code">{{ sa.username }}</code></td>
              <td><span class="company-badge">{{ sa.nama_penggilingan }}</span></td>
              <td class="td-date">{{ formatDate(sa.created_at) }}</td>
              <td class="td-actions">
                <button @click="openEdit(sa)" class="btn-icon btn-edit" title="Edit">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <button @click="confirmDelete(sa)" class="btn-icon btn-del" title="Hapus">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h3>{{ isEdit ? 'Edit Sub-Admin' : 'Tambah Sub-Admin' }}</h3>
          <button class="btn-close" @click="closeModal">&times;</button>
        </div>

        <div class="modal-info">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          Sub-admin akan memiliki akses yang sama ke data <strong>{{ authStore.namaPenggilingan }}</strong>.
        </div>

        <form @submit.prevent="submitForm" class="modal-form">
          <div class="form-group">
            <label>Nama Lengkap <span class="req">*</span></label>
            <input v-model="form.name" type="text" placeholder="Contoh: Jono Santoso" required />
          </div>
          <div class="form-group">
            <label>Username <span class="req">*</span></label>
            <input v-model="form.username" type="text" placeholder="Contoh: jono_sumberrezeki" required />
          </div>
          <div class="form-group">
            <label>Password <span v-if="!isEdit" class="req">*</span><span v-else class="hint"> (kosongkan jika tidak diubah)</span></label>
            <div class="input-pw">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Minimal 6 karakter"
                :required="!isEdit"
                minlength="6"
              />
              <button type="button" class="pw-toggle" @click="showPassword = !showPassword">
                <svg v-if="!showPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
          </div>
          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
            <button type="submit" class="btn-primary" :disabled="saving">
              <span v-if="saving" class="btn-spinner"></span>
              {{ isEdit ? 'Simpan Perubahan' : 'Tambah Sub-Admin' }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()

const subAdmins = ref([])
const loading   = ref(true)
const saving    = ref(false)
const showModal = ref(false)
const isEdit    = ref(false)
const showPassword = ref(false)

const emptyForm = () => ({ id: null, name: '', username: '', password: '' })
const form = ref(emptyForm())

const fetchSubAdmins = async () => {
  loading.value = true
  try {
    const res = await api.get('/my-sub-admins')
    subAdmins.value = res.data.data || []
  } catch {
    alert('Gagal memuat data sub-admin')
  } finally {
    loading.value = false
  }
}

const openAdd = () => {
  isEdit.value = false
  showPassword.value = false
  form.value = emptyForm()
  showModal.value = true
}

const openEdit = (sa) => {
  isEdit.value = true
  showPassword.value = false
  form.value = { id: sa.id, name: sa.name, username: sa.username, password: '' }
  showModal.value = true
}

const closeModal = () => { showModal.value = false }

const submitForm = async () => {
  saving.value = true
  try {
    const payload = { name: form.value.name, username: form.value.username }
    if (form.value.password) payload.password = form.value.password

    if (isEdit.value) {
      await api.put(`/my-sub-admins/${form.value.id}`, payload)
      alert('Sub-admin berhasil diupdate')
    } else {
      payload.password = form.value.password
      await api.post('/my-sub-admins', payload)
      alert('Sub-admin berhasil ditambahkan')
    }

    closeModal()
    await fetchSubAdmins()
  } catch (err) {
    const msg = err.response?.data?.message || 'Terjadi kesalahan'
    const errors = err.response?.data?.errors
    if (errors) {
      const detail = Object.values(errors).flat().join('\n')
      alert(msg + '\n' + detail)
    } else {
      alert(msg)
    }
  } finally {
    saving.value = false
  }
}

const confirmDelete = async (sa) => {
  if (!confirm(`Hapus sub-admin "${sa.name}"? Tindakan ini tidak dapat dibatalkan.`)) return
  try {
    await api.delete(`/my-sub-admins/${sa.id}`)
    subAdmins.value = subAdmins.value.filter(s => s.id !== sa.id)
  } catch {
    alert('Gagal menghapus sub-admin')
  }
}

const initials = (name) => {
  return name.split(' ').slice(0, 2).map(w => w[0]?.toUpperCase()).join('')
}

const formatDate = (dt) => {
  if (!dt) return '-'
  return new Date(dt).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(fetchSubAdmins)
</script>

<style scoped>
.sub-admin-page { padding: 20px; }

/* Hero */
.hero-banner {
  display: flex; align-items: center; gap: 1.25rem;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  border-radius: 16px; padding: 1.75rem 2rem; color: #fff;
  margin-bottom: 1.5rem;
}
.hero-icon {
  width: 52px; height: 52px; border-radius: 14px;
  background: rgba(255,255,255,.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.hero-icon svg { width: 26px; height: 26px; }
.hero-text h2 { font-size: 1.3rem; font-weight: 700; margin-bottom: .25rem; }
.hero-text p  { font-size: .875rem; opacity: .85; }

/* Page card */
.page-card {
  background: #fff; border-radius: 16px;
  border: 1px solid #e8ecf0; padding: 1.5rem;
  box-shadow: 0 2px 10px rgba(0,0,0,.04);
}

.toolbar {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 1.25rem; flex-wrap: wrap; gap: .75rem;
}
.toolbar-left { display: flex; align-items: center; gap: .75rem; }
.count-badge {
  display: inline-flex; align-items: center; gap: 6px;
  background: #f0f7ff; border: 1px solid #bfdbfe; color: #1d4ed8;
  padding: 5px 13px; border-radius: 8px; font-size: .82rem; font-weight: 600;
}
.count-badge svg { width: 14px; height: 14px; }

/* Buttons */
.btn-primary {
  display: inline-flex; align-items: center; gap: 6px;
  background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff;
  border: none; border-radius: 9px; padding: 9px 18px;
  font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .15s;
}
.btn-primary:hover { filter: brightness(1.1); transform: translateY(-1px); }
.btn-primary:disabled { opacity: .6; cursor: not-allowed; }
.btn-primary svg { width: 15px; height: 15px; }
.btn-secondary {
  background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;
  border-radius: 9px; padding: 9px 18px; font-size: .875rem; font-weight: 600; cursor: pointer;
}
.btn-secondary:hover { background: #e5e7eb; }

/* Loading */
.loading-wrap {
  display: flex; align-items: center; justify-content: center; gap: .75rem;
  padding: 3rem 0; color: #6b7280; font-size: .875rem;
}
.spinner {
  width: 28px; height: 28px; border: 3px solid #e8ecf0;
  border-top-color: #3b82f6; border-radius: 50%; animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Empty */
.empty-state {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 3.5rem 1rem; text-align: center; color: #9ea9b8;
}
.empty-state svg { width: 56px; height: 56px; margin-bottom: 1rem; }
.empty-state p { font-size: 1.05rem; font-weight: 600; color: #374151; margin-bottom: .4rem; }
.empty-state small { font-size: .82rem; }

/* Table */
.table-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
.data-table th {
  background: #f8fafc; text-align: left; padding: .75rem 1rem;
  font-size: .75rem; font-weight: 700; color: #6b7280; text-transform: uppercase;
  letter-spacing: .04em; border-bottom: 1px solid #e8ecf0;
}
.data-table td { padding: .85rem 1rem; border-bottom: 1px solid #f0f3f7; color: #374151; }
.data-table tbody tr:hover { background: #f8fafc; }
.td-num   { color: #9ea9b8; width: 48px; text-align: center; }
.td-date  { color: #9ea9b8; font-size: .8rem; white-space: nowrap; }
.td-actions { width: 90px; }

.name-cell { display: flex; align-items: center; gap: .6rem; font-weight: 600; }
.avatar {
  width: 34px; height: 34px; border-radius: 50%;
  background: linear-gradient(135deg, #3b82f6, #6366f1);
  color: #fff; font-size: .75rem; font-weight: 700;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.username-code {
  background: #f3f4f6; border: 1px solid #e5e7eb;
  border-radius: 5px; padding: 2px 7px; font-size: .8rem; font-family: monospace;
}
.company-badge {
  background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;
  padding: 3px 10px; border-radius: 6px; font-size: .78rem; font-weight: 600;
}

.btn-icon {
  width: 32px; height: 32px; border-radius: 7px; border: 1px solid;
  display: inline-flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all .15s; margin-right: 4px;
}
.btn-icon svg { width: 14px; height: 14px; }
.btn-edit { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; }
.btn-edit:hover { background: #dbeafe; }
.btn-del  { background: #fef2f2; border-color: #fecaca; color: #ef4444; }
.btn-del:hover { background: #fee2e2; }

/* Modal */
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,.45);
  display: flex; align-items: center; justify-content: center;
  z-index: 2000; padding: 1rem;
}
.modal {
  background: #fff; border-radius: 16px; width: 100%; max-width: 480px;
  box-shadow: 0 20px 60px rgba(0,0,0,.2); animation: slideIn .2s ease;
}
@keyframes slideIn { from { transform: translateY(-16px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.modal-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1.25rem 1.5rem; border-bottom: 1px solid #f0f3f7;
}
.modal-header h3 { font-size: 1.05rem; font-weight: 700; color: #0f172a; }
.btn-close {
  background: none; border: none; font-size: 1.4rem; color: #9ea9b8;
  cursor: pointer; padding: 2px 6px; border-radius: 6px; line-height: 1;
}
.btn-close:hover { background: #f3f4f6; color: #374151; }

.modal-info {
  display: flex; align-items: flex-start; gap: .6rem;
  background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px;
  margin: 1rem 1.5rem 0; padding: .75rem 1rem;
  font-size: .82rem; color: #1d4ed8; line-height: 1.5;
}
.modal-info svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }

.modal-form { padding: 1.25rem 1.5rem 1.5rem; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-size: .82rem; font-weight: 600; color: #374151; margin-bottom: .4rem; }
.form-group input {
  width: 100%; padding: .6rem .85rem; border: 1px solid #d1d5db; border-radius: 8px;
  font-size: .875rem; color: #0f172a; outline: none; box-sizing: border-box;
  transition: border-color .15s;
}
.form-group input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
.req { color: #ef4444; }
.hint { font-weight: 400; color: #9ea9b8; font-size: .78rem; }

.input-pw { position: relative; }
.input-pw input { padding-right: 42px; }
.pw-toggle {
  position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer; color: #9ea9b8; padding: 2px;
}
.pw-toggle:hover { color: #374151; }
.pw-toggle svg { width: 16px; height: 16px; }

.modal-actions {
  display: flex; justify-content: flex-end; gap: .75rem; margin-top: 1.5rem;
}
.btn-spinner {
  display: inline-block; width: 14px; height: 14px;
  border: 2px solid rgba(255,255,255,.4); border-top-color: #fff;
  border-radius: 50%; animation: spin .6s linear infinite; margin-right: 4px;
}

@media (max-width: 600px) {
  .hero-banner { padding: 1.25rem; }
  .data-table th, .data-table td { padding: .6rem .75rem; }
  .td-date { display: none; }
}
</style>
