<template>
  <div class="home-view">
    <div class="hero-section">
      <h1>
        <svg class="icon-inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M2 12h20"/>
          <path d="M6 8v8"/>
          <path d="M10 8v8"/>
          <path d="M14 8v8"/>
          <path d="M18 8v8"/>
          <path d="M2 16h20"/>
          <path d="M2 8h20"/>
        </svg>
        Selamat Datang di SiJagaTani
      </h1>
      <p class="subtitle">Sistem Informasi Arsip Data Petani dan Lahan Pertanian</p>
      <div class="features">
        <div class="feature-card">
          <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <h3>Data Petani</h3>
          <p>Kelola data petani dengan lengkap dan terstruktur</p>
          <router-link to="/petani" class="btn-feature">Kelola Data</router-link>
        </div>
        <div class="feature-card">
          <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
              <path d="M9 22v-4h6v4"/>
              <path d="M8 6h.01"/>
              <path d="M16 6h.01"/>
              <path d="M12 6h.01"/>
              <path d="M12 10h.01"/>
              <path d="M12 14h.01"/>
              <path d="M16 10h.01"/>
              <path d="M16 14h.01"/>
              <path d="M8 10h.01"/>
              <path d="M8 14h.01"/>
            </svg>
          </div>
          <h3>Data Makloon</h3>
          <p>Kelola data makloon dan distribusi hasil panen</p>
          <router-link to="/penggilingan" class="btn-feature">Kelola Data</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const stats = ref({
  totalPetani: 0,
  totalLahan: 0,
  totalLuas: 0
})

const fetchStats = async () => {
  try {
    const [petaniRes, lahanRes] = await Promise.all([
      api.get('/petani'),
      api.get('/lahan')
    ])
    
    stats.value.totalPetani = petaniRes.data.data.length
    stats.value.totalLahan = lahanRes.data.data.length
    stats.value.totalLuas = lahanRes.data.data
      .reduce((sum, lahan) => sum + parseFloat(lahan.luas || 0), 0)
      .toFixed(2)
  } catch (error) {
    console.error('Error fetching stats:', error)
  }
}

onMounted(() => {
  fetchStats()
})
</script>

<style scoped>
.home-view {
  width: 100%;
  max-width: 100%;
}

.hero-section {
  text-align: center;
  padding: 3rem 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  margin-bottom: 2rem;
}

.hero-section h1 {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  color: #2e7d32;
}

.subtitle {
  font-size: 1.2rem;
  color: #666;
  margin-bottom: 3rem;
}

.features {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2rem;
  margin-top: 2rem;
  max-width: 1000px;
  margin-left: auto;
  margin-right: auto;
}

.feature-card {
  background: #f8f9fa;
  padding: 2rem;
  border-radius: 10px;
  text-align: center;
  transition: transform 0.3s, box-shadow 0.3s;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.feature-card h3 {
  margin-bottom: 0.5rem;
  color: #333;
}

.feature-card p {
  color: #666;
  margin-bottom: 1.5rem;
}

.btn-feature {
  display: inline-block;
  padding: 0.75rem 2rem;
  background: #4caf50;
  color: white;
  text-decoration: none;
  border-radius: 5px;
  font-weight: 500;
  transition: background 0.3s;
}

.btn-feature:hover {
  background: #388e3c;
}

.stats-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
  color: white;
  padding: 2rem;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.stat-number {
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 1rem;
  opacity: 0.95;
}

.info-section {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.info-section h2 {
  margin-bottom: 1.5rem;
  text-align: center;
}

.feature-list {
  list-style: none;
  padding: 0;
}

.feature-list li {
  padding: 0.75rem 0;
  font-size: 1.1rem;
  color: #555;
  border-bottom: 1px solid #eee;
}

.feature-list li:last-child {
  border-bottom: none;
}

@media (max-width: 992px) {
  .home-view {
    padding: 0 10px;
  }

  .features {
    grid-template-columns: 1fr;
  }

  .stats-section {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .hero-section {
    padding: 2rem 1rem;
  }

  .hero-section h1 {
    font-size: 1.8rem;
  }

  .subtitle {
    font-size: 1rem;
    margin-bottom: 2rem;
  }

  .features {
    gap: 1rem;
  }

  .feature-card {
    padding: 1.5rem;
  }

  .icon {
    font-size: 3rem;
  }

  .stat-card {
    padding: 1.5rem;
  }

  .stat-number {
    font-size: 2.5rem;
  }

  .info-section {
    padding: 1.5rem;
  }

  .feature-list li {
    font-size: 1rem;
  }
}

@media (max-width: 480px) {
  .hero-section h1 {
    font-size: 1.5rem;
  }

  .subtitle {
    font-size: 0.9rem;
  }

  .icon {
    font-size: 2.5rem;
  }

  .feature-card h3 {
    font-size: 1.2rem;
  }

  .feature-card p {
    font-size: 0.9rem;
  }

  .btn-feature {
    padding: 0.6rem 1.5rem;
    font-size: 0.9rem;
  }

  .stat-number {
    font-size: 2rem;
  }

  .stat-label {
    font-size: 0.9rem;
  }
}

.icon-inline {
  width: 20px;
  height: 20px;
  display: inline-block;
  vertical-align: middle;
  margin-right: 8px;
}

.feature-card .icon svg {
  width: 48px;
  height: 48px;
  stroke: #4caf50;
}

.feature-list li svg {
  width: 18px;
  height: 18px;
  stroke: #4caf50;
  margin-right: 8px;
}
</style>
