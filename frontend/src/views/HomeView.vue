<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const now = ref(new Date())
let clockTimer = null
onMounted(() => { clockTimer = setInterval(() => { now.value = new Date() }, 1000) })
onUnmounted(() => clearInterval(clockTimer))

const timeStr = computed(() => now.value.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }))
const dateStr = computed(() => now.value.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }))
const periodLabel = computed(() => now.value.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }))
const greeting = computed(() => {
  const h = now.value.getHours()
  if (h < 11) return 'Selamat Pagi'
  if (h < 15) return 'Selamat Siang'
  if (h < 18) return 'Selamat Sore'
  return 'Selamat Malam'
})
const greetingEmoji = computed(() => {
  const h = now.value.getHours()
  if (h < 11) return ''
  if (h < 15) return ''
  if (h < 18) return ''
  return ''
})

const petaniData = ref([])
const penggilinganData = ref([])
const usersData = ref([])
const subAdminData = ref([])
const loading = ref(true)

const fetchAll = async () => {
  loading.value = true
  const tasks = []
  if (authStore.canAccessPetani)       tasks.push(api.get('/petani').then(r => { petaniData.value = r.data.data || [] }))
  if (authStore.canAccessPenggilingan) tasks.push(api.get('/penggilingan').then(r => { penggilinganData.value = r.data.data || [] }))
  if (authStore.canAccessUsers)        tasks.push(api.get('/users').then(r => { usersData.value = r.data.data || [] }))
  if (authStore.canManageSubAdmins)    tasks.push(api.get('/my-sub-admins').then(r => { subAdminData.value = r.data.data || [] }))
  await Promise.allSettled(tasks)
  loading.value = false
}
onMounted(fetchAll)

const petaniTotal = computed(() => petaniData.value.length)
const petaniByKomoditi = computed(() => {
  const map = {}
  for (const p of petaniData.value) { const k = p.komoditi || 'Lainnya'; map[k] = (map[k] || 0) + 1 }
  return map
})
const petaniByStatus = computed(() => ({
  Disetujui: petaniData.value.filter(p => p.status_verifikasi === 'disetujui').length,
  Pending:   petaniData.value.filter(p => !p.status_verifikasi || p.status_verifikasi === 'pending').length,
  Ditolak:   petaniData.value.filter(p => p.status_verifikasi === 'ditolak').length,
}))
const penggilinganTotal = computed(() => penggilinganData.value.length)
const penggilinganUnikTotal = computed(() => new Set(penggilinganData.value.map(p => p.nama_penggilingan).filter(Boolean)).size)
const totalTonase = computed(() =>
  penggilinganData.value.reduce((s, p) => s + parseFloat(p.total_tonase || 0), 0)
    .toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
)
const totalAngkutan = computed(() =>
  penggilinganData.value.reduce((s, p) => s + parseInt(p.jumlah_angkutan || 0), 0)
)
const penggilinganByStatus = computed(() => ({
  Disetujui: penggilinganData.value.filter(p => p.status_verifikasi === 'disetujui').length,
  Pending:   penggilinganData.value.filter(p => !p.status_verifikasi || p.status_verifikasi === 'pending').length,
  Ditolak:   penggilinganData.value.filter(p => p.status_verifikasi === 'ditolak').length,
}))

const totalLahan = computed(() =>
  petaniData.value.reduce((s, p) => s + parseFloat(p.luas_lahan || 0), 0)
    .toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
)
const totalPotensiPanen = computed(() =>
  Math.round(petaniData.value.reduce((s, p) => s + parseFloat(p.potensi_panen || 0), 0))
)

const formatRibuan = (val) => Number(val).toLocaleString('id-ID')

const usersTotal = computed(() => usersData.value.length)

// Sub-admin stats: for superadmin derived from usersData; for penggilingan parent from /my-sub-admins
const subAdminTotal = computed(() => {
  if (authStore.canAccessUsers) return usersData.value.filter(u => u.parent_id).length
  return subAdminData.value.length
})
const subAdminAktif = computed(() => subAdminTotal.value)

const usersByRole = computed(() => {
  const map = {}
  for (const u of usersData.value) { map[u.role] = (map[u.role] || 0) + 1 }
  return map
})

