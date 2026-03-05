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
const loading = ref(true)

const fetchAll = async () => {
  loading.value = true
  const tasks = []
  if (authStore.canAccessPetani)       tasks.push(api.get('/petani').then(r => { petaniData.value = r.data.data || [] }))
  if (authStore.canAccessPenggilingan) tasks.push(api.get('/penggilingan').then(r => { penggilinganData.value = r.data.data || [] }))
  if (authStore.canAccessUsers)        tasks.push(api.get('/users').then(r => { usersData.value = r.data.data || [] }))
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

    <section class="hero">
      <div class="hero-blob hero-blob--1"></div>
      <div class="hero-blob hero-blob--2"></div>
      <div class="hero-badge"><span class="badge-dot"></span> Dashboard Active</div>
      <h1 class="hero-greeting">{{ greeting }}, {{ authStore.user?.name }}! {{ greetingEmoji }}</h1>
      <p class="hero-time">{{ timeStr }}</p>
      <div class="hero-meta">
        <span class="hero-meta-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          Sistem Aktif
        </span>
        <span class="hero-meta-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          {{ dateStr }}
        </span>
      </div>
    </section>

    <div v-if="loading" class="loading-wrap"><div class="spinner"></div></div>

    <template v-else>

      <section class="section">
        <div class="section-head">
          <h2 class="section-title">Ringkasan Dashboard</h2>
          <p class="section-sub">Overview data utama sistem pengadaan untuk monitoring dan analisis performa</p>
          <div class="periode-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Periode: <strong>{{ periodLabel }}</strong>
          </div>
        </div>

        <div class="chart-grid">
          <div v-if="authStore.canAccessPetani" class="chart-card">
            <div class="chart-card__header">
              <p class="chart-card__label">Distribusi Komoditi Petani</p>
              <p class="chart-card__sub">Total: {{ petaniTotal }} petani</p>
            </div>
            <div class="chart-body">
              <div class="donut-wrap">
                <svg viewBox="0 0 120 120" class="donut-svg">
                  <circle cx="60" cy="60" r="45" fill="none" stroke="#f0f3f8" stroke-width="16"/>
                  <circle v-for="(seg, i) in komoditiSegments" :key="i" cx="60" cy="60" r="45" fill="none"
                    :stroke="seg.color" stroke-width="16"
                    :stroke-dasharray="`${seg.dash} ${seg.gap}`"
                    :stroke-dashoffset="-seg.offset"
                    style="transform:rotate(-90deg);transform-origin:60px 60px"/>
                  <text x="60" y="56" text-anchor="middle" class="donut-top">Total</text>
                  <text x="60" y="70" text-anchor="middle" class="donut-num">{{ petaniTotal }}</text>
                </svg>
              </div>
              <div class="legend">
                <div v-for="(seg, i) in komoditiSegments" :key="i" class="legend-row">
                  <span class="ldot" :style="{background:seg.color}"></span>
                  <div class="ltext">
                    <span class="lname">{{ seg.label }}</span>
                    <span class="lpct">{{ seg.pct }}% dari total</span>
                  </div>
                  <span class="lval">{{ seg.value }}<br><small>petani</small></span>
                </div>
              </div>
            </div>
            <div class="chart-foot"><span class="rtdot"></span> Real-time</div>
          </div>

          <div v-if="authStore.canAccessPetani" class="chart-card">
            <div class="chart-card__header">
              <p class="chart-card__label">Status Verifikasi Petani</p>
              <p class="chart-card__sub">Total: {{ petaniTotal }} petani</p>
            </div>
            <div class="chart-body">
              <div class="donut-wrap">
                <svg viewBox="0 0 120 120" class="donut-svg">
                  <circle cx="60" cy="60" r="45" fill="none" stroke="#f0f3f8" stroke-width="16"/>
                  <circle v-for="(seg, i) in statusSegments" :key="i" cx="60" cy="60" r="45" fill="none"
                    :stroke="seg.color" stroke-width="16"
                    :stroke-dasharray="`${seg.dash} ${seg.gap}`"
                    :stroke-dashoffset="-seg.offset"
                    style="transform:rotate(-90deg);transform-origin:60px 60px"/>
                  <text x="60" y="56" text-anchor="middle" class="donut-top">Total</text>
                  <text x="60" y="70" text-anchor="middle" class="donut-num">{{ petaniTotal }}</text>
                </svg>
              </div>
              <div class="legend">
                <div v-for="(seg, i) in statusSegments" :key="i" class="legend-row">
                  <span class="ldot" :style="{background:seg.color}"></span>
                  <div class="ltext">
                    <span class="lname">{{ seg.label }}</span>
                    <span class="lpct">{{ seg.pct }}% dari total</span>
                  </div>
                  <span class="lval">{{ seg.value }}<br><small>petani</small></span>
                </div>
              </div>
            </div>
            <div class="chart-foot"><span class="rtdot"></span> Real-time</div>
          </div>

          <div v-if="authStore.canAccessPenggilingan" class="chart-card">
            <div class="chart-card__header">
              <p class="chart-card__label">Status Verifikasi Makloon</p>
              <p class="chart-card__sub">Total: {{ penggilinganTotal }} pengajuan</p>
            </div>
            <div class="chart-body">
              <div class="donut-wrap">
                <svg viewBox="0 0 120 120" class="donut-svg">
                  <circle cx="60" cy="60" r="45" fill="none" stroke="#f0f3f8" stroke-width="16"/>
                  <circle v-for="(seg, i) in gkpSegments" :key="i" cx="60" cy="60" r="45" fill="none"
                    :stroke="seg.color" stroke-width="16"
                    :stroke-dasharray="`${seg.dash} ${seg.gap}`"
                    :stroke-dashoffset="-seg.offset"
                    style="transform:rotate(-90deg);transform-origin:60px 60px"/>
                  <text x="60" y="56" text-anchor="middle" class="donut-top">Total</text>
                  <text x="60" y="70" text-anchor="middle" class="donut-num">{{ penggilinganTotal }}</text>
                </svg>
              </div>
              <div class="legend">
                <div v-for="(seg, i) in gkpSegments" :key="i" class="legend-row">
                  <span class="ldot" :style="{background:seg.color}"></span>
                  <div class="ltext">
                    <span class="lname">{{ seg.label }}</span>
                    <span class="lpct">{{ seg.pct }}% dari total</span>
                  </div>
                  <span class="lval">{{ seg.value }}<br><small>GKP</small></span>
                </div>
              </div>
            </div>
            <div class="chart-foot"><span class="rtdot"></span> Real-time</div>
          </div>
        </div>

        <p v-if="authStore.canAccessPetani || authStore.canAccessUsers" class="note">
          <span v-if="authStore.canAccessPetani">Data petani ditampilkan untuk bulan <strong>{{ periodLabel }}</strong></span>
          <span v-if="authStore.canAccessPetani && authStore.canAccessUsers"> &bull; </span>
          <span v-if="authStore.canAccessUsers">Data pengguna menampilkan total keseluruhan</span>
        </p>
      </section>

      <section class="section">
        <div class="section-head">
          <h2 class="section-title">Ringkasan Statistik</h2>
          <p class="section-sub">Data terkini sistem pengadaan dan manajemen pengguna</p>
        </div>
        <div class="stat-grid">
          <!-- Petani: total lahan & potensi panen -->
          <div v-if="authStore.canAccessPetani" class="stat-card sindigo">
            <div class="sbg-dots"></div>
            <div class="sicon sicon-indigo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
            <div class="snum">{{ totalLahan }} <span class="sunit">Ha</span></div>
            <div class="slabel">Total Lahan</div>
            <div class="ssub">Luas lahan terdaftar</div>
          </div>
          <div v-if="authStore.canAccessPetani" class="stat-card sorange">
            <div class="sbg-dots"></div>
            <div class="sicon sicon-orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10"/><path d="M12 6v6l3 3"/><path d="M22 2L12 12"/></svg></div>
            <div class="snum">{{ formatRibuan(totalPotensiPanen) }} <span class="sunit">KG</span></div>
            <div class="slabel">Potensi Panen</div>
            <div class="ssub">Total estimasi panen</div>
          </div>
          <!-- Penggilingan stats -->
          <div v-if="authStore.canAccessPenggilingan" class="stat-card steal">
            <div class="sbg-dots"></div>
            <div class="sicon sicon-teal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg></div>
            <div class="snum">{{ totalTonase }} <span class="sunit">KG</span></div>
            <div class="slabel">Total Tonase GKP</div>
            <div class="ssub">Akumulasi tonase makloon</div>
          </div>
          <div v-if="authStore.canAccessPenggilingan" class="stat-card sdark">
            <div class="sbg-dots"></div>
            <div class="sicon sicon-dark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
            <div class="snum">{{ totalAngkutan }}</div>
            <div class="slabel">Total Angkutan</div>
            <div class="ssub">Jumlah kendaraan makloon</div>
          </div>
        </div>
      </section>

      <section class="section">
        <div class="role-card">
          <div class="role-avatars">
            <span class="ravatar rav-blue"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg></span>
            <span class="ravatar rav-green"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg></span>
            <span class="ravatar rav-purple"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg></span>
          </div>
          <h3 class="role-title">{{ roleLabel }}</h3>
          <p class="role-desc">{{ roleDesc }}</p>
        </div>
      </section>

      <footer class="page-footer">Copyright &copy; 2025 Pengadaan Komoditas Kantor Cabang Surakarta MasbeID</footer>
    </template>
  </div>
</template>

<style scoped>
.home-view { width: 100%; }

.hero {
  position: relative; overflow: hidden;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 55%, #6366f1 100%);
  border-radius: 16px; text-align: center;
  padding: 3rem 2rem 2.5rem; margin-bottom: 2rem; color: #fff;
}
.hero-blob { position: absolute; border-radius: 50%; opacity: 0.12; background: #fff; }
.hero-blob--1 { width: 200px; height: 200px; top: -60px; left: -60px; }
.hero-blob--2 { width: 160px; height: 160px; bottom: -50px; right: -40px; }
.hero-badge {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);
  border-radius: 99px; padding: 5px 14px; font-size: .82rem; font-weight: 600;
  margin-bottom: 1.2rem; backdrop-filter: blur(4px);
}
.badge-dot {
  width: 8px; height: 8px; border-radius: 50%; background: #4ade80;
  box-shadow: 0 0 0 3px rgba(74,222,128,.3);
}
.hero-greeting { font-size: 2.4rem; font-weight: 800; margin-bottom: .6rem; text-shadow: 0 2px 8px rgba(0,0,0,.15); }
.hero-time { font-size: 1.5rem; font-weight: 300; letter-spacing: 3px; opacity: .9; margin-bottom: 1.2rem; font-variant-numeric: tabular-nums; }
.hero-meta { display: inline-flex; gap: 1.5rem; }
.hero-meta-item { display: flex; align-items: center; gap: 5px; font-size: .85rem; opacity: .85; }
.hero-meta-item svg { width: 15px; height: 15px; }

.loading-wrap { display: flex; justify-content: center; padding: 4rem 0; }
.spinner { width: 40px; height: 40px; border: 3px solid #e8ecf0; border-top-color: #3b82f6; border-radius: 50%; animation: spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.section { margin-bottom: 2rem; }
.section-head { text-align: center; margin-bottom: 1.5rem; }
.section-title { font-size: 1.6rem; font-weight: 800; color: #1a2332; margin-bottom: .4rem; }
.section-sub { color: #6b7280; font-size: .9rem; margin-bottom: .8rem; }
.periode-badge { display: inline-flex; align-items: center; gap: 6px; font-size: .85rem; color: #374151; }
.periode-badge strong { background: #2563eb; color: #fff; padding: 3px 12px; border-radius: 99px; font-size: .82rem; margin-left: 4px; }
.periode-badge svg { width: 14px; height: 14px; }

.chart-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; }
.chart-card { background: #fff; border-radius: 14px; border: 1px solid #e8ecf0; padding: 1.4rem; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
.chart-card__header { margin-bottom: 1rem; }
.chart-card__label { font-size: 1rem; font-weight: 700; color: #1a2332; }
.chart-card__sub { font-size: .8rem; color: #9ea9b8; margin-top: 2px; }
.chart-body { display: flex; align-items: center; gap: 1.25rem; }
.donut-wrap { flex-shrink: 0; width: 120px; }
.donut-svg { width: 100%; height: auto; overflow: visible; }
.donut-top { font-size: 9px; fill: #9ea9b8; font-weight: 500; }
.donut-num { font-size: 16px; fill: #1a2332; font-weight: 800; }
.legend { flex: 1; display: flex; flex-direction: column; gap: .55rem; }
.legend-row { display: flex; align-items: center; gap: 8px; }
.ldot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.ltext { flex: 1; }
.lname { display: block; font-size: .82rem; font-weight: 600; color: #1a2332; }
.lpct  { display: block; font-size: .72rem; color: #9ea9b8; margin-top: 1px; }
.lval  { font-size: .95rem; font-weight: 700; color: #1a2332; text-align: right; line-height: 1.2; white-space: nowrap; }
.lval small { font-size: .65rem; color: #9ea9b8; font-weight: 400; }
.chart-foot { display: flex; align-items: center; gap: 5px; justify-content: flex-end; margin-top: 1rem; padding-top: .75rem; border-top: 1px solid #f0f3f7; font-size: .75rem; color: #9ea9b8; }
.rtdot { width: 7px; height: 7px; border-radius: 50%; background: #10b981; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:.4; } }
.note { text-align: center; font-size: .8rem; color: #9ea9b8; margin-top: 1rem; }

.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; }
.stat-card { position: relative; overflow: hidden; background: #fff; border-radius: 14px; padding: 1.4rem 1.4rem 1.2rem; border: 1px solid #e8ecf0; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
.sbg-dots { position:absolute;top:0;right:0;bottom:0;left:0; background-image: radial-gradient(circle,rgba(0,0,0,.04) 1px,transparent 1px); background-size:18px 18px; pointer-events:none; }
.slive { position:absolute;top:12px;right:12px;width:8px;height:8px;border-radius:50%; }
.slive-green { background:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,.2); }
.slive-red   { background:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,.2); }
.sicon { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:1rem; }
.sicon svg { width:22px;height:22px; }
.sicon-blue  { background:#eff6ff;color:#2563eb; }
.sicon-dark  { background:#1f2937;color:#fff; }
.sicon-green { background:#ecfdf5;color:#10b981; }
.sicon-red   { background:#fef2f2;color:#ef4444; }
.sblue  { background:#eff6ff;border-color:#bfdbfe; }
.sdark  { background:#f9fafb;border-color:#e5e7eb; }
.sgreen { background:#ecfdf5;border-color:#a7f3d0; }
.sred    { background:#fef2f2;border-color:#fecaca; }
.sindigo { background:#eef2ff;border-color:#c7d2fe; }
.sorange { background:#fff7ed;border-color:#fed7aa; }
.steal   { background:#f0fdfa;border-color:#99f6e4; }
.sicon-indigo { background:#e0e7ff;color:#4f46e5; }
.sicon-orange { background:#ffedd5;color:#ea580c; }
.sicon-teal   { background:#ccfbf1;color:#0d9488; }
.snum   { font-size:2rem;font-weight:800;color:#1a2332;line-height:1;margin-bottom:.3rem; }
.sunit  { font-size:1rem;font-weight:600;color:#6b7280; }
.slabel { font-size:.9rem;font-weight:600;color:#374151;margin-bottom:.2rem; }
.ssub   { font-size:.75rem;color:#9ea9b8; }

.role-card { background:#fff;border:1px solid #e8ecf0;border-radius:14px;padding:2rem;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.05); }
.role-avatars { display:flex;justify-content:center;margin-bottom:1rem; }
.ravatar { width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:2px solid #fff;margin:0 -4px; }
.ravatar svg { width:22px;height:22px; }
.rav-blue   { background:#3b82f6;color:#fff; }
.rav-green  { background:#10b981;color:#fff; }
.rav-purple { background:#8b5cf6;color:#fff; }
.role-title { font-size:1.15rem;font-weight:700;color:#1a2332;margin-bottom:.4rem; }
.role-desc  { font-size:.875rem;color:#6b7280; }

.page-footer { text-align:center;padding:1.5rem 0 .5rem;font-size:.78rem;color:#9ea9b8; }

@media (max-width: 768px) {
  .hero { padding:2rem 1rem 1.8rem; }
  .hero-greeting { font-size:1.6rem; }
  .hero-time { font-size:1.1rem; }
  .chart-grid { grid-template-columns:1fr; }
  .stat-grid  { grid-template-columns:1fr 1fr; }
}
@media (max-width: 480px) {
  .stat-grid { grid-template-columns:1fr; }
  .hero-greeting { font-size:1.3rem; }
  .hero-meta { flex-direction:column;gap:.5rem; }
}
</style>