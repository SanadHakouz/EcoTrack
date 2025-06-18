<template>
  <DashboardLayout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
          <h1 class="text-2xl font-bold text-gray-900">{{ $t('settings.title') }}</h1>
          <p class="mt-1 text-sm text-gray-600">{{ $t('settings.subtitle') }}</p>
        </div>

        <!-- Profile Image Section -->
        <div class="px-6 py-6">
          <div class="flex items-center space-x-6">
            <div class="relative">
              <img
                :src="profileImageUrl"
                :alt="user?.name"
                class="h-24 w-24 rounded-full object-cover border-4 border-gray-200 shadow-sm"
                @error="handleImageError"
              >
              <button
                @click="triggerImageUpload"
                :disabled="isUploading"
                class="absolute bottom-0 right-0 bg-green-600 hover:bg-green-700 text-white rounded-full p-2 shadow-lg transition-colors disabled:bg-gray-400"
              >
                <svg v-if="!isUploading" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <svg v-else class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </button>
            </div>

            <div class="flex-1">
              <h3 class="text-lg font-medium text-gray-900">{{ $t('settings.profilePhoto.title') }}</h3>
              <p class="text-sm text-gray-500">{{ $t('settings.profilePhoto.description') }}</p>
              <div class="mt-2 flex space-x-3">
                <button
                  @click="triggerImageUpload"
                  :disabled="isUploading"
                  class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50"
                >
                  {{ isUploading ? $t('settings.profilePhoto.uploading') : $t('settings.profilePhoto.changePhoto') }}
                </button>
                <button
                  v-if="user?.profile_image"
                  @click="deleteProfileImage"
                  :disabled="isUploading"
                  class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50"
                >
                  {{ $t('settings.profilePhoto.remove') }}
                </button>
              </div>
            </div>
          </div>

          <!-- Hidden file input -->
          <input
            ref="fileInput"
            type="file"
            accept="image/*"
            class="hidden"
            @change="handleImageUpload"
          >
        </div>
      </div>

      <!-- Profile Information Form -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">{{ $t('settings.personalInfo.title') }}</h2>
          <p class="mt-1 text-sm text-gray-600">{{ $t('settings.personalInfo.subtitle') }}</p>
        </div>

        <form @submit.prevent="updateProfile" class="px-6 py-6 space-y-6">
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('settings.fields.fullName') }}
            </label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
              :placeholder="$t('settings.fields.fullNamePlaceholder')"
            >
          </div>

          <!-- Username -->
          <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('settings.fields.username') }}
            </label>
            <input
              id="username"
              v-model="form.username"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
              :placeholder="$t('settings.fields.usernamePlaceholder')"
            >
            <p class="mt-1 text-xs text-gray-500">{{ $t('settings.fields.usernameHint') }}</p>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('settings.fields.email') }}
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
              :placeholder="$t('settings.fields.emailPlaceholder')"
            >
          </div>

          <!-- Bio -->
          <div>
            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('settings.fields.bio') }}
            </label>
            <textarea
              id="bio"
              v-model="form.bio"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
              :placeholder="$t('settings.fields.bioPlaceholder')"
            ></textarea>
            <p class="mt-1 text-xs text-gray-500">{{ $t('settings.fields.bioHint') }}</p>
          </div>

          <!-- Phone -->
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('settings.fields.phone') }}
            </label>
            <input
              id="phone"
              v-model="form.phone"
              type="tel"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
              :placeholder="$t('settings.fields.phonePlaceholder')"
            >
          </div>

          <!-- Eco Score (Read-only) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('settings.fields.ecoScore') }}
            </label>
            <div class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md">
              <div class="flex items-center justify-between">
                <span class="text-2xl font-bold text-green-600">{{ user?.eco_score || 0 }}</span>
                <span class="text-sm text-gray-500">{{ $t('settings.fields.pointsEarned') }}</span>
              </div>
            </div>
            <p class="mt-1 text-xs text-gray-500">{{ $t('settings.fields.ecoScoreHint') }}</p>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="rounded-md bg-red-50 p-4">
            <div class="flex">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">{{ $t('settings.actions.updateError') }}</h3>
                <p class="mt-1 text-sm text-red-700">{{ error }}</p>
              </div>
            </div>
          </div>

          <!-- Success Message -->
          <div v-if="successMessage" class="rounded-md bg-green-50 p-4">
            <div class="flex">
              <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="isLoading"
              class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
            >
              <span v-if="isLoading">{{ $t('settings.actions.saving') }}</span>
              <span v-else>{{ $t('settings.actions.saveChanges') }}</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Account Actions -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-8">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">{{ $t('settings.accountActions.title') }}</h2>
          <p class="mt-1 text-sm text-gray-600">{{ $t('settings.accountActions.subtitle') }}</p>
        </div>

        <div class="px-6 py-6 space-y-4">
          <!-- Reset Password Button (Now Active) -->
          <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
            <div>
              <h3 class="text-sm font-medium text-gray-900">{{ $t('settings.accountActions.resetPassword.title') }}</h3>
              <p class="text-sm text-gray-500">{{ $t('settings.accountActions.resetPassword.description') }}</p>
            </div>
            <button
              @click="openPasswordResetModal"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors"
            >
              {{ $t('settings.passwordReset.resetPassword') }}
            </button>
          </div>

          <!-- Deactivate Account Button (Inactive) -->
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg opacity-60">
            <div>
              <h3 class="text-sm font-medium text-gray-900">{{ $t('settings.accountActions.deactivateAccount.title') }}</h3>
              <p class="text-sm text-gray-500">{{ $t('settings.accountActions.deactivateAccount.description') }}</p>
            </div>
            <button
              disabled
              class="bg-gray-300 text-gray-500 px-4 py-2 rounded-md text-sm font-medium cursor-not-allowed"
            >
              {{ $t('settings.accountActions.deactivateAccount.button') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Password Reset Modal -->
    <div v-if="showPasswordResetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">{{ $t('settings.passwordReset.title') }}</h3>
            <button @click="closePasswordResetModal" class="text-gray-400 hover:text-gray-600">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Modal Content -->
        <div class="px-6 py-4">
          <!-- Step 1: Request Reset Code -->
          <div v-if="resetStep === 1">
            <p class="text-sm text-gray-600 mb-4">{{ $t('settings.passwordReset.step1Description') }}</p>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ $t('settings.fields.email') }}
              </label>
              <input
                v-model="resetForm.email"
                type="email"
                :placeholder="user?.email"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :disabled="isResetting"
              >
            </div>

            <div v-if="resetError" class="mb-4 text-sm text-red-600">
              {{ resetError }}
            </div>

            <div class="flex justify-end space-x-3">
              <button
                @click="closePasswordResetModal"
                class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50"
                :disabled="isResetting"
              >
                {{ $t('settings.passwordReset.cancel') }}
              </button>
              <button
                @click="requestResetCode"
                :disabled="isResetting || !resetForm.email"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400"
              >
                {{ isResetting ? $t('settings.passwordReset.sending') : $t('settings.passwordReset.sendCode') }}
              </button>
            </div>
          </div>

          <!-- Step 2: Verify Code -->
          <div v-else-if="resetStep === 2">
            <p class="text-sm text-gray-600 mb-4">
              {{ $t('settings.passwordReset.step2Description', { email: resetForm.email }) }}
            </p>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ $t('settings.passwordReset.verificationCode') }}
              </label>
              <input
                v-model="resetForm.code"
                type="text"
                maxlength="4"
                :placeholder="$t('settings.passwordReset.codePlaceholder')"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-center text-2xl tracking-widest font-mono"
                :disabled="isResetting"
                @input="formatCode"
              >
            </div>

            <div v-if="resetError" class="mb-4 text-sm text-red-600">
              {{ resetError }}
            </div>

            <div class="flex justify-end space-x-3">
              <button
                @click="resetStep = 1"
                class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50"
                :disabled="isResetting"
              >
                {{ $t('settings.passwordReset.back') }}
              </button>
              <button
                @click="verifyCode"
                :disabled="isResetting || resetForm.code.length !== 4"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400"
              >
                {{ isResetting ? $t('settings.passwordReset.verifying') : $t('settings.passwordReset.verifyCode') }}
              </button>
            </div>
          </div>

          <!-- Step 3: Set New Password -->
          <div v-else-if="resetStep === 3">
            <p class="text-sm text-gray-600 mb-4">{{ $t('settings.passwordReset.step3Description') }}</p>

            <div class="space-y-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ $t('settings.passwordReset.newPassword') }}
                </label>
                <input
                  v-model="resetForm.password"
                  type="password"
                  :placeholder="$t('settings.passwordReset.newPasswordPlaceholder')"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :disabled="isResetting"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ $t('settings.passwordReset.confirmPassword') }}
                </label>
                <input
                  v-model="resetForm.password_confirmation"
                  type="password"
                  :placeholder="$t('settings.passwordReset.confirmPasswordPlaceholder')"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :disabled="isResetting"
                >
              </div>
            </div>

            <div v-if="resetError" class="mb-4 text-sm text-red-600">
              {{ resetError }}
            </div>

            <div class="flex justify-end space-x-3">
              <button
                @click="resetStep = 2"
                class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50"
                :disabled="isResetting"
              >
                {{ $t('settings.passwordReset.back') }}
              </button>
              <button
                @click="resetPassword"
                :disabled="isResetting || !resetForm.password || !resetForm.password_confirmation || resetForm.password !== resetForm.password_confirmation"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:bg-gray-400"
              >
                {{ isResetting ? $t('settings.passwordReset.updating') : $t('settings.passwordReset.updatePassword') }}
              </button>
            </div>
          </div>

          <!-- Success Step -->
          <div v-else-if="resetStep === 4">
            <div class="text-center py-4">
              <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('settings.passwordReset.success') }}</h3>
              <p class="text-sm text-gray-600 mb-4">{{ $t('settings.passwordReset.successDescription') }}</p>
              <button
                @click="closePasswordResetModal"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
              >
                {{ $t('settings.passwordReset.close') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../../../stores/auth'
import DashboardLayout from '../../layouts/DashboardLayout.vue'
import axios from 'axios'

const { t } = useI18n()
const authStore = useAuthStore()

// Reactive data
const form = ref({
  name: '',
  username: '',
  email: '',
  bio: '',
  phone: ''
})

const isLoading = ref(false)
const isUploading = ref(false)
const error = ref('')
const successMessage = ref('')
const fileInput = ref(null)

// Password reset modal data
const showPasswordResetModal = ref(false)
const resetStep = ref(1) // 1: email, 2: code, 3: password, 4: success
const isResetting = ref(false)
const resetError = ref('')
const resetForm = ref({
  email: '',
  code: '',
  password: '',
  password_confirmation: ''
})

// Computed properties
const user = computed(() => authStore.user)

const profileImageUrl = computed(() => {
  if (user.value?.profile_image) {
    return `/storage/${user.value.profile_image}`
  }
  return '/storage/profile-pictures/pp1.JPG'
})

// Watchers
watch(user, (newUser) => {
  if (newUser) {
    populateForm()
  }
}, { immediate: true })

// Methods
const populateForm = () => {
  if (user.value) {
    form.value = {
      name: user.value.name || '',
      username: user.value.username || '',
      email: user.value.email || '',
      bio: user.value.bio || '',
      phone: user.value.phone || ''
    }
  }
}

const updateProfile = async () => {
  isLoading.value = true
  error.value = ''
  successMessage.value = ''

  try {
    const response = await axios.put('/user/profile', form.value)

    if (response.data.success) {
      // Update auth store with new user data
      authStore.updateUser(response.data.user)
      successMessage.value = t('settings.actions.updateSuccess')

      // Clear success message after 3 seconds
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    }
  } catch (err) {
    error.value = err.response?.data?.message || t('settings.errors.updateFailed')
  } finally {
    isLoading.value = false
  }
}

const triggerImageUpload = () => {
  fileInput.value?.click()
}

const handleImageUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  isUploading.value = true
  error.value = ''

  try {
    const formData = new FormData()
    formData.append('avatar', file)

    const response = await axios.post('/user/profile/avatar', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    if (response.data.success) {
      // Update auth store with new profile image
      authStore.updateUser({ profile_image: response.data.profile_image })
      successMessage.value = t('settings.profilePhoto.uploadSuccess')

      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    }
  } catch (err) {
    error.value = err.response?.data?.message || t('settings.errors.uploadFailed')
  } finally {
    isUploading.value = false
    // Clear file input
    event.target.value = ''
  }
}

const deleteProfileImage = async () => {
  if (!confirm(t('settings.profilePhoto.removeConfirm'))) {
    return
  }

  isUploading.value = true
  error.value = ''

  try {
    const response = await axios.delete('/user/profile/avatar')

    if (response.data.success) {
      // Update auth store
      authStore.updateUser({ profile_image: null })
      successMessage.value = t('settings.profilePhoto.removeSuccess')

      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    }
  } catch (err) {
    error.value = err.response?.data?.message || t('settings.errors.removeFailed')
  } finally {
    isUploading.value = false
  }
}

const handleImageError = (event) => {
  event.target.src = '/storage/profile-pictures/pp1.JPG'
}

// Password reset methods
const openPasswordResetModal = () => {
  showPasswordResetModal.value = true
  resetStep.value = 1
  resetError.value = ''
  resetForm.value = {
    email: user.value?.email || '',
    code: '',
    password: '',
    password_confirmation: ''
  }
}

const closePasswordResetModal = () => {
  showPasswordResetModal.value = false
  resetStep.value = 1
  resetError.value = ''
  resetForm.value = {
    email: '',
    code: '',
    password: '',
    password_confirmation: ''
  }
}

const requestResetCode = async () => {
  isResetting.value = true
  resetError.value = ''

  try {
    const response = await axios.post('/auth/password/request-reset', {
      email: resetForm.value.email
    })

    if (response.data.success) {
      resetStep.value = 2
    }
  } catch (err) {
    resetError.value = err.response?.data?.message || t('settings.passwordReset.errors.sendFailed')
  } finally {
    isResetting.value = false
  }
}

const formatCode = (event) => {
  // Only allow numbers
  resetForm.value.code = event.target.value.replace(/[^0-9]/g, '').substring(0, 4)
}

const verifyCode = async () => {
  isResetting.value = true
  resetError.value = ''

  try {
    const response = await axios.post('/auth/password/verify-code', {
      email: resetForm.value.email,
      code: resetForm.value.code
    })

    if (response.data.success) {
      resetStep.value = 3
    }
  } catch (err) {
    resetError.value = err.response?.data?.message || t('settings.passwordReset.errors.verifyFailed')
  } finally {
    isResetting.value = false
  }
}

const resetPassword = async () => {
  isResetting.value = true
  resetError.value = ''

  try {
    const response = await axios.post('/auth/password/reset', {
      email: resetForm.value.email,
      code: resetForm.value.code,
      password: resetForm.value.password,
      password_confirmation: resetForm.value.password_confirmation
    })

    if (response.data.success) {
      resetStep.value = 4
    }
  } catch (err) {
    resetError.value = err.response?.data?.message || t('settings.passwordReset.errors.resetFailed')
  } finally {
    isResetting.value = false
  }
}

// Initialize on mount
onMounted(() => {
  populateForm()
})
</script>