function donutSegments(data, colors) {
  const total = Object.values(data).reduce((s, v) => s + v, 0)
  if (!total) return []
  let offset = 0
  const r = 45, circ = 2 * Math.PI * r
  return Object.entries(data).map(([label, value], i) => {
    const pct = value / total
    const dash = pct * circ
    const gap  = circ - dash
    const seg = { label, value, pct: (pct * 100).toFixed(1), color: colors[i % colors.length], dash, gap, offset, total }
    offset += dash
    return seg
  })
}

const komoditiColors = ['#3b82f6', '#10b981', '#f59e0b']
const statusColors   = ['#10b981', '#f59e0b', '#ef4444']
const roleColors     = ['#8b5cf6', '#10b981', '#f59e0b', '#3b82f6']
const gkpColors      = ['#10b981', '#f59e0b', '#ef4444']

const komoditiSegments = computed(() => donutSegments(petaniByKomoditi.value,       komoditiColors))
const statusSegments   = computed(() => donutSegments(petaniByStatus.value,         statusColors))
const roleSegments     = computed(() => donutSegments(usersByRole.value,            roleColors))
const gkpSegments      = computed(() => donutSegments(penggilinganByStatus.value,   gkpColors))

const roleLabel = computed(() => {
  if (authStore.isSuperAdmin)   return 'Super Admin'
  if (authStore.isAdmin)        return 'Admin'
  if (authStore.isLapangan)     return 'Admin Lapangan'
  if (authStore.isPenggilingan) return 'Penggilingan'
  return 'User'
})
const roleDesc = computed(() => {
  if (authStore.isSuperAdmin)   return 'Mengelola sistem pengadaan secara efisien'
  if (authStore.isAdmin)        return 'Mengelola dan memverifikasi data pengadaan'
  if (authStore.isLapangan)     return 'Menginput data petani dan pengadaan lapangan'
  if (authStore.isPenggilingan) return 'Mengelola data makloon & penggilingan'
  return ''
})
</script>

