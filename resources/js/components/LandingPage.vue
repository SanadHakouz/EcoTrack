<template>
    <div class="min-h-screen bg-white">
      <!-- Navbar Component -->
      <Navbar />

      <!-- Main Content -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Split Layout -->
        <div class="grid lg:grid-cols-2 gap-12 mb-16">
          <!-- Left Side - Project Information -->
          <div class="space-y-8">
            <div>
              <h2 class="text-4xl font-bold text-black mb-6">
                {{ $t('landing.welcome') }}
              </h2>
              <div class="prose prose-lg text-gray-700">
                <p class="text-xl leading-relaxed mb-6">
                  {{ $t('landing.description', {
                    personalCommissionLog: $t('landing.personalCommissionLog'),
                    socialMediaPlatform: $t('landing.socialMediaPlatform')
                  }) }}
                </p>

                <div class="bg-green-50 p-6 rounded-lg border-l-4 border-green-500 mb-6">
                  <h3 class="text-lg font-semibold text-green-800 mb-3">{{ $t('landing.keyFeatures') }}</h3>
                  <ul class="space-y-2 text-green-700">
                    <li class="flex items-center">
                      <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                      {{ $t('landing.features.carbonTracking') }}
                    </li>
                    <li class="flex items-center">
                      <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                      {{ $t('landing.features.commissionChallenges') }}
                    </li>
                    <li class="flex items-center">
                      <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                      {{ $t('landing.features.socialSharing') }}
                    </li>
                    <li class="flex items-center">
                      <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                      {{ $t('landing.features.realtimeVisualization') }}
                    </li>
                  </ul>
                </div>

                <p class="text-gray-600">
                  {{ $t('landing.communityDescription') }}
                </p>
              </div>
            </div>

            <!-- REMOVED: Demo Counter section completely -->
          </div>

          <!-- Right Side - Login/Register Form -->
          <div class="lg:pl-8">
            <div class="bg-gray-50 p-8 rounded-lg border-2 border-gray-200">
              <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-black mb-2">
                  {{ isRegistering ? $t('auth.createAccount') : $t('auth.welcomeBack') }}
                </h3>
                <p class="text-gray-600">
                  {{ isRegistering ? $t('auth.joinSubtitle') : $t('auth.signInSubtitle') }}
                </p>
              </div>

              <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- Name field (only for registration) -->
                <div v-if="isRegistering">
                  <label class="block text-sm font-medium text-black mb-2">{{ $t('auth.fullName') }}</label>
                  <input
                    type="text"
                    v-model="formData.name"
                    :placeholder="$t('auth.fullNamePlaceholder')"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  >
                </div>

                <!-- Username field (only for registration) -->
                <div v-if="isRegistering">
                  <label class="block text-sm font-medium text-black mb-2">{{ $t('auth.username') || 'Username' }}</label>
                  <input
                    type="text"
                    v-model="formData.username"
                    placeholder="Enter your username"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  >
                </div>

                <!-- Email field -->
                <div>
                  <label class="block text-sm font-medium text-black mb-2">{{ $t('auth.email') }}</label>
                  <input
                    type="email"
                    v-model="formData.email"
                    :placeholder="$t('auth.emailPlaceholder')"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  >
                </div>

                <!-- Password field -->
                <div>
                  <label class="block text-sm font-medium text-black mb-2">{{ $t('auth.password') }}</label>
                  <input
                    type="password"
                    v-model="formData.password"
                    :placeholder="$t('auth.passwordPlaceholder')"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  >
                </div>

                <!-- Confirm Password (only for registration) -->
                <div v-if="isRegistering">
                  <label class="block text-sm font-medium text-black mb-2">{{ $t('auth.confirmPassword') }}</label>
                  <input
                    type="password"
                    v-model="formData.password_confirmation"
                    :placeholder="$t('auth.confirmPasswordPlaceholder')"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  >
                </div>

                <!-- Error Display -->
                <div v-if="authStore.error" class="text-red-600 text-sm bg-red-50 p-3 rounded">
                  {{ authStore.error }}
                </div>

                <!-- Submit Button -->
                <button
                  type="submit"
                  :disabled="authStore.isLoading"
                  class="w-full bg-green-500 text-white py-3 px-4 rounded-lg font-medium hover:bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                >
                  <span v-if="authStore.isLoading">{{ $t('auth.loading') }}...</span>
                  <span v-else>{{ isRegistering ? $t('auth.createAccountBtn') : $t('auth.signIn') }}</span>
                </button>
              </form>

              <!-- Toggle between Login/Register -->
              <div class="mt-6 text-center">
                <button
                  @click="toggleForm"
                  class="text-green-600 hover:text-green-700 font-medium underline"
                >
                  {{ isRegistering ? $t('auth.alreadyHaveAccount') : $t('auth.noAccount') }}
                </button>
              </div>

              <!-- Demo API Test -->
              <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="text-sm font-medium text-black mb-3">{{ $t('auth.apiDemo') }}</h4>
                <button
                  @click="fetchEnvironmentalData"
                  :disabled="apiLoading"
                  class="w-full bg-black text-white py-2 px-4 rounded disabled:bg-gray-400 transition-colors"
                >
                  {{ apiLoading ? $t('auth.loading') : $t('auth.testApiConnection') }}
                </button>

                <div v-if="apiError" class="mt-3 text-red-600 text-sm bg-red-50 p-2 rounded">
                  {{ $t('auth.error') }}: {{ apiError }}
                </div>

                <div v-if="apiData" class="mt-3 text-green-600 text-sm bg-green-50 p-2 rounded">
                  {{ $t('auth.apiConnectionSuccessful') }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Technical Configuration Section -->
        <div class="bg-black text-white rounded-lg p-8 mb-8">
          <h2 class="text-2xl font-semibold mb-6 text-green-400">{{ $t('technical.title') }}</h2>
          <div class="grid md:grid-cols-2 gap-8">
            <div>
              <h3 class="font-medium mb-3 text-green-300">{{ $t('technical.frontendStack') }}</h3>
              <ul class="text-gray-300 space-y-2">
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">▶</span>
                  {{ $t('technical.stack.vue') }}
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">▶</span>
                  {{ $t('technical.stack.pinia') }}
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">▶</span>
                  {{ $t('technical.stack.axios') }}
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">▶</span>
                  {{ $t('technical.stack.tailwind') }}
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">▶</span>
                  {{ $t('technical.stack.vite') }}
                </li>
              </ul>
            </div>
            <div>
              <h3 class="font-medium mb-3 text-green-300">{{ $t('technical.fileStructure') }}</h3>
              <ul class="text-gray-300 space-y-2">
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">📁</span>
                  <code class="text-green-400">vite.config.js</code> - {{ $t('technical.files.viteConfig') }}
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">📁</span>
                  <code class="text-green-400">resources/js/app.js</code> - {{ $t('technical.files.appJs') }}
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">📁</span>
                  <code class="text-green-400">resources/js/components/</code> - {{ $t('technical.files.components') }}
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">📁</span>
                  <code class="text-green-400">resources/js/stores/</code> - {{ $t('technical.files.stores') }}
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">📁</span>
                  <code class="text-green-400">resources/js/bootstrap.js</code> - {{ $t('technical.files.bootstrap') }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Author Notes Section -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
          <h2 class="text-xl font-semibold mb-4 text-black flex items-center">
            <span class="text-green-600 mr-2">📝</span>
            {{ $t('authorNotes.title').slice(3) }}
          </h2>
          <div class="space-y-4">
            <div class="bg-white p-4 rounded border-l-4 border-green-500">
              <div class="flex items-start justify-between">
                <div>
                  <h3 class="font-medium text-black">{{ $t('authorNotes.note1.title') }}</h3>
                  <p class="text-gray-600 mt-1">
                    {{ $t('authorNotes.note1.content') }}
                  </p>
                </div>
                <span class="text-xs text-gray-400 whitespace-nowrap ml-4">
                  {{ $t('authorNotes.note1.timestamp') }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { ref, computed, watch } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import { useAuthStore } from '../stores/auth';
  import Navbar from './Navbar.vue';
  import axios from 'axios';

  const route = useRoute();
  const router = useRouter();
  const authStore = useAuthStore();

  // API test state
  const apiLoading = ref(false);
  const apiError = ref(null);
  const apiData = ref(null);

  // Form state - now router-aware
  const isRegistering = computed(() => {
    return route.meta.formType === 'register' || route.name === 'register';
  });

  // Form data
  const formData = ref({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: ''
  });

  // Watch route changes to clear form data
  watch(() => route.name, () => {
    clearApiTest();
    clearForm();
  });

  const toggleForm = () => {
    if (isRegistering.value) {
      router.push({ name: 'login' });
    } else {
      router.push({ name: 'register' });
    }
  };

  const clearForm = () => {
    formData.value = {
      name: '',
      username: '',
      email: '',
      password: '',
      password_confirmation: ''
    };
  };

  const clearApiTest = () => {
    apiError.value = null;
    apiData.value = null;
  };

  const fetchEnvironmentalData = async () => {
    apiLoading.value = true;
    apiError.value = null;

    try {
      const response = await axios.get('/test');
      apiData.value = response.data;
    } catch (error) {
      apiError.value = error.response?.data?.message || error.message || 'API test failed';
      console.error('Failed to fetch environmental data:', error);
    } finally {
      apiLoading.value = false;
    }
  };

  const handleSubmit = async () => {
    if (isRegistering.value) {
      const result = await authStore.register(formData.value);
      if (result.success) {
        router.push({ name: 'dashboard' });
      }
    } else {
      const result = await authStore.login({
        login: formData.value.email,
        password: formData.value.password
      });
      if (result.success) {
        router.push({ name: 'dashboard' });
      }
    }
  };
  </script>
