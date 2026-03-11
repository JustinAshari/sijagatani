<template>
  <div class="fd-wrapper" ref="wrapperRef">
    <button
      @click="togglePanel"
      class="fd-btn"
      :class="{ 'fd-btn--active': isOpen, 'fd-btn--filtered': activeCount > 0 }"
      type="button"
    >
      <!-- Funnel icon -->
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
      </svg>
      Filter
      <span v-if="activeCount > 0" class="fd-badge">{{ activeCount }}</span>
      <svg class="fd-chevron" :class="{ 'fd-chevron--open': isOpen }" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="6 9 12 15 18 9"/>
      </svg>
    </button>

    <!-- Dropdown panel -->
    <transition name="fd-fade">
      <div v-if="isOpen" class="fd-panel">
        <div class="fd-panel-header">
          <span class="fd-panel-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            Filter Data
          </span>
          <button @click="closePanel" class="fd-close" type="button">&times;</button>
        </div>

        <div class="fd-panel-body">
          <slot />
        </div>

        <div class="fd-panel-footer">
          <button @click="onReset" class="fd-btn-reset" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="23 4 23 10 17 10"/>
              <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
            </svg>
            Reset
          </button>
          <button @click="onApply" class="fd-btn-apply" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.35-4.35"/>
            </svg>
            Terapkan
          </button>
        </div>
      </div>
    </transition>

    <!-- Backdrop -->
    <div v-if="isOpen" class="fd-backdrop" @click="closePanel" />
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  activeCount: {
    type: Number,
    default: 0,
  },
})

const emit = defineEmits(['apply', 'reset'])

const isOpen = ref(false)
const wrapperRef = ref(null)

const togglePanel = () => {
  isOpen.value = !isOpen.value
}

const closePanel = () => {
  isOpen.value = false
}

const onApply = () => {
  emit('apply')
  closePanel()
}

const onReset = () => {
  emit('reset')
  closePanel()
}
</script>

<style scoped>
.fd-wrapper {
  position: relative;
  display: inline-block;
}

/* Trigger button */
.fd-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  height: 36px;
  padding: 0 14px;
  background: #fff;
  color: #374151;
  border: 1.5px solid #d1d5db;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.15s ease;
  white-space: nowrap;
}

.fd-btn:hover {
  border-color: #9ca3af;
  background: #f9fafb;
}

.fd-btn--active {
  border-color: #475569;
  background: #f1f5f9;
}

.fd-btn--filtered {
  border-color: #3b82f6;
  background: #eff6ff;
  color: #1d4ed8;
}

.fd-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 18px;
  height: 18px;
  padding: 0 5px;
  background: #3b82f6;
  color: #fff;
  border-radius: 999px;
  font-size: 0.7rem;
  font-weight: 700;
  line-height: 1;
}

.fd-chevron {
  transition: transform 0.2s ease;
  flex-shrink: 0;
}

.fd-chevron--open {
  transform: rotate(180deg);
}

/* Backdrop */
.fd-backdrop {
  position: fixed;
  inset: 0;
  z-index: 99;
}

/* Panel */
.fd-panel {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  z-index: 100;
  min-width: 280px;
  max-width: 340px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  overflow: hidden;
}

.fd-panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.fd-panel-title {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.85rem;
  font-weight: 600;
  color: #334155;
}

.fd-close {
  background: none;
  border: none;
  font-size: 1.2rem;
  color: #94a3b8;
  cursor: pointer;
  line-height: 1;
  padding: 0 2px;
}

.fd-close:hover { color: #475569; }

.fd-panel-body {
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.fd-panel-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8px;
  padding: 12px 16px;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}

.fd-btn-reset {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  height: 32px;
  padding: 0 12px;
  background: #fff;
  color: #64748b;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.8rem;
  font-weight: 500;
  transition: all 0.15s;
}

.fd-btn-reset:hover {
  background: #f1f5f9;
  border-color: #94a3b8;
}

.fd-btn-apply {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  height: 32px;
  padding: 0 14px;
  background: #475569;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.8rem;
  font-weight: 600;
  transition: background 0.15s;
}

.fd-btn-apply:hover { background: #334155; }

/* Transition */
.fd-fade-enter-active,
.fd-fade-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}

.fd-fade-enter-from,
.fd-fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