<template>
  <div class="home-view">

    <!-- ══════════════════ HERO BANNER ══════════════════ -->
    <section class="hero">
      <div class="hero-blur hero-blur--1"></div>
      <div class="hero-blur hero-blur--2"></div>
      <div class="hero-blur hero-blur--3"></div>
      <div class="hero-inner">
        <div class="hero-left">
          <div class="hero-badge"><span class="badge-dot"></span> Sistem Aktif</div>
          <h1 class="hero-greeting">{{ greeting }}, <span class="hero-name">{{ authStore.user?.name }}</span>! {{ greetingEmoji }}</h1>
          <p class="hero-role-tag">{{ roleLabel }} &mdash; {{ roleDesc }}</p>
          <div class="hero-chips">
            <span class="chip chip-white">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              {{ dateStr }}
            </span>
            <span class="chip chip-green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              {{ timeStr }}
            </span>
            <span class="chip chip-yellow">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              Periode {{ periodLabel }}
            </span>
          </div>
        </div>
        <div class="hero-right">
          <div class="hero-clock">{{ timeStr }}</div>
          <div class="hero-date">{{ dateStr }}</div>
        </div>
      </div>
    </section>

    <!-- ══════════════════ LOADING ══════════════════ -->
    <div v-if="loading" class="loading-wrap">
      <div class="loading-card">
        <div class="spinner"></div>
        <p>Memuat data...</p>
      </div>
    </div>

    <template v-else>

      <!-- ══════════════════ KPI CARDS ══════════════════ -->
      <div class="kpi-grid">

        <div v-if="authStore.canAccessPetani" class="kpi-card kpi-blue">
          <div class="kpi-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="kpi-body">
            <div class="kpi-label">Petani Terdaftar</div>
            <div class="kpi-value">{{ petaniTotal }}</div>
          </div>
          <div class="kpi-trend">
            <span class="kpi-badge kpi-badge-blue">Aktif</span>
          </div>
        </div>

        <div v-if="authStore.canAccessUsers || authStore.canManageSubAdmins" class="kpi-card kpi-green">
          <div class="kpi-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
          </div>
          <div class="kpi-body">
            <div class="kpi-label">Jumlah Sub Admin</div>
            <div class="kpi-value">{{ subAdminTotal }}</div>
          </div>
          <div class="kpi-trend">
            <span class="kpi-badge kpi-badge-green">Total</span>
          </div>
        </div>

        <div v-if="authStore.canAccessPetani" class="kpi-card kpi-indigo">
          <div class="kpi-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <div class="kpi-body">
            <div class="kpi-label">Total Lahan</div>
            <div class="kpi-value">{{ totalLahan }} <span class="kpi-unit">Ha</span></div>
          </div>
          <div class="kpi-trend">
            <span class="kpi-badge kpi-badge-indigo">Luas</span>
          </div>
        </div>

        <div v-if="authStore.canAccessPetani" class="kpi-card kpi-orange">
          <div class="kpi-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10"/><path d="M12 6v6l3 3"/></svg>
          </div>
          <div class="kpi-body">
            <div class="kpi-label">Potensi Panen</div>
            <div class="kpi-value">{{ formatRibuan(totalPotensiPanen) }} <span class="kpi-unit">KG</span></div>
          </div>
          <div class="kpi-trend">
            <span class="kpi-badge kpi-badge-orange">Estimasi</span>
          </div>
        </div>

        <div v-if="authStore.canAccessPenggilingan" class="kpi-card kpi-teal">
          <div class="kpi-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
          </div>
          <div class="kpi-body">
            <div class="kpi-label">Total Tonase GKP</div>
            <div class="kpi-value">{{ totalTonase }} <span class="kpi-unit">KG</span></div>
          </div>
          <div class="kpi-trend">
            <span class="kpi-badge kpi-badge-teal">Akumulasi</span>
          </div>
        </div>

        <div v-if="authStore.canAccessPenggilingan" class="kpi-card kpi-dark">
          <div class="kpi-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          </div>
          <div class="kpi-body">
            <div class="kpi-label">Total Angkutan</div>
            <div class="kpi-value">{{ totalAngkutan }}</div>
          </div>
          <div class="kpi-trend">
            <span class="kpi-badge kpi-badge-dark">Kendaraan</span>
          </div>
        </div>

        <div v-if="authStore.canAccessUsers" class="kpi-card kpi-purple">
          <div class="kpi-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          <div class="kpi-body">
            <div class="kpi-label">Total Pengguna</div>
            <div class="kpi-value">{{ usersTotal }}</div>
          </div>
          <div class="kpi-trend">
            <span class="kpi-badge kpi-badge-purple">Akun</span>
          </div>
        </div>

      </div>

      <!-- ══════════════════ DONUT CHARTS ══════════════════ -->
      <div v-if="authStore.canAccessPetani || authStore.canAccessPenggilingan" class="section-header">
        <div class="sh-line"></div>
        <h2 class="sh-title">Distribusi &amp; Verifikasi</h2>
        <div class="sh-line"></div>
      </div>

      <div class="chart-grid">

        <!-- Komoditi Petani -->
        <div v-if="authStore.canAccessPetani" class="chart-card">
          <div class="cc-top">
            <div class="cc-dot cc-dot-blue"></div>
            <div>
              <p class="cc-title">Distribusi Komoditi</p>
              <p class="cc-sub">{{ petaniTotal }} petani terdaftar</p>
            </div>
            <span class="cc-live"><span class="rtdot"></span> Live</span>
          </div>
          <div class="cc-body">
            <div class="donut-wrap">
              <svg viewBox="0 0 120 120" class="donut-svg">
                <circle cx="60" cy="60" r="42" fill="none" stroke="#f0f3f8" stroke-width="14"/>
                <circle v-for="(seg, i) in komoditiSegments" :key="i" cx="60" cy="60" r="42" fill="none"
                  :stroke="seg.color" stroke-width="14"
                  :stroke-dasharray="`${seg.dash} ${seg.gap}`"
                  :stroke-dashoffset="-seg.offset"
                  stroke-linecap="round"
                  style="transform:rotate(-90deg);transform-origin:60px 60px;transition:stroke-dasharray .6s ease"/>
                <text x="60" y="55" text-anchor="middle" class="donut-label">Total</text>
                <text x="60" y="70" text-anchor="middle" class="donut-value">{{ petaniTotal }}</text>
              </svg>
            </div>
            <div class="legend">
              <div v-for="(seg, i) in komoditiSegments" :key="i" class="legend-row">
                <span class="ldot" :style="{background:seg.color}"></span>
                <div class="ltext">
                  <span class="lname">{{ seg.label }}</span>
                  <div class="lbar-wrap"><div class="lbar" :style="{width:seg.pct+'%',background:seg.color}"></div></div>
                </div>
                <div class="lright">
                  <span class="lval">{{ seg.value }}</span>
                  <span class="lpct">{{ seg.pct }}%</span>
                </div>
              </div>
              <div v-if="!komoditiSegments.length" class="empty-legend">Belum ada data</div>
            </div>
          </div>
        </div>

        <!-- Status Verifikasi Petani -->
        <div v-if="authStore.canAccessPetani" class="chart-card">
          <div class="cc-top">
            <div class="cc-dot cc-dot-green"></div>
            <div>
              <p class="cc-title">Verifikasi Petani</p>
              <p class="cc-sub">{{ petaniTotal }} petani terdaftar</p>
            </div>
            <span class="cc-live"><span class="rtdot"></span> Live</span>
          </div>
          <div class="cc-body">
            <div class="donut-wrap">
              <svg viewBox="0 0 120 120" class="donut-svg">
                <circle cx="60" cy="60" r="42" fill="none" stroke="#f0f3f8" stroke-width="14"/>
                <circle v-for="(seg, i) in statusSegments" :key="i" cx="60" cy="60" r="42" fill="none"
                  :stroke="seg.color" stroke-width="14"
                  :stroke-dasharray="`${seg.dash} ${seg.gap}`"
                  :stroke-dashoffset="-seg.offset"
                  stroke-linecap="round"
                  style="transform:rotate(-90deg);transform-origin:60px 60px;transition:stroke-dasharray .6s ease"/>
                <text x="60" y="55" text-anchor="middle" class="donut-label">Total</text>
                <text x="60" y="70" text-anchor="middle" class="donut-value">{{ petaniTotal }}</text>
              </svg>
            </div>
            <div class="legend">
              <div v-for="(seg, i) in statusSegments" :key="i" class="legend-row">
                <span class="ldot" :style="{background:seg.color}"></span>
                <div class="ltext">
                  <span class="lname">{{ seg.label }}</span>
                  <div class="lbar-wrap"><div class="lbar" :style="{width:seg.pct+'%',background:seg.color}"></div></div>
                </div>
                <div class="lright">
                  <span class="lval">{{ seg.value }}</span>
                  <span class="lpct">{{ seg.pct }}%</span>
                </div>
              </div>
              <div v-if="!statusSegments.length" class="empty-legend">Belum ada data</div>
            </div>
          </div>
        </div>

        <!-- Status Verifikasi Makloon -->
        <div v-if="authStore.canAccessPenggilingan" class="chart-card">
          <div class="cc-top">
            <div class="cc-dot cc-dot-orange"></div>
            <div>
              <p class="cc-title">Verifikasi Makloon</p>
              <p class="cc-sub">{{ penggilinganTotal }} pengajuan</p>
            </div>
            <span class="cc-live"><span class="rtdot"></span> Live</span>
          </div>
          <div class="cc-body">
            <div class="donut-wrap">
              <svg viewBox="0 0 120 120" class="donut-svg">
                <circle cx="60" cy="60" r="42" fill="none" stroke="#f0f3f8" stroke-width="14"/>
                <circle v-for="(seg, i) in gkpSegments" :key="i" cx="60" cy="60" r="42" fill="none"
                  :stroke="seg.color" stroke-width="14"
                  :stroke-dasharray="`${seg.dash} ${seg.gap}`"
                  :stroke-dashoffset="-seg.offset"
                  stroke-linecap="round"
                  style="transform:rotate(-90deg);transform-origin:60px 60px;transition:stroke-dasharray .6s ease"/>
                <text x="60" y="55" text-anchor="middle" class="donut-label">Total</text>
                <text x="60" y="70" text-anchor="middle" class="donut-value">{{ penggilinganTotal }}</text>
              </svg>
            </div>
            <div class="legend">
              <div v-for="(seg, i) in gkpSegments" :key="i" class="legend-row">
                <span class="ldot" :style="{background:seg.color}"></span>
                <div class="ltext">
                  <span class="lname">{{ seg.label }}</span>
                  <div class="lbar-wrap"><div class="lbar" :style="{width:seg.pct+'%',background:seg.color}"></div></div>
                </div>
                <div class="lright">
                  <span class="lval">{{ seg.value }}</span>
                  <span class="lpct">{{ seg.pct }}%</span>
                </div>
              </div>
              <div v-if="!gkpSegments.length" class="empty-legend">Belum ada data</div>
            </div>
          </div>
        </div>

        <!-- Distribusi Role Pengguna -->
        <div v-if="authStore.canAccessUsers" class="chart-card">
          <div class="cc-top">
            <div class="cc-dot cc-dot-purple"></div>
            <div>
              <p class="cc-title">Distribusi Role</p>
              <p class="cc-sub">{{ usersTotal }} pengguna aktif</p>
            </div>
            <span class="cc-live"><span class="rtdot"></span> Live</span>
          </div>
          <div class="cc-body">
            <div class="donut-wrap">
              <svg viewBox="0 0 120 120" class="donut-svg">
                <circle cx="60" cy="60" r="42" fill="none" stroke="#f0f3f8" stroke-width="14"/>
                <circle v-for="(seg, i) in roleSegments" :key="i" cx="60" cy="60" r="42" fill="none"
                  :stroke="seg.color" stroke-width="14"
                  :stroke-dasharray="`${seg.dash} ${seg.gap}`"
                  :stroke-dashoffset="-seg.offset"
                  stroke-linecap="round"
                  style="transform:rotate(-90deg);transform-origin:60px 60px;transition:stroke-dasharray .6s ease"/>
                <text x="60" y="55" text-anchor="middle" class="donut-label">Total</text>
                <text x="60" y="70" text-anchor="middle" class="donut-value">{{ usersTotal }}</text>
              </svg>
            </div>
            <div class="legend">
              <div v-for="(seg, i) in roleSegments" :key="i" class="legend-row">
                <span class="ldot" :style="{background:seg.color}"></span>
                <div class="ltext">
                  <span class="lname">{{ seg.label }}</span>
                  <div class="lbar-wrap"><div class="lbar" :style="{width:seg.pct+'%',background:seg.color}"></div></div>
                </div>
                <div class="lright">
                  <span class="lval">{{ seg.value }}</span>
                  <span class="lpct">{{ seg.pct }}%</span>
                </div>
              </div>
              <div v-if="!roleSegments.length" class="empty-legend">Belum ada data</div>
            </div>
          </div>
        </div>

      </div>

      <!-- ══════════════════ FOOTER ══════════════════ -->
      <footer class="page-footer">
        <span class="rtdot"></span>
        Data real-time &bull; Periode <strong>{{ periodLabel }}</strong> &bull;
        &copy; 2025 Pengadaan Komoditas KC Surakarta
      </footer>

    </template>
  </div>
