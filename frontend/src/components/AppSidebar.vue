<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import bulogLogo from '../assets/Bulog Logo.png'

const authStore = useAuthStore()

defineProps({
  open: {
    type: Boolean,
    default: true
  }
})

defineEmits(['close'])
</script>

<template>
  <aside class="sidebar" :class="{ collapsed: !open }">
    <!-- Logo Section -->
    <div class="sidebar-logo">
      <img :src="bulogLogo" alt="Bulog" class="logo-img" />
    </div>

    <!-- Nav Items -->
    <nav class="sidebar-nav">
      <RouterLink to="/" class="sidebar-link" exact-active-class="active">
        <span class="icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <rect x="3" y="3" width="7" height="7" rx="1.5"/>
            <rect x="14" y="3" width="7" height="7" rx="1.5"/>
            <rect x="3" y="14" width="7" height="7" rx="1.5"/>
            <rect x="14" y="14" width="7" height="7" rx="1.5"/>
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

      <RouterLink v-if="authStore.canAccessPetani" to="/surat-pernyataan" class="sidebar-link">
        <span class="icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10 9 9 9 8 9"/>
          </svg>
        </span>
        <span class="label">Surat Pernyataan</span>
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
      
      <RouterLink v-if="authStore.canAccessUsers" to="/wilayah" class="sidebar-link">
        <span class="icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
            <circle cx="12" cy="9" r="2.5"/>
          </svg>
        </span>
        <span class="label">Data Wilayah</span>
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
  </aside>
</template>

<style scoped>
.sidebar {
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  width: 240px;
  background: #ffffff;
  z-index: 1000;
  border-right: 1px solid #e8ecf0;
  display: flex;
  flex-direction: column;
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  box-shadow: 2px 0 16px rgba(0, 0, 0, 0.07);
}

.sidebar.collapsed {
  transform: translateX(-240px);
}

/* Logo */
.sidebar-logo {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.4rem 1.4rem 1.2rem;
  border-bottom: 1px solid #f0f3f7;
  flex-shrink: 0;
}

.logo-img {
  height: 52px;
  width: auto;
  object-fit: contain;
}

.logo-text {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.logo-name {
  font-size: 1.1rem;
  font-weight: 800;
  color: #1a2332;
  letter-spacing: -0.3px;
  line-height: 1;
}

.logo-sub {
  font-size: 0.6rem;
  color: #9ea9b8;
  font-weight: 500;
  letter-spacing: 0.1px;
  line-height: 1;
}

/* Nav */
.sidebar-nav {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
  padding: 1rem 0.75rem;
  overflow-y: auto;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding: 0.72rem 1rem;
  color: #8a96a8;
  text-decoration: none;
  border-radius: 10px;
  transition: all 0.15s ease;
  font-weight: 500;
  font-size: 0.9rem;
}

.sidebar-link:hover {
  background: #f3f6fb;
  color: #374151;
}

.sidebar-link.router-link-active,
.sidebar-link.active {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: #ffffff;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
  font-weight: 600;
}

.sidebar-link.router-link-active:hover,
.sidebar-link.active:hover {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff;
}

.sidebar-link .icon {
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.sidebar-link .icon svg {
  width: 18px;
  height: 18px;
}

.sidebar-link .label {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.sidebar-nav::-webkit-scrollbar {
  width: 3px;
}

.sidebar-nav::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar-nav::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 2px;
}
</style>