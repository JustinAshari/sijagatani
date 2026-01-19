<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
</script>

<template>
  <aside class="sidebar">
    <div class="sidebar-content">
      <div class="sidebar-header">
        <h2>Menu</h2>
      </div>

      <nav class="sidebar-nav">
        <RouterLink to="/" class="sidebar-link">
          <span class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
              <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
          </span>
          <span class="label">Beranda</span>
        </RouterLink>

        <RouterLink v-if="authStore.canAccessPetani" to="/petani" class="sidebar-link">
          <span class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </span>
          <span class="label">Data Petani</span>
        </RouterLink>

        <RouterLink v-if="authStore.canAccessPenggilingan" to="/penggilingan" class="sidebar-link">
          <span class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
          </span>
          <span class="label">Data Makloon</span>
        </RouterLink>

        <RouterLink v-if="authStore.canAccessUsers" to="/users" class="sidebar-link">
          <span class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </span>
          <span class="label">Kelola Akun</span>
        </RouterLink>
      </nav>
    </div>
  </aside>
</template>

<style scoped>
.sidebar {
  position: fixed;
  left: 0;
  top: 70px;
  height: calc(100vh - 70px);
  width: 250px;
  background: #ffffff;
  color: #333;
  z-index: 1000;
  border-right: 1px solid #e0e0e0;
}

.sidebar-content {
  padding: 1.5rem 0;
  height: 100%;
  overflow-y: auto;
}

.sidebar-header {
  padding: 0 1.5rem 1.5rem;
  border-bottom: 1px solid #e8e8e8;
  margin-bottom: 1rem;
}

.sidebar-header h2 {
  color: #333;
  font-size: 1.3rem;
  font-weight: 600;
  margin: 0;
}

.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
  padding: 0 0.8rem;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  padding: 0.8rem 1rem;
  color: #555;
  text-decoration: none;
  border-radius: 6px;
  transition: all 0.2s;
  font-weight: 400;
  font-size: 0.95rem;
}

.sidebar-link:hover {
  background: #f0f0f0;
  color: #333;
}

.sidebar-link.router-link-active {
  background: #e8e8e8;
  color: #000;
  font-weight: 500;
}

.sidebar-link .icon {
  min-width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sidebar-link .icon svg {
  width: 20px;
  height: 20px;
  stroke: currentColor;
}

.sidebar-link .label {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.sidebar-content::-webkit-scrollbar {
  width: 4px;
}

.sidebar-content::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar-content::-webkit-scrollbar-thumb {
  background: #d0d0d0;
  border-radius: 2px;
}

.sidebar-content::-webkit-scrollbar-thumb:hover {
  background: #b0b0b0;
}

@media (max-width: 768px) {
  .sidebar {
    width: 70px;
  }

  .sidebar-header h2 {
    font-size: 0;
  }

  .sidebar-header h2::first-letter {
    font-size: 1.2rem;
  }

  .sidebar-link .label {
    display: none;
  }

  .sidebar-link {
    justify-content: center;
  }
}
</style>