</template>

<style scoped>
/* ─── Base ─────────────────────────────────────────── */
.home-view { width: 100%; }

/* ─── Hero ──────────────────────────────────────────── */
.hero {
  position: relative; overflow: hidden;
  background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 45%, #2563eb 80%, #6366f1 100%);
  border-radius: 20px; margin-bottom: 1.75rem; color: #fff;
}
.hero-blur {
  position: absolute; border-radius: 50%; filter: blur(60px); pointer-events: none;
}
.hero-blur--1 { width: 300px; height: 300px; top: -120px; left: -80px; background: rgba(99,102,241,.35); }
.hero-blur--2 { width: 250px; height: 250px; bottom: -100px; right: -60px; background: rgba(16,185,129,.25); }
.hero-blur--3 { width: 200px; height: 200px; top: 50%; right: 20%; transform: translateY(-50%); background: rgba(59,130,246,.2); }

.hero-inner {
  position: relative; z-index: 1;
  display: flex; align-items: center; justify-content: space-between;
  padding: 2.5rem 2.5rem;
  gap: 2rem;
}
.hero-left { flex: 1; min-width: 0; }
.hero-right { flex-shrink: 0; text-align: right; }

.hero-badge {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,.13); border: 1px solid rgba(255,255,255,.25);
  border-radius: 99px; padding: 4px 14px; font-size: .78rem; font-weight: 600;
  margin-bottom: 1rem; backdrop-filter: blur(6px);
}
.badge-dot {
  width: 7px; height: 7px; border-radius: 50%; background: #4ade80;
  box-shadow: 0 0 0 3px rgba(74,222,128,.35); animation: pulse 2s infinite;
}
.hero-greeting {
  font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: 800;
  margin-bottom: .4rem; line-height: 1.2;
}
.hero-name { color: #93c5fd; }
.hero-role-tag { font-size: .85rem; opacity: .75; margin-bottom: 1rem; }
.hero-chips { display: flex; flex-wrap: wrap; gap: .5rem; }
.chip {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 12px; border-radius: 8px; font-size: .78rem; font-weight: 600;
}
.chip svg { width: 13px; height: 13px; }
.chip-white  { background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25); }
.chip-green  { background: rgba(16,185,129,.2);  border: 1px solid rgba(16,185,129,.35); }
.chip-yellow { background: rgba(245,158,11,.2);  border: 1px solid rgba(245,158,11,.35); }

