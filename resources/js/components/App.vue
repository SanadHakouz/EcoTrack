<template>
    <div id="app">
      <!-- Your existing LandingPage through router -->
      <router-view />

      <!-- Global loading indicator -->
      <div v-if="authStore.isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto"></div>
          <p class="mt-2 text-gray-600">Loading...</p>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { useAuthStore } from '../stores/auth'

  const authStore = useAuthStore()
  </script>