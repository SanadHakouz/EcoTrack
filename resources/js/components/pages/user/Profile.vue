<template>
  <DashboardLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Loading State -->
      <div v-if="isLoading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
        <span class="ml-3 text-gray-600">{{ $t('profile.loading') }}</span>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
          <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
          <h3 class="text-lg font-medium text-red-800 mb-2">{{ $t('profile.notFound') }}</h3>
          <p class="text-red-600">{{ error }}</p>
          <router-link
            to="/dashboard"
            class="mt-4 inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
          >
            {{ $t('profile.backToDashboard') }}
          </router-link>
        </div>
      </div>

      <!-- Profile Content -->
      <div v-else-if="profileUser">
        <!-- Profile Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
          <!-- Cover Photo Area -->
          <div class="h-64 md:h-80 bg-gradient-to-r from-green-400 via-blue-500 to-purple-600 relative">
            <!-- Future: Cover photo will go here -->
            <div class="absolute inset-0 bg-black bg-opacity-20"></div>
          </div>

          <!-- Profile Info -->
          <div class="relative px-4 sm:px-6 lg:px-8 pb-6">
            <div class="max-w-7xl mx-auto">
              <!-- Profile Image -->
              <div class="flex items-end -mt-20 mb-6">
                <div class="relative">
                  <img
                    :src="profileImageUrl"
                    :alt="profileUser.name"
                    class="h-40 w-40 rounded-full object-cover border-6 border-white shadow-xl bg-white"
                    @error="handleImageError"
                  >
                  <!-- Online Status Indicator (if current user is viewing their own profile) -->
                  <div v-if="isOwnProfile" class="absolute bottom-3 right-3 h-8 w-8 bg-green-400 border-3 border-white rounded-full"></div>
                </div>

                <!-- Action Buttons -->
                <div class="ml-auto flex space-x-3">
                  <router-link
                    v-if="isOwnProfile"
                    to="/settings"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors flex items-center"
                  >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    {{ $t('profile.editProfile') }}
                  </router-link>
                  <button
                    v-else
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center"
                    disabled
                  >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ $t('profile.followComingSoon') }}
                  </button>
                </div>
              </div>

              <!-- Name and Basic Info -->
              <div class="mb-6">
                <h1 class="text-4xl font-bold text-gray-900 mb-3">{{ profileUser.name }}</h1>
                <div class="flex flex-wrap items-center gap-6 text-gray-600">
                  <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    @{{ profileUser.username || $t('profile.noUsername') }}
                  </span>
                  <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0v6a2 2 0 002 2h2a2 2 0 002-2V7"></path>
                    </svg>
                    {{ profileUser.role || 'user' }}
                  </span>
                  <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $t('profile.joined') }} {{ formatDate(profileUser.created_at) }}
                  </span>
                </div>
              </div>

              <!-- Bio -->
              <div v-if="profileUser.bio" class="mb-8">
                <p class="text-lg text-gray-700 leading-relaxed max-w-3xl">{{ profileUser.bio }}</p>
              </div>
              <div v-else class="mb-8">
                <p class="text-gray-500 italic text-lg">{{ isOwnProfile ? $t('profile.addBio') : $t('profile.noBio') }}</p>
              </div>

              <!-- Stats Cards -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl">
                <!-- Eco Score -->
                <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                  <div class="text-3xl font-bold text-green-600 mb-2">{{ profileUser.eco_score || 0 }}</div>
                  <div class="text-base text-green-700 font-medium">{{ $t('profile.stats.ecoScore') }}</div>
                  <div class="text-sm text-green-600 mt-1">{{ $t('profile.stats.environmentalImpact') }}</div>
                </div>

                <!-- Posts Count -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 text-center">
                  <div class="text-3xl font-bold text-blue-600 mb-2">{{ postsCount }}</div>
                  <div class="text-base text-blue-700 font-medium">{{ $t('profile.stats.posts') }}</div>
                  <div class="text-sm text-blue-600 mt-1">{{ $t('profile.stats.sharedStories') }}</div>
                </div>

                <!-- Followers/Following (Coming Soon) -->
                <div class="bg-purple-50 border border-purple-200 rounded-xl p-6 text-center">
                  <div class="text-3xl font-bold text-purple-600 mb-2">0</div>
                  <div class="text-base text-purple-700 font-medium">{{ $t('profile.stats.connections') }}</div>
                  <div class="text-sm text-purple-600 mt-1">{{ $t('profile.comingSoon') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Posts Section -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
          <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-medium text-gray-900 flex items-center">
                  <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                  </svg>
                  {{ isOwnProfile ? $t('profile.posts.yourPosts') : $t('profile.posts.userPosts', { name: profileUser.name }) }}
                </h2>
              </div>

              <!-- Coming Soon Placeholder -->
              <div class="px-6 py-16 text-center">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-gray-100 mb-6">
                  <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                  </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-3">{{ $t('profile.posts.comingSoonTitle') }}</h3>
                <p class="text-gray-500 max-w-sm mx-auto mb-8">
                  {{ $t('profile.posts.comingSoonDescription') }}
                </p>
                <div class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-600 rounded-lg">
                  <svg class="w-5 h-5 mr-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  {{ $t('profile.posts.featureInDevelopment') }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../../stores/auth'
import DashboardLayout from '../../layouts/DashboardLayout.vue'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

// Reactive data
const profileUser = ref(null)
const isLoading = ref(true)
const error = ref('')
const postsCount = ref(0) // Will be dynamic when posts feature is implemented

// Computed properties
const isOwnProfile = computed(() => {
  return profileUser.value?.id === authStore.user?.id
})

const profileImageUrl = computed(() => {
  if (profileUser.value?.profile_image) {
    return `/storage/${profileUser.value.profile_image}`
  }
  return '/storage/profile-pictures/pp1.JPG'
})

// Methods
const fetchUserProfile = async () => {
  try {
    isLoading.value = true
    error.value = ''

    const userId = route.params.userId
    const response = await axios.get(`/users/${userId}/profile`)

    if (response.data.success) {
      profileUser.value = response.data.user
      postsCount.value = response.data.posts_count || 0
    } else {
      error.value = response.data.message || 'Failed to load profile'
    }
  } catch (err) {
    if (err.response?.status === 404) {
      error.value = err.response?.data?.message || 'User not found or profile is private.'
    } else {
      error.value = err.response?.data?.message || 'Failed to load profile.'
    }
  } finally {
    isLoading.value = false
  }
}

const handleImageError = (event) => {
  event.target.src = '/storage/profile-pictures/pp1.JPG'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const options = { year: 'numeric', month: 'long' }
  return date.toLocaleDateString('en-US', options)
}

// Initialize on mount
onMounted(() => {
  fetchUserProfile()
})

// Watch for route changes (if navigating between different profiles)
watch(() => route.params.userId, () => {
  if (route.params.userId) {
    fetchUserProfile()
  }
})
</script>
