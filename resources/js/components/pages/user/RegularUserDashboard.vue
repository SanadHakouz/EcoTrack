<template>
  <DashboardLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-gray-900">This is the user dashboard</h1>
      <p class="mt-4 text-gray-600">Welcome to your personal EcoTrack dashboard!</p>

      <!-- Dashboard content will go here -->
      <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-medium text-gray-900">Eco Score</h3>
          <p class="mt-2 text-3xl font-bold text-green-600">{{ user?.eco_score || 0 }}</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-medium text-gray-900">Recent Emissions</h3>
          <p class="mt-2 text-gray-600">Coming soon...</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-medium text-gray-900">Community</h3>
          <p class="mt-2 text-gray-600">Coming soon...</p>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import DashboardLayout from '../../layouts/DashboardLayout.vue'

const authStore = useAuthStore()
const user = computed(() => authStore.user)
</script>