.hero-clock {
  font-size: clamp(2rem, 4vw, 3rem); font-weight: 200; letter-spacing: 4px;
  font-variant-numeric: tabular-nums; opacity: .95;
}
.hero-date { font-size: .8rem; opacity: .6; margin-top: .25rem; }

/* ─── Loading ────────────────────────────────────────── */
.loading-wrap { display: flex; justify-content: center; padding: 4rem 0; }
.loading-card {
  display: flex; flex-direction: column; align-items: center; gap: 1rem;
  background: #fff; border-radius: 16px; padding: 2.5rem 3rem;
  box-shadow: 0 4px 24px rgba(0,0,0,.07); color: #6b7280; font-size: .9rem;
}
.spinner {
  width: 42px; height: 42px;
  border: 3px solid #e8ecf0; border-top-color: #2563eb;
  border-radius: 50%; animation: spin .75s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
@keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:.35; } }

/* ─── KPI Grid ───────────────────────────────────────── */
.kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1rem; margin-bottom: 1.75rem;
}
.kpi-card {
  display: flex; align-items: center; gap: 1rem;
  background: #fff; border-radius: 14px;
  padding: 1.2rem 1.3rem;
  border: 1px solid #e8ecf0;
  box-shadow: 0 2px 10px rgba(0,0,0,.04);
  transition: transform .2s ease, box-shadow .2s ease;
  position: relative; overflow: hidden;
}
.kpi-card::before {
  content: ''; position: absolute; top: 0; left: 0;
  width: 4px; height: 100%; border-radius: 4px 0 0 4px;
}
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }

