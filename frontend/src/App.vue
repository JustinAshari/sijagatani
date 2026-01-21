<script setup>
import { RouterView, useRoute } from 'vue-router'
import { computed } from 'vue'
import AppSidebar from './components/AppSidebar.vue'
import { useAuthStore } from './stores/auth'
import bulogLogo from './assets/Bulog Logo.PNG'

const route = useRoute()
const authStore = useAuthStore()

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
          <img :src="bulogLogo" alt="Bulog Logo" class="bulog-logo">
          <h1 class="app-title">SiJagaTani</h1>
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
                 authStore.isAdmin ? 'Administrator' : 
                 authStore.isLapangan ? 'Admin Lapangan' : 
                 authStore.isPenggilingan ? 'Penggilingan' : 'User' }}
            </span>
          </div>
          <button @click="handleLogout" class="btn-logout">
            Logout
          </button>
        </div>
      </div>
    </header>

    <!-- Sidebar -->
    <AppSidebar />

    <!-- Main Content -->
    <div class="main-wrapper">
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

.app-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
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

.btn-logout:hover {
  background: #c82333;
  transform: translateY(-1px);
}

.navbar-subtitle {
  font-size: 0.9rem;
  color: #666;
  font-weight: 500;
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

.app-main {
  flex: 1;
  padding: 1.5rem;
  width: 100%;
}

@media (max-width: 768px) {
  .navbar-content {
    padding: 0 1rem;
  }

  .bulog-logo {
    height: 35px;
  }

  .app-title {
    font-size: 1.2rem;
  }

  .navbar-subtitle {
    display: none;
  }
  
  .main-wrapper {
    margin-left: 70px;
  }

  .app-main {
    padding: 1rem;
  }
}

@media (max-width: 480px) {
  .app-title {
    font-size: 1rem;
  }

  .bulog-logo {
    height: 30px;
  }

  .app-main {
    padding: 0.8rem;
  }
}
</style>
