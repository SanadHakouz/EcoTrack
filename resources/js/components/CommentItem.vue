<template>
  <div class="flex space-x-3">
    <!-- User Avatar -->
    <router-link :to="`/profile/${comment.user.id}`" class="flex-shrink-0">
      <img
        :src="comment.user.profile_image
          ? `/storage/${comment.user.profile_image}`
          : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(comment.user.name) + '&background=059669&color=fff'"
        :alt="comment.user.name"
        class="w-8 h-8 rounded-full object-cover"
      >
    </router-link>

    <!-- Comment Content -->
    <div class="flex-1 min-w-0">
      <!-- Comment Header -->
      <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
        <div class="flex items-center space-x-2 mb-1">
          <router-link
            :to="`/profile/${comment.user.id}`"
            class="font-semibold text-sm text-gray-900 dark:text-white hover:text-green-600 dark:hover:text-green-400"
          >
            {{ comment.user.name }}
          </router-link>
          <span v-if="comment.user.username" class="text-xs text-gray-500 dark:text-gray-400">
            @{{ comment.user.username }}
          </span>
          <span class="text-xs text-gray-500 dark:text-gray-400">
            {{ formatDate(comment.created_at) }}
          </span>
          <span v-if="comment.is_edited" class="text-xs text-gray-500 dark:text-gray-400">
            • {{ $t('community.posts.edited') }}
          </span>
        </div>

        <!-- Comment Text -->
        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
          {{ comment.content }}
        </div>
      </div>

      <!-- Comment Actions -->
      <div class="flex items-center space-x-4 mt-2 text-xs">
        <button
          @click="toggleReply"
          class="text-gray-500 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 font-medium"
        >
          {{ $t('community.posts.reply') }}
        </button>

        <button
          v-if="comment.replies && comment.replies.length > 0"
          @click="toggleReplies"
          class="text-gray-500 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 font-medium"
        >
          {{ showReplies
            ? $t('community.posts.hideReplies')
            : $t('community.posts.viewReplies', { count: comment.replies.length })
          }}
        </button>

        <!-- Edit/Delete for comment owner -->
        <div v-if="isOwnComment" class="relative">
          <button
            @click="showMenu = !showMenu"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
            </svg>
          </button>
          <div v-if="showMenu" class="absolute right-0 mt-1 w-32 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-10">
            <button
              @click="editComment"
              class="block w-full text-left px-3 py-1 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600"
            >
              {{ $t('community.posts.edit') }}
            </button>
            <button
              @click="deleteComment"
              class="block w-full text-left px-3 py-1 text-xs text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600"
            >
              {{ $t('community.posts.delete') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Reply Form -->
      <div v-if="showReplyForm" class="mt-3">
        <div class="flex space-x-3">
          <img
            :src="authStore.user?.profile_image
              ? `/storage/${authStore.user.profile_image}`
              : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(authStore.user?.name || 'User') + '&background=059669&color=fff'"
            :alt="authStore.user?.name"
            class="w-6 h-6 rounded-full object-cover flex-shrink-0"
          >
          <div class="flex-1">
            <textarea
              v-model="replyContent"
              :placeholder="$t('community.posts.replyTo', { name: comment.user.name.split(' ')[0] })"
              class="w-full p-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
              rows="2"
              @keydown.ctrl.enter="submitReply"
              @keydown.esc="cancelReply"
            ></textarea>
            <div class="flex justify-between items-center mt-2">
              <span class="text-xs text-gray-500 dark:text-gray-400">
                Ctrl + Enter to post, Esc to cancel
              </span>
              <div class="flex space-x-2">
                <button
                  @click="cancelReply"
                  class="px-3 py-1 text-xs text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors"
                >
                  Cancel
                </button>
                <button
                  @click="submitReply"
                  :disabled="!replyContent.trim() || submittingReply"
                  class="px-3 py-1 text-xs bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded transition-colors"
                >
                  {{ submittingReply ? 'Posting...' : 'Reply' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Replies -->
      <div v-if="showReplies && comment.replies && comment.replies.length > 0" class="mt-4 space-y-3">
        <CommentItem
          v-for="reply in comment.replies"
          :key="reply.id"
          :comment="reply"
          :post-id="postId"
          :is-reply="true"
          @reply="handleNestedReply"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
import axios from '@/utils/axios'

const props = defineProps({
  comment: {
    type: Object,
    required: true
  },
  postId: {
    type: [String, Number],
    required: true
  },
  isReply: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['reply'])

const { t } = useI18n()
const authStore = useAuthStore()

// State
const showReplyForm = ref(false)
const showReplies = ref(false)
const showMenu = ref(false)
const replyContent = ref('')
const submittingReply = ref(false)

// Computed
const isOwnComment = computed(() => {
  return authStore.user && authStore.user.id === props.comment.user.id
})

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

const toggleReply = () => {
  showReplyForm.value = !showReplyForm.value
  if (!showReplyForm.value) {
    replyContent.value = ''
  }
}

const toggleReplies = () => {
  showReplies.value = !showReplies.value
}

const cancelReply = () => {
  showReplyForm.value = false
  replyContent.value = ''
}

const submitReply = async () => {
  if (!replyContent.value.trim() || submittingReply.value) return

  try {
    submittingReply.value = true

    const response = await axios.post(`/posts/${props.postId}/comments`, {
      content: replyContent.value.trim(),
      parent_id: props.comment.id
    })

    if (response.data.success) {
      // Add reply to local state
      if (!props.comment.replies) {
        props.comment.replies = []
      }
      props.comment.replies.push(response.data.comment)

      // Show replies if they were hidden
      showReplies.value = true

      // Reset form
      replyContent.value = ''
      showReplyForm.value = false

      // Emit to parent for count update
      emit('reply', props.comment.id, response.data.comment)
    }
  } catch (error) {
    console.error('Failed to submit reply:', error)
  } finally {
    submittingReply.value = false
  }
}

const handleNestedReply = (commentId, reply) => {
  // Pass up to parent
  emit('reply', commentId, reply)
}

const editComment = () => {
  // TODO: Implement edit comment functionality
  console.log('Edit comment:', props.comment.id)
  showMenu.value = false
}

const deleteComment = async () => {
  if (confirm('Are you sure you want to delete this comment?')) {
    try {
      // TODO: Implement delete comment API endpoint
      console.log('Delete comment:', props.comment.id)
      showMenu.value = false
    } catch (error) {
      console.error('Failed to delete comment:', error)
    }
  }
}

// Close menu when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)

  // Auto-expand replies if there are only a few
  if (props.comment.replies && props.comment.replies.length <= 2) {
    showReplies.value = true
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