.kpi-icon {
  flex-shrink: 0; width: 46px; height: 46px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
}
.kpi-icon svg { width: 22px; height: 22px; }
.kpi-body { flex: 1; min-width: 0; }
.kpi-label { font-size: .75rem; color: #6b7280; font-weight: 500; margin-bottom: .2rem; line-height: 1.3; }
.kpi-value { font-size: 1.55rem; font-weight: 800; color: #0f172a; line-height: 1; }
.kpi-unit  { font-size: .85rem; font-weight: 500; color: #9ea9b8; }
.kpi-trend { flex-shrink: 0; }
.kpi-badge { font-size: .65rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; white-space: nowrap; }

.kpi-blue::before   { background: #2563eb; }
.kpi-green::before  { background: #16a34a; }
.kpi-indigo::before { background: #4f46e5; }
.kpi-orange::before { background: #ea580c; }
.kpi-teal::before   { background: #0d9488; }
.kpi-dark::before   { background: #374151; }
.kpi-purple::before { background: #7c3aed; }
.kpi-rose::before   { background: #e11d48; }

.kpi-blue   .kpi-icon { background: #eff6ff; color: #2563eb; }
.kpi-green  .kpi-icon { background: #f0fdf4; color: #16a34a; }
.kpi-indigo .kpi-icon { background: #eef2ff; color: #4f46e5; }
.kpi-orange .kpi-icon { background: #fff7ed; color: #ea580c; }
.kpi-teal   .kpi-icon { background: #f0fdfa; color: #0d9488; }
.kpi-dark   .kpi-icon { background: #f1f5f9; color: #374151; }
.kpi-purple .kpi-icon { background: #f5f3ff; color: #7c3aed; }
.kpi-rose   .kpi-icon { background: #fff1f2; color: #e11d48; }

.kpi-badge-blue   { background: #dbeafe; color: #1d4ed8; }
.kpi-badge-green  { background: #dcfce7; color: #15803d; }
.kpi-badge-indigo { background: #e0e7ff; color: #3730a3; }
.kpi-badge-orange { background: #ffedd5; color: #c2410c; }
.kpi-badge-teal   { background: #ccfbf1; color: #0f766e; }
.kpi-badge-dark   { background: #e5e7eb; color: #374151; }
.kpi-badge-purple { background: #ede9fe; color: #6d28d9; }
.kpi-badge-rose   { background: #ffe4e6; color: #be123c; }

/* ─── Section Header ─────────────────────────────────── */
.section-header {
  display: flex; align-items: center; gap: 1rem;
  margin-bottom: 1.25rem;
}
.sh-title { font-size: 1rem; font-weight: 700; color: #6b7280; white-space: nowrap; }
.sh-line { flex: 1; height: 1px; background: #e8ecf0; }

/* ─── Chart Grid ─────────────────────────────────────── */
.chart-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem; margin-bottom: 1.75rem;
}
.chart-card {
  background: #fff; border-radius: 16px;
  border: 1px solid #e8ecf0; padding: 1.4rem;
  box-shadow: 0 2px 10px rgba(0,0,0,.04);
  transition: box-shadow .2s;
}
.chart-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.09); }

.cc-top {
  display: flex; align-items: center; gap: .75rem;
  margin-bottom: 1.2rem; padding-bottom: .9rem;
  border-bottom: 1px solid #f0f3f7;
}
.cc-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.cc-dot-blue   { background: #2563eb; box-shadow: 0 0 0 3px #dbeafe; }
.cc-dot-green  { background: #16a34a; box-shadow: 0 0 0 3px #dcfce7; }
.cc-dot-orange { background: #ea580c; box-shadow: 0 0 0 3px #ffedd5; }
.cc-dot-purple { background: #7c3aed; box-shadow: 0 0 0 3px #ede9fe; }
.cc-title { font-size: .95rem; font-weight: 700; color: #0f172a; }
.cc-sub   { font-size: .75rem; color: #9ea9b8; margin-top: 1px; }
.cc-live  {
  margin-left: auto; display: flex; align-items: center; gap: 5px;
  font-size: .72rem; color: #6b7280; background: #f9fafb;
  padding: 3px 9px; border-radius: 99px; border: 1px solid #e8ecf0;
}

.cc-body { display: flex; align-items: center; gap: 1.25rem; }
.donut-wrap { flex-shrink: 0; width: 110px; }
.donut-svg  { width: 100%; height: auto; overflow: visible; }
.donut-label { font-size: 8px; fill: #9ea9b8; font-weight: 500; }
.donut-value { font-size: 15px; fill: #0f172a; font-weight: 800; }

.legend { flex: 1; display: flex; flex-direction: column; gap: .65rem; min-width: 0; }
.legend-row { display: flex; align-items: center; gap: 8px; }
.ldot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.ltext { flex: 1; min-width: 0; }
.lname { display: block; font-size: .78rem; font-weight: 600; color: #1a2332; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.lbar-wrap { margin-top: 3px; height: 4px; background: #f0f3f7; border-radius: 99px; overflow: hidden; }
.lbar { height: 100%; border-radius: 99px; transition: width .6s ease; }
.lright { flex-shrink: 0; text-align: right; }
.lval { display: block; font-size: .88rem; font-weight: 800; color: #0f172a; line-height: 1; }
.lpct { display: block; font-size: .68rem; color: #9ea9b8; margin-top: 1px; }
.empty-legend { font-size: .8rem; color: #9ea9b8; text-align: center; padding: .5rem 0; }

.rtdot {
  width: 7px; height: 7px; border-radius: 50%; background: #10b981;
  display: inline-block; animation: pulse 2s infinite;
  box-shadow: 0 0 0 2px rgba(16,185,129,.25);
}

/* ─── Footer ─────────────────────────────────────────── */
.page-footer {
  display: flex; align-items: center; justify-content: center; gap: .5rem;
  padding: 1.25rem 0 .25rem;
  font-size: .78rem; color: #9ea9b8;
}
.page-footer strong { color: #6b7280; }

/* ─── Responsive ─────────────────────────────────────── */
@media (max-width: 900px) {
  .hero-right { display: none; }
}
@media (max-width: 768px) {
  .hero-inner { padding: 1.75rem 1.5rem; }
  .hero-greeting { font-size: 1.4rem; }
  .kpi-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
  .chart-grid { grid-template-columns: 1fr; }
}
@media (max-width: 480px) {
  .hero-inner { padding: 1.4rem 1.1rem; }
  .hero-greeting { font-size: 1.2rem; }
  .kpi-grid { grid-template-columns: 1fr 1fr; }
  .kpi-value { font-size: 1.3rem; }
  .chip-yellow { display: none; }
}
</style>