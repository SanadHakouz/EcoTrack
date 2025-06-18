<template>
  <DashboardLayout>
    <div class="max-w-4xl mx-auto px-4 py-6 space-y-6">
      <!-- Header -->
      <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
          {{ $t('community.title') }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
          {{ $t('community.subtitle') }}
        </p>
      </div>

      <!-- Create Post Section -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition-all duration-300">
        <!-- What's on your mind trigger -->
        <div v-if="!showCreateForm" class="p-6">
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
              <img
                :src="authStore.user?.profile_image
                  ? `/storage/${authStore.user.profile_image}`
                  : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(authStore.user?.name || 'User') + '&background=059669&color=fff'"
                :alt="authStore.user?.name"
                class="w-12 h-12 rounded-full object-cover"
              >
            </div>
            <div class="flex-1">
              <button
                @click="openCreateForm"
                class="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-600 transition-colors"
              >
                <span class="text-gray-500 dark:text-gray-400">
                  {{ authStore.user?.name
                    ? $t('community.whatsOnYourMind', { name: authStore.user.name.split(' ')[0] })
                    : $t('community.whatsOnYourMindDefault')
                  }}
                </span>
              </button>
            </div>
          </div>
        </div>

        <!-- Inline Create Post Form -->
        <div v-if="showCreateForm" ref="createFormRef" class="transition-all duration-300 ease-in-out">
          <div class="p-6 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ $t('community.createPost.title') }}
              </h3>
              <button
                @click="closeCreateForm"
                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <!-- User Info -->
            <div class="flex items-center space-x-3 mb-4">
              <img
                :src="authStore.user?.profile_image
                  ? `/storage/${authStore.user.profile_image}`
                  : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(authStore.user?.name || 'User') + '&background=059669&color=fff'"
                :alt="authStore.user?.name"
                class="w-10 h-10 rounded-full object-cover"
              >
              <div>
                <div class="font-medium text-gray-900 dark:text-white text-sm">
                  {{ authStore.user?.name }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                  Public post
                </div>
              </div>
            </div>

            <form @submit.prevent="createPost">
              <!-- Post Title -->
              <div class="mb-4">
                <input
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
                <textarea
                  v-model="form.content"
                  :placeholder="$t('community.createPost.contentPlaceholder')"
                  rows="4"
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  :class="{ 'border-red-300': errors.content }"
                  required
                ></textarea>
                <div class="flex justify-between items-center mt-2">
                  <p v-if="errors.content" class="text-sm text-red-600 dark:text-red-400">
                    {{ errors.content[0] }}
                  </p>
                  <p class="text-sm text-gray-500 dark:text-gray-400 ml-auto">
                    {{ form.content.length }}/5000
                  </p>
                </div>
              </div>

              <!-- Image Upload -->
              <div v-if="selectedImage" class="mb-4">
                <div class="relative">
                  <img
                    :src="imagePreviewUrl"
                    alt="Post image preview"
                    class="w-full max-h-64 object-cover rounded-lg"
                  >
                  <button
                    @click="removeImage"
                    type="button"
                    class="absolute top-2 right-2 p-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Error Messages -->
              <div v-if="generalError" class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <p class="text-sm text-red-600 dark:text-red-400">
                  {{ generalError }}
                </p>
              </div>

              <!-- Actions -->
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <button
                    @click="triggerImageSelect"
                    type="button"
                    class="flex items-center space-x-2 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-sm">Photo</span>
                  </button>
                </div>

                <div class="flex items-center space-x-3">
                  <button
                    @click="closeCreateForm"
                    type="button"
                    class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                  >
                    {{ $t('community.createPost.cancel') }}
                  </button>
                  <button
                    type="submit"
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

              <!-- Hidden file input -->
              <input
                ref="imageInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleImageSelect"
              >
            </form>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap gap-2">
        <button
          v-for="filter in filters"
          :key="filter.key"
          @click="currentFilter = filter.key"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition-colors',
            currentFilter === filter.key
              ? 'bg-green-600 text-white'
              : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600'
          ]"
        >
          {{ filter.label }}
        </button>
      </div>

      <!-- Posts Feed -->
      <div class="space-y-6">
        <!-- Loading State -->
        <div v-if="loading" class="space-y-6">
          <div v-for="i in 3" :key="i" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 animate-pulse">
            <div class="flex items-center space-x-4 mb-4">
              <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
              <div class="flex-1">
                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/4 mb-2"></div>
                <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/6"></div>
              </div>
            </div>
            <div class="space-y-2">
              <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
              <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
            </div>
          </div>
        </div>

        <!-- No Posts State -->
        <div v-else-if="!loading && posts.length === 0" class="text-center py-12">
          <div class="max-w-sm mx-auto">
            <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
              <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.439L3 21l2.439-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
              {{ $t('community.posts.noPostsYet') }}
            </h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
              {{ $t('community.posts.noPostsDescription') }}
            </p>
            <button
              @click="openCreateForm"
              class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              {{ $t('community.posts.writeFirst') }}
            </button>
          </div>
        </div>

        <!-- Posts List -->
        <PostCard
          v-for="post in posts"
          :key="post.id"
          :post="post"
          @reaction="handleReaction"
          @comment="handleComment"
          @edit="handleEditPost"
          @delete="handleDeletePost"
        />

        <!-- Load More Button -->
        <div v-if="pagination.current_page < pagination.last_page" class="text-center">
          <button
            @click="loadMorePosts"
            :disabled="loadingMore"
            class="px-6 py-3 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 rounded-lg transition-colors disabled:opacity-50"
          >
            <span v-if="loadingMore">{{ $t('community.posts.loading') }}</span>
            <span v-else>Load More Posts</span>
          </button>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted, computed, watch, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
import DashboardLayout from '@/components/layouts/DashboardLayout.vue'
import PostCard from '@/components/PostCard.vue'
import axios from '@/utils/axios'

const { t } = useI18n()
const authStore = useAuthStore()

// State
const posts = ref([])
const loading = ref(true)
const loadingMore = ref(false)
const showCreatePost = ref(false)
const currentFilter = ref('latest')
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0
})
const showCreateForm = ref(false)
const createFormRef = ref(null)
const imageInput = ref(null)
const form = ref({
  title: '',
  content: ''
})
const selectedImage = ref(null)
const imagePreviewUrl = ref(null)
const errors = ref({})
const generalError = ref('')
const isSubmitting = ref(false)

