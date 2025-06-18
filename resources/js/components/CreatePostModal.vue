<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-600">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
          {{ $t('community.createPost.title') }}
        </h2>
        <button
          @click="closeModal"
          class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="p-6 max-h-[calc(90vh-140px)] overflow-y-auto">
        <form @submit.prevent="createPost">
          <!-- User Info -->
          <div class="flex items-center space-x-3 mb-6">
            <img
              :src="authStore.user?.profile_image
                ? `/storage/${authStore.user.profile_image}`
                : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(authStore.user?.name || 'User') + '&background=059669&color=fff'"
              :alt="authStore.user?.name"
              class="w-12 h-12 rounded-full object-cover"
            >
            <div>
              <div class="font-semibold text-gray-900 dark:text-white">
                {{ authStore.user?.name }}
              </div>
              <div class="text-sm text-gray-500 dark:text-gray-400">
                Public post
              </div>
            </div>
          </div>

          <!-- Post Title -->
          <div class="mb-4">
            <label for="post-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ $t('community.createPost.postTitle') }}
            </label>
            <input
              id="post-title"
              v-model="form.title"
              type="text"
              :placeholder="$t('community.createPost.postTitlePlaceholder')"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
              :class="{ 'border-red-300': errors.title }"
              required
            >
            <p v-if="errors.title" class="mt-2 text-sm text-red-600 dark:text-red-400">
              {{ errors.title[0] }}
            </p>
          </div>

          <!-- Post Content -->
          <div class="mb-4">
            <label for="post-content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ $t('community.createPost.content') }}
            </label>
            <textarea
              id="post-content"
              v-model="form.content"
              :placeholder="$t('community.createPost.contentPlaceholder')"
              rows="6"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
              :class="{ 'border-red-300': errors.content }"
              required
            ></textarea>
            <div class="flex justify-between items-center mt-2">
              <p v-if="errors.content" class="text-sm text-red-600 dark:text-red-400">
                {{ errors.content[0] }}
              </p>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ form.content.length }}/5000
              </p>
            </div>
          </div>

          <!-- Image Upload -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ $t('community.createPost.addImage') }}
            </label>

            <!-- Upload Area -->
            <div
              v-if="!selectedImage"
              @click="triggerImageSelect"
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="handleImageDrop"
              :class="[
                'border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors',
                isDragging
                  ? 'border-green-400 bg-green-50 dark:bg-green-900/20'
                  : 'border-gray-300 dark:border-gray-600 hover:border-green-400 hover:bg-gray-50 dark:hover:bg-gray-700'
              ]"
            >
              <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <p class="text-gray-600 dark:text-gray-400 mb-2">
                Click to upload or drag and drop
              </p>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                PNG, JPG, GIF up to 2MB
              </p>
            </div>

            <!-- Selected Image Preview -->
            <div v-if="selectedImage" class="relative">
              <img
                :src="imagePreviewUrl"
                alt="Post image preview"
                class="w-full max-h-64 object-cover rounded-lg"
              >
              <button
                @click="removeImage"
                type="button"
                class="absolute top-2 right-2 p-2 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <!-- Hidden file input -->
            <input
              ref="imageInput"
              type="file"
              accept="image/*"
              class="hidden"
              @change="handleImageSelect"
            >

            <p v-if="errors.image" class="mt-2 text-sm text-red-600 dark:text-red-400">
              {{ errors.image[0] }}
            </p>
          </div>

          <!-- Error Messages -->
          <div v-if="generalError" class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-600 dark:text-red-400">
              {{ generalError }}
            </p>
          </div>
        </form>
      </div>

      <!-- Modal Footer -->
      <div class="flex items-center justify-between p-6 border-t border-gray-200 dark:border-gray-600">
        <div class="flex items-center space-x-4">
          <button
            @click="triggerImageSelect"
            type="button"
            class="flex items-center space-x-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>Photo</span>
          </button>
        </div>

        <div class="flex items-center space-x-3">
          <button
            @click="closeModal"
            type="button"
            class="px-6 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
          >
            {{ $t('community.createPost.cancel') }}
          </button>
          <button
            @click="createPost"
            :disabled="!canPublish || isSubmitting"
            class="px-6 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg transition-colors flex items-center space-x-2"
          >
            <svg v-if="isSubmitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
              <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75"></path>
            </svg>
            <span>
              {{ isSubmitting ? $t('community.createPost.publishing') : $t('community.createPost.publish') }}
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
import axios from '@/utils/axios'

