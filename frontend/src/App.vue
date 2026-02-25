<script setup>
import { RouterView, useRoute } from 'vue-router'
import { ref, computed } from 'vue'
import AppSidebar from './components/AppSidebar.vue'
import { useAuthStore } from './stores/auth'
import sijagataniLogo from './assets/Sijagatani Logo.PNG'

const route = useRoute()
const authStore = useAuthStore()
const sidebarOpen = ref(window.innerWidth > 768)

const showLayout = computed(() => route.name !== 'login')

const handleLogout = async () => {
  if (confirm('Apakah Anda yakin ingin logout?')) {
    await authStore.logout()
    window.location.href = '/login'
  }
}
</script>

<template>
  <div v-if="!showLayout">
    <RouterView />
  </div>
  <div v-else class="app-container">
    
    <!-- Top Navbar -->
    <header class="top-navbar">
      <div class="navbar-content">
        <div class="navbar-left">
          <button @click="sidebarOpen = !sidebarOpen" class="btn-toggle-sidebar" :title="sidebarOpen ? 'Tutup Sidebar' : 'Buka Sidebar'">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="3" y1="6" x2="21" y2="6"/>
              <line x1="3" y1="12" x2="21" y2="12"/>
              <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
          </button>
          <img :src="sijagataniLogo" alt="Sijagatani Logo" class="sijagatani-logo">
          <h1 class="app-title">Sistem Jemput<br>Gabah Jagung Petani</h1>
        </div>
        <div class="navbar-right">
          <div class="user-info">
            <span class="user-name">{{ authStore.user?.name }}</span>
            <span class="user-role" :class="{
              'role-superadmin': authStore.isSuperAdmin,
              'role-admin': authStore.isAdmin,
              'role-lapangan': authStore.isLapangan,
              'role-penggilingan': authStore.isPenggilingan
            }">
              {{ authStore.isSuperAdmin ? 'Super Admin' : 
                 authStore.isAdmin ? 'Admin' : 
                 authStore.isLapangan ? 'Lapangan' : 
                 authStore.isPenggilingan ? 'Penggilingan' : 'User' }}
            </span>
          </div>
          <button @click="handleLogout" class="btn-logout">
            <svg class="logout-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span class="logout-text">Logout</span>
          </button>
        </div> 
      </div>
    </header>

    <!-- Sidebar -->
    <AppSidebar :open="sidebarOpen" @close="sidebarOpen = false" />

    <!-- Mobile Backdrop -->
    <div v-if="sidebarOpen" class="mobile-backdrop" @click="sidebarOpen = false"></div>

    <!-- Main Content -->
    <div class="main-wrapper" :class="{ 'sidebar-closed': !sidebarOpen }">
      <main class="app-main">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  background: #f5f5f5;
  color: #333;
}

.app-container {
  min-height: 100vh;
  display: flex;
}

/* Top Navbar Styles */
.top-navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 70px;
  background: #ffffff;
  border-bottom: 1px solid #e0e0e0;
  z-index: 1100;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.navbar-content {
  height: 100%;
  padding: 0 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.navbar-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.bulog-logo {
  height: 45px;
  width: auto;
  object-fit: contain;
}

.sijagatani-logo {
  height: 53px;
  width: auto;
  object-fit: contain;
}

.app-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
  line-height: 1.3;
}

.navbar-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 2px;
}

.user-name {
  font-size: 0.9rem;
  color: #2c3e50;
  font-weight: 600;
}

.user-role {
  font-size: 0.75rem;
  padding: 2px 8px;
  border-radius: 12px;
  font-weight: 600;
}

.role-superadmin {
  background: #e3f2fd;
  color: #1976d2;
}

.role-admin {
  background: #f3e5f5;
  color: #7b1fa2;
}

.role-lapangan {
  background: #e8f5e9;
  color: #388e3c;
}

.role-penggilingan {
  background: #fff3e0;
  color: #f57c00;
}

.role-user {
  background: #f3e5f5;
  color: #7b1fa2;
}

.btn-logout {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.logout-icon {
  width: 17px;
  height: 17px;
  flex-shrink: 0;
}

.btn-logout:hover {
  background: #c82333;
  transform: translateY(-1px);
}

.btn-toggle-sidebar {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  background: transparent;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  color: #2c3e50;
  transition: background 0.2s;
  flex-shrink: 0;
}

.btn-toggle-sidebar:hover {
  background: #f0f0f0;
}

.navbar-subtitle {
  font-size: 0.9rem;
  color: #666;
  font-weight: 500;
}

/* Mobile Backdrop */
.mobile-backdrop {
  display: none;
}

/* Main Content */
.main-wrapper {
  flex: 1;
  margin-left: 250px;
  margin-top: 70px;
  min-height: calc(100vh - 70px);
  display: flex;
  flex-direction: column;
  transition: margin-left 0.3s ease;
  background: #f5f5f5;
}

.main-wrapper.sidebar-closed {
  margin-left: 0;
}

.app-main {
  flex: 1;
  padding: 1.5rem;
  width: 100%;
}

@media (max-width: 768px) {
  .top-navbar {
    height: 56px;
  }

  .navbar-content {
    padding: 0 0.8rem;
  }

  .sijagatani-logo {
    height: 36px;
  }

  .app-title {
    display: none;
  }

  .user-name {
    display: none;
  }

  .logout-text {
    display: none;
  }

  .btn-logout {
    padding: 8px 10px;
  }

  /* Mobile: sidebar is an overlay drawer, does NOT push content */
  .main-wrapper,
  .main-wrapper.sidebar-closed {
    margin-left: 0;
    margin-top: 56px;
    min-height: calc(100vh - 56px);
  }

  /* Backdrop shown behind drawer on mobile */
  .mobile-backdrop {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    z-index: 999;
  }

  .app-main {
    padding: 0.8rem;
  }
}

@media (max-width: 480px) {
  .sijagatani-logo {
    height: 30px;
  }

  .app-main {
    padding: 0.6rem;
  }
}
</style>
