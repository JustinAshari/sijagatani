<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const logs = ref([])
const loading = ref(true)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)
const perPage = ref(50)

const filters = ref({
  search: '',
  module: '',
  action: '',
  tanggal_dari: '',
  tanggal_sampai: '',
})

const modules = ['auth', 'petani', 'penggilingan', 'user', 'sub-admin']
const actions = ['login', 'logout', 'create', 'update', 'delete', 'verify']

const fetchLogs = async (page = 1) => {
  loading.value = true
  try {
    const params = { page, per_page: perPage.value }
    if (filters.value.search)        params.search = filters.value.search
    if (filters.value.module)        params.module = filters.value.module
    if (filters.value.action)        params.action = filters.value.action
    if (filters.value.tanggal_dari)  params.tanggal_dari = filters.value.tanggal_dari
    if (filters.value.tanggal_sampai) params.tanggal_sampai = filters.value.tanggal_sampai

    const res = await api.get('/activity-logs', { params })
    if (res.data.success) {
      logs.value = res.data.data
      currentPage.value = res.data.meta.current_page
      lastPage.value = res.data.meta.last_page
      total.value = res.data.meta.total
    }
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  currentPage.value = 1
  fetchLogs(1)
}

const resetFilters = () => {
  filters.value = { search: '', module: '', action: '', tanggal_dari: '', tanggal_sampai: '' }
  fetchLogs(1)
}

const prevPage = () => { if (currentPage.value > 1) fetchLogs(currentPage.value - 1) }
const nextPage = () => { if (currentPage.value < lastPage.value) fetchLogs(currentPage.value + 1) }

onMounted(() => fetchLogs(1))

const formatDateTime = (dt) => {
  if (!dt) return '-'
  return new Date(dt).toLocaleString('id-ID', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit', second: '2-digit'
  })
}

const actionLabel = (action) => {
  const map = {
    login: 'Login', logout: 'Logout', create: 'Tambah',
    update: 'Ubah', delete: 'Hapus', verify: 'Verifikasi'
  }
  return map[action] || action
}

const actionClass = (action) => {
  const map = {
    login: 'badge-login', logout: 'badge-logout', create: 'badge-create',
    update: 'badge-update', delete: 'badge-delete', verify: 'badge-verify'
  }
  return map[action] || 'badge-default'
}

const moduleLabel = (mod) => {
  const map = {
    auth: 'Auth', petani: 'Petani', penggilingan: 'Makloon',
    user: 'Akun', 'sub-admin': 'Sub-Admin'
  }
  return map[mod] || mod
}

const roleLabel = (role) => {
  const map = { superadmin: 'Super Admin', admin: 'Admin', lapangan: 'Lapangan', penggilingan: 'Penggilingan' }
  return map[role] || role || '-'
}
</script>

<template>
  <div class="activity-log">
    <div class="page-card">

      <!-- Hero Banner -->
      <div class="hero-banner slate">
        <div class="hero-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <line x1="10" y1="9" x2="8" y2="9"/>
          </svg>
        </div>
        <div class="hero-text">
          <h2>Log Aktivitas</h2>
          <p>Riwayat aktivitas seluruh pengguna dalam sistem</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="filters-bar">
        <div class="filter-row">
          <input
            v-model="filters.search"
            @keyup.enter="applyFilters"
            type="text"
            placeholder="Cari nama, username, deskripsi..."
            class="filter-input search-input"
          />
          <select v-model="filters.module" class="filter-select">
            <option value="">Semua Modul</option>
            <option v-for="m in modules" :key="m" :value="m">{{ moduleLabel(m) }}</option>
          </select>
          <select v-model="filters.action" class="filter-select">
            <option value="">Semua Aksi</option>
            <option v-for="a in actions" :key="a" :value="a">{{ actionLabel(a) }}</option>
          </select>
          <input v-model="filters.tanggal_dari" type="date" class="filter-input" title="Dari tanggal" />
          <input v-model="filters.tanggal_sampai" type="date" class="filter-input" title="Sampai tanggal" />
          <button @click="applyFilters" class="btn-filter">Cari</button>
          <button @click="resetFilters" class="btn-reset">Reset</button>
        </div>
        <div class="result-info" v-if="!loading">
          Menampilkan <strong>{{ logs.length }}</strong> dari <strong>{{ total }}</strong> entri
        </div>
      </div>

      <!-- Table -->
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Waktu</th>
              <th>Pengguna</th>
              <th>Role</th>
              <th>Aksi</th>
              <th>Modul</th>
              <th>Keterangan</th>
              <th>IP</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="8" class="loading-cell">Loading...</td>
            </tr>
            <tr v-else-if="logs.length === 0">
              <td colspan="8" class="empty-cell">Tidak ada data log</td>
            </tr>
            <tr v-else v-for="(log, index) in logs" :key="log.id">
              <td>{{ (currentPage - 1) * perPage + index + 1 }}</td>
              <td class="cell-datetime">{{ formatDateTime(log.created_at) }}</td>
              <td>
                <div class="user-cell">
                  <span class="user-name">{{ log.name || '-' }}</span>
                  <span class="user-username">@{{ log.username || '-' }}</span>
                </div>
              </td>
              <td>
                <span class="badge badge-role" :class="'badge-role-' + log.role">
                  {{ roleLabel(log.role) }}
                </span>
              </td>
              <td>
                <span class="badge" :class="actionClass(log.action)">
                  {{ actionLabel(log.action) }}
                </span>
              </td>
              <td>
                <span class="module-tag">{{ moduleLabel(log.module) }}</span>
              </td>
              <td class="cell-description">{{ log.description || '-' }}</td>
              <td class="cell-ip">{{ log.ip_address || '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="lastPage > 1" class="pagination">
        <button @click="prevPage" :disabled="currentPage === 1" class="btn-page">&laquo; Prev</button>
        <span class="page-info">Halaman {{ currentPage }} dari {{ lastPage }}</span>
        <button @click="nextPage" :disabled="currentPage === lastPage" class="btn-page">Next &raquo;</button>
      </div>

    </div>
  </div>
</template>

<style scoped>
.activity-log { padding: 20px; }
.page-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); overflow: hidden; }

