<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '@/services/api'
import FilterDropdown from '@/components/FilterDropdown.vue'

const logs = ref([])
const loading = ref(true)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)
const perPage = ref('10')
const responsePerPage = ref(10)

const filters = ref({
  search: '',
  module: '',
  action: '',
  tanggal_dari: '',
  tanggal_sampai: '',
})

const modules = ['auth', 'petani', 'transaksi-petani', 'penggilingan', 'user', 'sub-admin']
const actions = ['login', 'logout', 'create', 'update', 'delete', 'verify']

const logActiveFilterCount = computed(() => {
  let count = 0
  if (filters.value.module) count++
  if (filters.value.action) count++
  if (filters.value.tanggal_dari) count++
  if (filters.value.tanggal_sampai) count++
  return count
})

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
      responsePerPage.value = res.data.meta.per_page
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

const rowNumber = (index) => ((currentPage.value - 1) * (Number(responsePerPage.value) || 0)) + index + 1

const pageStart = computed(() => {
  if (!logs.value.length) return 0
  return (currentPage.value - 1) * (Number(responsePerPage.value) || 10) + 1
})

const pageEnd = computed(() => {
  if (!logs.value.length) return 0
  return Math.min(currentPage.value * (Number(responsePerPage.value) || 10), total.value)
})

watch(perPage, () => {
  currentPage.value = 1
  fetchLogs(1)
})

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
    auth: 'Auth',
    petani: 'Petani',
    'transaksi-petani': 'Transaksi Petani',
    penggilingan: 'Makloon',
    user: 'Akun',
    'sub-admin': 'Sub-Admin'
  }
  return map[mod] || mod
}

const roleLabel = (role) => {
  const map = { superadmin: 'Super Admin', admin: 'Admin', lapangan: 'Lapangan', penggilingan: 'Penggilingan' }
  return map[role] || role || '-'
}
</script>

<template>
  <div class="activity-log" style="padding: 20px;">

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

    <!-- Main Card Content -->
    <div class="page-card">
      <div class="toolbar">
        <div class="toolbar-left">
          <input
            v-model="filters.search"
            @keyup.enter="applyFilters"
            type="text"
            placeholder="Cari nama, username, deskripsi..."
            class="search-input"
          />
          <FilterDropdown
            :active-count="logActiveFilterCount"
            @apply="applyFilters"
            @reset="resetFilters"
          >
            <div class="fd-field">
              <label class="fd-label">Modul</label>
              <select v-model="filters.module" class="fd-select">
                <option value="">Semua Modul</option>
                <option v-for="m in modules" :key="m" :value="m">{{ moduleLabel(m) }}</option>
              </select>
            </div>
            <div class="fd-field">
              <label class="fd-label">Aksi</label>
              <select v-model="filters.action" class="fd-select">
                <option value="">Semua Aksi</option>
                <option v-for="a in actions" :key="a" :value="a">{{ actionLabel(a) }}</option>
              </select>
            </div>
            <div class="fd-field">
              <label class="fd-label">Tanggal Dari</label>
              <input v-model="filters.tanggal_dari" type="date" class="fd-input" />
            </div>
            <div class="fd-field">
              <label class="fd-label">Tanggal Sampai</label>
              <input v-model="filters.tanggal_sampai" type="date" class="fd-input" />
            </div>
          </FilterDropdown>
        </div>
        <div class="toolbar-right">
          <div class="result-info" v-if="!loading" style="font-size: 0.85rem; color: #64748b;">
            Menampilkan <strong>{{ logs.length }}</strong> dari <strong>{{ total }}</strong> entri
          </div>
        </div>
      </div>

      <!-- Data Table -->
      <div class="table-wrap">
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
              <td colspan="8" class="loading-cell">
                <div class="loading-wrap">
                  <div class="spinner"></div>
                  <span>Memuat log aktivitas...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="logs.length === 0">
              <td colspan="8" class="empty-cell" style="text-align: center; padding: 2rem 0; color: #9ea9b8;">Tidak ada data log aktivitas</td>
            </tr>
            <tr v-else v-for="(log, index) in logs" :key="log.id">
              <td class="td-num">{{ rowNumber(index) }}</td>
              <td class="td-date">{{ formatDateTime(log.created_at) }}</td>
              <td>
                <div class="user-cell">
                  <div class="font-semibold">{{ log.name || '-' }}</div>
                  <div style="font-size: 0.78rem; color: #64748b;">@{{ log.username || '-' }}</div>
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
              <td style="max-width: 320px; white-space: normal; line-height: 1.4; font-size: 0.82rem;">{{ log.description || '-' }}</td>
              <td style="font-family: monospace; font-size: 0.8rem; color: #64748b;">{{ log.ip_address || '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="lastPage > 1 || logs.length" class="pagination-bar">
        <div class="pagination-info">Menampilkan {{ pageStart }}-{{ pageEnd }} dari {{ total }} data</div>
        <div class="pagination-controls">
          <label for="log-per-page">Baris:</label>
          <select id="log-per-page" v-model="perPage" class="per-page-select">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <button @click="prevPage" :disabled="currentPage === 1" class="btn-page">&laquo;</button>
          <span class="page-label">{{ currentPage }} / {{ lastPage }}</span>
          <button @click="nextPage" :disabled="currentPage === lastPage" class="btn-page">&raquo;</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.activity-log {
  padding: 20px;
}

/* Hero Banner */
.hero-banner.slate {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  background: linear-gradient(135deg, #475569 0%, #1e293b 100%);
  border-radius: 16px;
  padding: 1.75rem 2rem;
  color: #fff;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 20px rgba(71, 85, 105, 0.15);
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
  max-width: 500px;
}
.search-input {
  width: 100%;
  padding: 0.55rem 0.9rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  outline: none;
  transition: border-color 0.15s;
}
.search-input:focus {
  border-color: #475569;
  box-shadow: 0 0 0 3px rgba(71, 85, 105, 0.12);
}

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
  vertical-align: middle;
}
.data-table tbody tr:hover {
  background: #f8fafc;
}

.td-num { color: #9ea9b8; width: 40px; text-align: center; }
.font-semibold { font-weight: 600; }
.td-date { color: #6b7280; font-size: 0.82rem; white-space: nowrap; }

/* Status Badges */
.badge {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 5px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  white-space: nowrap;
}
.badge-login  { background: #ecfdf5; color: #047857; }
.badge-logout { background: #fffbeb; color: #b45309; }
.badge-create { background: #eff6ff; color: #1d4ed8; }
.badge-update { background: #faf5ff; color: #7c3aed; }
.badge-delete { background: #fef2f2; color: #b91c1c; }
.badge-verify { background: #f0fdf4; color: #0f766e; }
.badge-default { background: #f1f5f9; color: #475569; }

.badge-role { font-weight: 500; }
.badge-role-superadmin { background: #fef9c3; color: #92400e; }
.badge-role-admin     { background: #dbeafe; color: #1d4ed8; }
.badge-role-lapangan  { background: #dcfce7; color: #15803d; }
.badge-role-penggilingan { background: #ede9fe; color: #6d28d9; }

.module-tag {
  display: inline-block;
  padding: 2px 8px;
  background: #f1f5f9;
  border-radius: 4px;
  font-size: 0.78rem;
  color: #475569;
  font-weight: 500;
}

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
  border-top-color: #475569;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 640px) {
  .hero-banner.slate { padding: 1.25rem; }
  .toolbar { flex-direction: column; align-items: stretch; }
  .td-date { display: none; }
}
</style>
