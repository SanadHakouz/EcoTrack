<template>
  <DashboardLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
      <p class="mt-4 text-gray-600">System administration and user management.</p>

      <!-- Admin content will go here -->
      <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-medium text-gray-900">Total Users</h3>
          <p class="mt-2 text-3xl font-bold text-blue-600">0</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-medium text-gray-900">Total Posts</h3>
          <p class="mt-2 text-3xl font-bold text-green-600">0</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-medium text-gray-900">System Health</h3>
          <p class="mt-2 text-3xl font-bold text-green-600">OK</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-medium text-gray-900">Reports</h3>
          <p class="mt-2 text-3xl font-bold text-red-600">0</p>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '../../layouts/DashboardLayout.vue'
// Future admin functionality will go here
</script>
