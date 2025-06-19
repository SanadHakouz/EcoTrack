<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Dashboard Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <!-- Top Part: Logo, User Info, Language & Logout -->
      <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center">
              <router-link to="/dashboard" class="flex items-center">
                <img
                  src="/storage/logo.jpg"
                  alt="EcoTrack Logo"
                  class="h-10 w-10 rounded-full ring-2 ring-green-200"
                  @error="handleLogoError"
                >
                <div class="ml-3">
                  <span class="text-xl font-bold text-gray-900">EcoTrack</span>
                  <p class="text-xs text-green-600 font-medium">Dashboard</p>
                </div>
              </router-link>
            </div>

            <!-- User Profile, Language & Logout Section -->
            <div class="flex items-center space-x-6">
              <!-- User Profile Image and Info -->
              <div class="flex items-center space-x-3 cursor-pointer group" @click="goToSettings">
                <img
                  :src="userProfileImage"
                  :alt="authStore.userName"
                  class="h-12 w-12 rounded-full object-cover ring-2 ring-green-200 group-hover:ring-green-400 transition-all duration-200"
                  @error="handleProfileImageError"
                >
                <div class="hidden sm:block">
                  <p class="text-sm font-semibold text-gray-900 group-hover:text-green-700 transition-colors">
                    {{ authStore.userName }}
                  </p>
                  <p class="text-xs text-gray-500 capitalize flex items-center">
                    <span class="inline-block w-2 h-2 bg-green-400 rounded-full mr-1.5"></span>
                    {{ authStore.userRole }}
                  </p>
                </div>
              </div>

              <!-- Action Buttons: Language Switcher & Logout -->
              <div class="flex items-center space-x-2">
                <!-- Language Switcher (matching logout button size) -->
                <div class="w-24 h-10">
                  <LanguageSwitcher class="w-full h-full" />
                </div>

                <!-- Logout Button -->
                <button
                  @click="handleLogout"
                  :disabled="authStore.isLoading"
                  class="w-24 h-10 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-md text-sm font-medium transition-all duration-200 flex items-center justify-center"
                >
                  <span v-if="authStore.isLoading" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-xs">...</span>
                  </span>
                  <span v-else class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="text-xs">Logout</span>
                  </span>
                </button>
              </div>

              <!-- Mobile menu button -->
              <div class="md:hidden">
                <button
                  @click="mobileMenuOpen = !mobileMenuOpen"
                  class="text-gray-700 hover:text-green-600 p-2 rounded-md"
                >
                  <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Part: Quick Links Navigation -->
      <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-center md:justify-start h-14">
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center space-x-1">
              <router-link
                to="/dashboard"
                class="group flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
                :class="getDashboardLinkClass('dashboard')"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                </svg>
                {{ $t('navbar.dashboard') }}
              </router-link>

              <router-link
                to="/emissions"
                class="group flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
                :class="getDashboardLinkClass('emissions')"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                {{ $t('navbar.emissions') }}
              </router-link>

              <router-link
                to="/community"
                class="group flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
                :class="getDashboardLinkClass('community')"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                {{ $t('navbar.community') }}
              </router-link>

              <router-link
                :to="`/profile/${authStore.user?.id}`"
                class="group flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
                :class="getDashboardLinkClass('profile')"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                {{ $t('navbar.profile') }}
              </router-link>

              <router-link
                to="/settings"
                class="group flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
                :class="getDashboardLinkClass('settings')"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ $t('navbar.settings') }}
              </router-link>

              <!-- Admin/Moderator Links -->
              <router-link
                v-if="authStore.isModerator"
                to="/moderator"
                class="group flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
                :class="getModeratorLinkClass()"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                {{ $t('navbar.moderator') }}
              </router-link>

              <router-link
                v-if="authStore.isAdmin"
                to="/admin"
                class="group flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
                :class="getAdminLinkClass()"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ $t('navbar.admin') }}
              </router-link>
            </nav>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-if="mobileMenuOpen" class="md:hidden bg-white border-t border-gray-200">
        <!-- Mobile User Profile -->
        <div class="flex items-center px-4 py-4 border-b border-gray-100 bg-gray-50">
          <img
            :src="userProfileImage"
            :alt="authStore.userName"
            class="h-12 w-12 rounded-full object-cover ring-2 ring-green-200"
            @error="handleProfileImageError"
          >
          <div class="ml-3 flex-1">
            <p class="text-base font-semibold text-gray-900">{{ authStore.userName }}</p>
            <p class="text-sm text-gray-500 capitalize">{{ authStore.userRole }}</p>
          </div>
          <div class="flex space-x-2">
            <LanguageSwitcher />
            <button
              @click="handleLogout"
              :disabled="authStore.isLoading"
              class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium"
            >
              Logout
            </button>
          </div>
        </div>

        <!-- Mobile Navigation Links -->
        <div class="px-4 py-2 space-y-1">
          <router-link
            to="/dashboard"
            class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50 transition-colors"
            @click="mobileMenuOpen = false"
          >
            <svg class="w-5 h-5 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
            </svg>
            {{ $t('navbar.dashboard') }}
          </router-link>

          <router-link
            to="/emissions"
            class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50 transition-colors"
            @click="mobileMenuOpen = false"
          >
            <svg class="w-5 h-5 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"></path>
            </svg>
            {{ $t('navbar.emissions') }}
          </router-link>

          <router-link
            to="/community"
            class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50 transition-colors"
            @click="mobileMenuOpen = false"
          >
            <svg class="w-5 h-5 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857"></path>
            </svg>
            {{ $t('navbar.community') }}
          </router-link>

          <router-link
            :to="`/profile/${authStore.user?.id}`"
            class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50 transition-colors"
            @click="mobileMenuOpen = false"
          >
            <svg class="w-5 h-5 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            {{ $t('navbar.profile') }}
          </router-link>

          <router-link
            to="/settings"
            class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-green-50 transition-colors"
            @click="mobileMenuOpen = false"
          >
            <svg class="w-5 h-5 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066"></path>
            </svg>
            {{ $t('navbar.settings') }}
          </router-link>

          <router-link
            v-if="authStore.isModerator"
            to="/moderator"
            class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:text-orange-600 hover:bg-orange-50 transition-colors"
            @click="mobileMenuOpen = false"
          >
            <svg class="w-5 h-5 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944"></path>
            </svg>
            {{ $t('navbar.moderator') }}
          </router-link>

          <router-link
            v-if="authStore.isAdmin"
            to="/admin"
            class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-colors"
            @click="mobileMenuOpen = false"
          >
            <svg class="w-5 h-5 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066"></path>
            </svg>
            {{ $t('navbar.admin') }}
          </router-link>
        </div>
      </div>
    </header>

    <!-- Inactive Navbar with Notification Icons -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center h-12">
          <div class="flex items-center space-x-8">
            <!-- Followers Icon -->
            <div class="flex items-center text-gray-400 cursor-not-allowed">
              <div class="relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <!-- Notification Badge -->
                <span class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                  0
                </span>
              </div>
              <span class="ml-2 text-sm font-medium">{{ $t('notifications.followers') }}</span>
            </div>

            <!-- Separator -->
            <div class="h-6 w-px bg-gray-200"></div>

            <!-- Reactions & Comments Icon -->
            <div class="flex items-center text-gray-400 cursor-not-allowed">
              <div class="relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <!-- Notification Badge -->
                <span class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                  0
                </span>
              </div>
              <span class="ml-2 text-sm font-medium">{{ $t('notifications.reactionsComments') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import LanguageSwitcher from '../LanguageSwitcher.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const mobileMenuOpen = ref(false)

// Computed property for user profile image
const userProfileImage = computed(() => {
  if (authStore.user?.profile_image) {
    return `/storage/${authStore.user.profile_image}`
  }
  return '/storage/profile-pictures/pp1.JPG'
})

// Link styling functions
const getDashboardLinkClass = (routeName) => {
  const isActive = route.name === routeName
  return isActive
    ? 'text-green-700 bg-green-100 border border-green-200'
    : 'text-gray-600 hover:text-green-600 hover:bg-green-50 border border-transparent hover:border-green-100'
}

const getModeratorLinkClass = () => {
  const isActive = route.name === 'moderator'
  return isActive
    ? 'text-orange-700 bg-orange-100 border border-orange-200'
    : 'text-gray-600 hover:text-orange-600 hover:bg-orange-50 border border-transparent hover:border-orange-100'
}

const getAdminLinkClass = () => {
  const isActive = route.name === 'admin'
  return isActive
    ? 'text-blue-700 bg-blue-100 border border-blue-200'
    : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50 border border-transparent hover:border-blue-100'
}

const handleLogout = async () => {
  await authStore.logout()
  router.push({ name: 'home' })
}

const goToSettings = () => {
  router.push({ name: 'settings' })
}

const handleLogoError = (event) => {
  event.target.style.display = 'none'
}

const handleProfileImageError = (event) => {
  event.target.src = '/storage/profile-pictures/pp1.JPG'
}
</script>
