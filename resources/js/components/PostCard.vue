<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
    <!-- Post Header -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-3">
        <router-link :to="`/profile/${post.user.id}`" class="flex-shrink-0">
          <img
            :src="post.user.profile_image
              ? `/storage/${post.user.profile_image}`
              : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(post.user.name) + '&background=059669&color=fff'"
            :alt="post.user.name"
            class="w-12 h-12 rounded-full object-cover"
          >
        </router-link>
        <div>
          <div class="flex items-center space-x-2">
            <router-link
              :to="`/profile/${post.user.id}`"
              class="font-semibold text-gray-900 dark:text-white hover:text-green-600 dark:hover:text-green-400"
            >
              {{ post.user.name }}
            </router-link>
            <span v-if="post.user.username" class="text-gray-500 dark:text-gray-400 text-sm">
              @{{ post.user.username }}
            </span>
          </div>
          <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <span>{{ formatDate(post.created_at) }}</span>
            <span v-if="post.is_edited" class="text-xs">
              • {{ $t('community.posts.edited') }}
            </span>
          </div>
        </div>
      </div>

      <!-- Post Actions Menu (for post owner) -->
      <div v-if="isOwnPost" class="relative">
        <button
          @click="showMenu = !showMenu"
          class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
          </svg>
        </button>
        <div v-if="showMenu" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-10">
          <button
            @click="editPost"
            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600"
          >
            {{ $t('community.posts.edit') }}
          </button>
          <button
            @click="deletePost"
            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600"
          >
            {{ $t('community.posts.delete') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Post Content -->
    <div class="mb-4">
      <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
        {{ post.title }}
      </h3>
      <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
        {{ post.content }}
      </div>
    </div>

    <!-- Post Image -->
    <div v-if="post.image" class="mb-4">
      <img
        :src="`/storage/${post.image}`"
        :alt="post.title"
        class="w-full max-h-96 object-cover rounded-lg cursor-pointer"
        @click="showImageModal = true"
      >
    </div>

    <!-- Reactions Bar -->
    <div class="flex items-center justify-between mb-4 pt-4 border-t border-gray-200 dark:border-gray-600">
      <div class="flex items-center space-x-4">
        <!-- Reaction Button -->
        <div class="relative">
          <button
            @click="showReactions = !showReactions"
            :class="[
              'flex items-center space-x-2 px-3 py-2 rounded-lg transition-colors',
              userReaction
                ? 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400'
                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
            ]"
          >
            <span v-if="userReaction" class="text-lg">{{ getReactionIcon(userReaction.type) }}</span>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V9a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
            </svg>
            <span class="text-sm font-medium">
              {{ post.reactions_count || 0 }}
            </span>
          </button>

          <!-- Reactions Popup -->
          <div v-if="showReactions" class="absolute bottom-full left-0 mb-2 bg-white dark:bg-gray-700 rounded-lg shadow-lg p-2 flex space-x-1 z-20">
            <button
              v-for="(reaction, type) in availableReactions"
              :key="type"
              @click="toggleReaction(type)"
              :class="[
                'p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors',
                userReaction?.type === type ? 'bg-green-100 dark:bg-green-900' : ''
              ]"
              :title="reaction.label"
            >
              <span class="text-xl">{{ reaction.icon }}</span>
            </button>
          </div>
        </div>

        <!-- Comments Button -->
        <button
          @click="toggleComments"
          class="flex items-center space-x-2 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.439L3 21l2.439-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
          </svg>
          <span class="text-sm font-medium">{{ post.comments_count || 0 }}</span>
        </button>
      </div>

      <!-- Reaction Summary -->
      <div v-if="Object.keys(post.reaction_counts || {}).length > 0" class="flex items-center space-x-1">
        <div v-for="(count, type) in post.reaction_counts" :key="type" class="flex items-center">
          <span class="text-sm">{{ getReactionIcon(type) }}</span>
          <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ count }}</span>
        </div>
      </div>
    </div>

    <!-- Comments Section -->
    <div v-if="showCommentsSection" class="border-t border-gray-200 dark:border-gray-600 pt-4">
      <!-- Add Comment Form -->
      <div class="flex items-start space-x-3 mb-4">
        <img
          :src="authStore.user?.profile_image
            ? `/storage/${authStore.user.profile_image}`
            : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(authStore.user?.name || 'User') + '&background=059669&color=fff'"
          :alt="authStore.user?.name"
          class="w-8 h-8 rounded-full object-cover flex-shrink-0"
        >
        <div class="flex-1">
          <textarea
            v-model="newComment"
            :placeholder="$t('community.posts.commentPlaceholder')"
            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
            rows="2"
            @keydown.ctrl.enter="addComment"
          ></textarea>
          <div class="flex justify-between items-center mt-2">
            <span class="text-xs text-gray-500 dark:text-gray-400">Ctrl + Enter to post</span>
            <button
              @click="addComment"
              :disabled="!newComment.trim() || addingComment"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg transition-colors text-sm"
            >
              {{ addingComment ? $t('community.posts.posting') : $t('community.posts.postComment') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Comments List -->
      <div v-if="comments.length > 0" class="space-y-4">
        <CommentItem
          v-for="comment in comments"
          :key="comment.id"
          :comment="comment"
          :post-id="post.id"
          @reply="handleReply"
        />
      </div>

      <!-- Load More Comments -->
      <div v-if="commentsLoading" class="text-center py-4">
        <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-green-600"></div>
      </div>
    </div>

    <!-- Image Modal -->
    <div v-if="showImageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" @click="showImageModal = false">
      <div class="max-w-4xl max-h-full p-4">
        <img
          :src="`/storage/${post.image}`"
          :alt="post.title"
          class="max-w-full max-h-full object-contain"
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
import CommentItem from '@/components/CommentItem.vue'
import axios from '@/utils/axios'

const props = defineProps({
  post: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['reaction', 'comment', 'edit', 'delete'])

const { t } = useI18n()
const authStore = useAuthStore()

// State
const showMenu = ref(false)
const showReactions = ref(false)
const showCommentsSection = ref(false)
const showImageModal = ref(false)
const newComment = ref('')
const addingComment = ref(false)
const comments = ref([])
const commentsLoading = ref(false)

// Computed
const isOwnPost = computed(() => {
  return authStore.user && authStore.user.id === props.post.user.id
})

const userReaction = computed(() => {
  return props.post.user_reaction
})

const availableReactions = {
  'like': { icon: '👍', label: 'Like' },
  'love': { icon: '❤️', label: 'Love' },
  'eco_love': { icon: '🌱', label: 'Eco Love' },
  'recycle': { icon: '♻️', label: 'Recycle' },
  'earth_day': { icon: '🌍', label: 'Earth Day' },
  'green_energy': { icon: '⚡', label: 'Green Energy' },
  'dislike': { icon: '👎', label: 'Dislike' }
}

// Methods
const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))

  if (diffInHours < 1) {
    return t('community.posts.justNow')
  } else if (diffInHours < 24) {
    return t('community.posts.timeAgo', { time: `${diffInHours}h` })
  } else {
    const diffInDays = Math.floor(diffInHours / 24)
    if (diffInDays < 7) {
      return t('community.posts.timeAgo', { time: `${diffInDays}d` })
    } else {
      return date.toLocaleDateString()
    }
  }
}

