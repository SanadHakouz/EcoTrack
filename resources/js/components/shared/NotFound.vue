<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="max-w-md w-full text-center">
      <div class="mb-8">
        <h1 class="text-9xl font-bold text-green-600">404</h1>
        <h2 class="text-2xl font-semibold text-gray-900 mt-4">Page Not Found</h2>
        <p class="text-gray-600 mt-2">The page you're looking for doesn't exist.</p>
      </div>

      <div class="space-y-4">
        <router-link
          to="/"
          class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors"
        >
          Go Back Home
        </router-link>

        <div>
          <button
            @click="$router.go(-1)"
            class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors"
          >
            Go Back
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// 404 page functionality
</script>
