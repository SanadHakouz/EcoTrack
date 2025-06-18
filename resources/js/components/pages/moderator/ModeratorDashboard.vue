<template>
  <DashboardLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-gray-900">Moderator Dashboard</h1>
      <p class="mt-4 text-gray-600">Moderate community content and manage users.</p>

      <!-- Moderator content will go here -->
      <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-medium text-gray-900">Reported Posts</h3>
          <p class="mt-2 text-3xl font-bold text-orange-600">0</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-medium text-gray-900">User Reports</h3>
          <p class="mt-2 text-3xl font-bold text-red-600">0</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-medium text-gray-900">Pending Reviews</h3>
          <p class="mt-2 text-3xl font-bold text-yellow-600">0</p>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '../../layouts/DashboardLayout.vue'
// Future moderator functionality will go here
</script>