const getReactionIcon = (type) => {
  return availableReactions[type]?.icon || '👍'
}

const toggleReaction = (type) => {
  emit('reaction', props.post.id, type)
  showReactions.value = false
}

const toggleComments = () => {
  showCommentsSection.value = !showCommentsSection.value
  if (showCommentsSection.value && comments.value.length === 0) {
    loadComments()
  }
}

const loadComments = async () => {
  try {
    commentsLoading.value = true
    const response = await axios.get(`/posts/${props.post.id}/comments`)

    if (response.data.success) {
      comments.value = response.data.comments
    }
  } catch (error) {
    console.error('Failed to load comments:', error)
  } finally {
    commentsLoading.value = false
  }
}

const addComment = async () => {
  if (!newComment.value.trim() || addingComment.value) return

  try {
    addingComment.value = true

    const commentData = {
      content: newComment.value.trim()
    }

    emit('comment', props.post.id, commentData)

    // Add comment to local state
    const response = await axios.post(`/posts/${props.post.id}/comments`, commentData)
    if (response.data.success) {
      comments.value.push(response.data.comment)
      newComment.value = ''
    }
  } catch (error) {
    console.error('Failed to add comment:', error)
  } finally {
    addingComment.value = false
  }
}

const handleReply = (commentId, content) => {
  // Handle reply to comment
  console.log('Reply to comment:', commentId, content)
}

const editPost = () => {
  emit('edit', props.post)
  showMenu.value = false
}

const deletePost = () => {
  emit('delete', props.post.id)
  showMenu.value = false
}

// Close menus when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showMenu.value = false
    showReactions.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
