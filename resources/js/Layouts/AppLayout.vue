<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <!-- Sidebar -->
    <AppSidebar :is-open="sidebarOpen" @close="sidebarOpen = false" />

    <!-- Main content -->
    <div class="lg:pl-64">
      <!-- Header -->
      <AppHeader @toggle-sidebar="sidebarOpen = !sidebarOpen" />

      <!-- Page content -->
      <main class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <slot />
        </div>
      </main>
    </div>

    <!-- Notifications -->
    <NotificationsDropdown />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import AppSidebar from '@/Components/AppSidebar.vue'
import AppHeader from '@/Components/AppHeader.vue'
import NotificationsDropdown from '@/Components/NotificationsDropdown.vue'

const sidebarOpen = ref(false)
</script>
