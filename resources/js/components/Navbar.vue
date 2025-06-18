<template>
  <nav class="bg-black shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center space-x-3">
          <img
            src="/storage/logo.jpg"
            alt="EcoTrack Logo"
            class="h-10 w-10 rounded-full object-cover ring-2 ring-green-500"
          >
          <h1 class="text-xl font-bold text-white">
            🌍 {{ $t('navbar.title') }}
          </h1>
        </div>
        <div class="flex items-center space-x-4">
          <div class="text-sm text-gray-300 hidden md:block">
            {{ $t('navbar.subtitle') }}
          </div>
          <LanguageSwitcher />
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import LanguageSwitcher from './LanguageSwitcher.vue';
</script>