/* Hero Banner */
.hero-banner {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem 2rem;
  color: #fff;
}
.hero-banner.slate { background: linear-gradient(135deg, #475569 0%, #1e293b 100%); }
.hero-icon {
  width: 52px; height: 52px; background: rgba(255,255,255,0.2);
  border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.hero-icon svg { width: 26px; height: 26px; }
.hero-text h2 { margin: 0 0 0.2rem; font-size: 1.3rem; font-weight: 700; }
.hero-text p  { margin: 0; opacity: 0.85; font-size: 0.875rem; }

/* Filters */
.filters-bar { padding: 1.2rem 1.5rem; border-bottom: 1px solid #e8ecf0; background: #f8fafc; }
.filter-row { display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center; }
.filter-input, .filter-select {
  height: 36px; border: 1px solid #d1d5db; border-radius: 6px;
  padding: 0 0.75rem; font-size: 0.875rem; background: #fff; outline: none;
}
.filter-input:focus, .filter-select:focus { border-color: #475569; }
.search-input { min-width: 220px; flex: 1; }
.btn-filter {
  height: 36px; padding: 0 1rem; background: #475569; color: #fff;
  border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem; font-weight: 500;
}
.btn-filter:hover { background: #334155; }
.btn-reset {
  height: 36px; padding: 0 1rem; background: #e2e8f0; color: #475569;
  border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem;
}
.btn-reset:hover { background: #cbd5e1; }
.result-info { margin-top: 0.5rem; font-size: 0.8rem; color: #64748b; }

/* Table */
.table-container { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.data-table th {
  background: #f1f5f9; padding: 0.75rem 1rem; text-align: left;
  font-weight: 600; color: #374151; border-bottom: 2px solid #e2e8f0; white-space: nowrap;
}
.data-table td { padding: 0.65rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.data-table tr:hover td { background: #f8fafc; }
.loading-cell,
.empty-cell {
  text-align: center;
  padding: 40px;
  color: #7f8c8d;
}

/* Cell types */
.cell-datetime { white-space: nowrap; font-size: 0.8rem; color: #475569; }
.cell-description { max-width: 280px; color: #374151; }
.cell-ip { font-size: 0.78rem; color: #94a3b8; font-family: monospace; white-space: nowrap; }
.user-cell { display: flex; flex-direction: column; gap: 1px; }
.user-name { font-weight: 600; color: #1e293b; }
.user-username { font-size: 0.78rem; color: #94a3b8; }
.module-tag {
  display: inline-block; padding: 2px 8px; background: #f1f5f9;
  border-radius: 4px; font-size: 0.78rem; color: #475569; font-weight: 500;
}

/* Badges */
.badge {
  display: inline-block; padding: 3px 10px; border-radius: 20px;
  font-size: 0.75rem; font-weight: 600; white-space: nowrap;
}
.badge-login  { background: #dcfce7; color: #15803d; }
.badge-logout { background: #fef3c7; color: #92400e; }
.badge-create { background: #dbeafe; color: #1d4ed8; }
.badge-update { background: #ede9fe; color: #6d28d9; }
.badge-delete { background: #fee2e2; color: #b91c1c; }
.badge-verify { background: #ccfbf1; color: #0f766e; }
.badge-default { background: #f1f5f9; color: #475569; }

.badge-role { font-weight: 500; }
.badge-role-superadmin { background: #fef9c3; color: #92400e; }
.badge-role-admin     { background: #dbeafe; color: #1d4ed8; }
.badge-role-lapangan  { background: #dcfce7; color: #15803d; }
.badge-role-penggilingan { background: #ede9fe; color: #6d28d9; }

/* Pagination */
.pagination {
  display: flex; align-items: center; justify-content: center;
  gap: 1rem; padding: 1.2rem; border-top: 1px solid #e8ecf0;
}
.btn-page {
  padding: 6px 14px; border: 1px solid #d1d5db; border-radius: 6px;
  background: #fff; cursor: pointer; font-size: 0.875rem;
}
.btn-page:disabled { opacity: 0.4; cursor: not-allowed; }
.btn-page:not(:disabled):hover { background: #f1f5f9; }
.page-info { font-size: 0.875rem; color: #64748b; }
</style>