// Filters
const filters = computed(() => [
  { key: 'latest', label: t('community.filters.latest') },
  { key: 'popular', label: t('community.filters.popular') },
  { key: 'my_posts', label: t('community.filters.myPosts') },
  { key: 'following', label: t('community.filters.following') }
])

const canPublish = computed(() => {
  return form.value.title.trim() && form.value.content.trim() && !isSubmitting.value
})

// Methods
const loadPosts = async (page = 1, append = false) => {
  try {
    if (!append) loading.value = true
    else loadingMore.value = true

    const response = await axios.get('/posts', {
      params: {
        page,
        filter: currentFilter.value
      }
    })

    if (response.data.success) {
      if (append) {
        posts.value.push(...response.data.posts)
      } else {
        posts.value = response.data.posts
      }
      pagination.value = response.data.pagination
    }
  } catch (error) {
    console.error('Failed to load posts:', error)
  } finally {
    loading.value = false
    loadingMore.value = false
  }
}

const loadMorePosts = () => {
  if (pagination.value.current_page < pagination.value.last_page) {
    loadPosts(pagination.value.current_page + 1, true)
  }
}

const handlePostCreated = (newPost) => {
  posts.value.unshift(newPost)
  showCreatePost.value = false
}

const handleReaction = async (postId, reactionType) => {
  try {
    const response = await axios.post(`/posts/${postId}/reactions`, {
      type: reactionType
    })

    if (response.data.success) {
      // Update the post in the list
      const postIndex = posts.value.findIndex(p => p.id === postId)
      if (postIndex !== -1) {
        posts.value[postIndex].user_reaction = response.data.user_reaction
        posts.value[postIndex].reaction_counts = response.data.reaction_counts
        posts.value[postIndex].reactions_count = response.data.total_reactions
      }
    }
  } catch (error) {
    console.error('Failed to toggle reaction:', error)
  }
}

const handleComment = async (postId, commentData) => {
  try {
    const response = await axios.post(`/posts/${postId}/comments`, commentData)

    if (response.data.success) {
      // Update the post's comment count
      const postIndex = posts.value.findIndex(p => p.id === postId)
      if (postIndex !== -1) {
        posts.value[postIndex].comments_count += 1
        // Add comment to the post's comments array if it exists
        if (posts.value[postIndex].top_level_comments) {
          posts.value[postIndex].top_level_comments.push(response.data.comment)
        }
      }
    }
  } catch (error) {
    console.error('Failed to add comment:', error)
  }
}

const handleEditPost = (post) => {
  // TODO: Implement edit post modal
  console.log('Edit post:', post)
}

const handleDeletePost = async (postId) => {
  if (confirm('Are you sure you want to delete this post?')) {
    try {
      const response = await axios.delete(`/posts/${postId}`)

      if (response.data.success) {
        posts.value = posts.value.filter(p => p.id !== postId)
      }
    } catch (error) {
      console.error('Failed to delete post:', error)
    }
  }
}

const openCreateForm = async () => {
  showCreateForm.value = true
  await nextTick()
  if (createFormRef.value) {
    createFormRef.value.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

const closeCreateForm = () => {
  showCreateForm.value = false
  resetForm()
}

const resetForm = () => {
  form.value = {
    title: '',
    content: ''
  }
  selectedImage.value = null
  if (imagePreviewUrl.value) {
    URL.revokeObjectURL(imagePreviewUrl.value)
    imagePreviewUrl.value = null
  }
  errors.value = {}
  generalError.value = ''
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
    formData.append('is_published', '1')

    if (selectedImage.value) {
      formData.append('image', selectedImage.value)
    }

    console.log('Creating post with data:', {
      title: form.value.title.trim(),
      content: form.value.content.trim(),
      hasImage: !!selectedImage.value
    })

    const response = await axios.post('/posts', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    if (response.data.success) {
      posts.value.unshift(response.data.post)
      closeCreateForm()
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

const triggerImageSelect = () => {
  imageInput.value?.click()
}

const handleImageSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
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

// Watch filter changes
watch(currentFilter, () => {
  loadPosts()
})

// Lifecycle
onMounted(() => {
  loadPosts()
})
</script>