const emit = defineEmits(['close', 'created'])

const { t } = useI18n()
const authStore = useAuthStore()

// State
const form = ref({
  title: '',
  content: '',
  is_published: true
})

const selectedImage = ref(null)
const imagePreviewUrl = ref(null)
const isDragging = ref(false)
const isSubmitting = ref(false)
const errors = ref({})
const generalError = ref('')
const imageInput = ref(null)

// Computed
const canPublish = computed(() => {
  return form.value.title.trim() && form.value.content.trim() && !isSubmitting.value
})

// Methods
const closeModal = () => {
  if (isSubmitting.value) return
  emit('close')
}

const triggerImageSelect = () => {
  imageInput.value?.click()
}

const handleImageSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    validateAndSetImage(file)
  }
}

const handleImageDrop = (event) => {
  isDragging.value = false
  const file = event.dataTransfer.files[0]
  if (file && file.type.startsWith('image/')) {
    validateAndSetImage(file)
  }
}

const validateAndSetImage = (file) => {
  // Validate file size (2MB)
  if (file.size > 2 * 1024 * 1024) {
    errors.value.image = ['Image must be less than 2MB']
    return
  }

  // Validate file type
  if (!file.type.startsWith('image/')) {
    errors.value.image = ['File must be an image']
    return
  }

  selectedImage.value = file
  imagePreviewUrl.value = URL.createObjectURL(file)
  errors.value.image = null
}

const removeImage = () => {
  selectedImage.value = null
  if (imagePreviewUrl.value) {
    URL.revokeObjectURL(imagePreviewUrl.value)
    imagePreviewUrl.value = null
  }
  if (imageInput.value) {
    imageInput.value.value = ''
  }
}

const createPost = async () => {
  if (!canPublish.value) return

  try {
    isSubmitting.value = true
    errors.value = {}
    generalError.value = ''

    const formData = new FormData()
    formData.append('title', form.value.title.trim())
    formData.append('content', form.value.content.trim())
    formData.append('is_published', form.value.is_published ? '1' : '0')

    if (selectedImage.value) {
      formData.append('image', selectedImage.value)
    }

    console.log('Creating post with data:', {
      title: form.value.title.trim(),
      content: form.value.content.trim(),
      is_published: form.value.is_published ? '1' : '0',
      hasImage: !!selectedImage.value
    })

    const response = await axios.post('/posts', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    if (response.data.success) {
      // Reset form
      form.value = {
        title: '',
        content: '',
        is_published: true
      }
      selectedImage.value = null
      if (imagePreviewUrl.value) {
        URL.revokeObjectURL(imagePreviewUrl.value)
        imagePreviewUrl.value = null
      }

      emit('created', response.data.post)
    }
  } catch (error) {
    console.error('Failed to create post:', error)
    console.error('Error response:', error.response?.data)

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      if (error.response.data.message) {
        generalError.value = error.response.data.message
      }
      console.log('Validation errors:', errors.value)
    } else {
      generalError.value = error.response?.data?.message || 'Failed to create post. Please try again.'
    }
  } finally {
    isSubmitting.value = false
  }
}

// Handle escape key
const handleEscape = (event) => {
  if (event.key === 'Escape') {
    closeModal()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
  document.body.style.overflow = 'hidden'
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
  document.body.style.overflow = ''

  // Clean up image URL
  if (imagePreviewUrl.value) {
    URL.revokeObjectURL(imagePreviewUrl.value)
  }
})
</script>
