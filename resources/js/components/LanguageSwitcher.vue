<template>
  <div class="relative h-full">
    <button
      @click="toggleDropdown"
      class="flex items-center justify-center w-full h-full px-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 hover:text-gray-900 transition-colors duration-200 border border-gray-300 hover:border-gray-400"
    >
      <span class="text-base">{{ currentFlag }}</span>
      <span class="text-xs font-medium ml-1 uppercase">{{ currentLanguageCode }}</span>
      <svg
        class="w-3 h-3 ml-1 transform transition-transform duration-200"
        :class="{ 'rotate-180': isOpen }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
      >
        <div class="py-1">
          <button
            v-for="language in languages"
            :key="language.code"
            @click="changeLanguage(language.code)"
            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-150"
            :class="{ 'bg-green-50 text-green-600 font-medium': $i18n.locale === language.code }"
          >
            <span class="text-xl mr-3">{{ language.flag }}</span>
            <span>{{ language.name }}</span>
            <svg
              v-if="$i18n.locale === language.code"
              class="w-4 h-4 ml-auto text-green-600"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                clip-rule="evenodd"
              ></path>
            </svg>
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();
const isOpen = ref(false);

const languages = [
  { code: 'en', name: 'English', flag: '🇺🇸' },
  { code: 'de', name: 'Deutsch', flag: '🇩🇪' }
];

const currentLanguage = computed(() => {
  return languages.find(lang => lang.code === locale.value) || languages[0];
});

const currentFlag = computed(() => currentLanguage.value.flag);
const currentLanguageCode = computed(() => currentLanguage.value.code.toUpperCase());

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const changeLanguage = (newLocale) => {
  locale.value = newLocale;
  isOpen.value = false;

  // Store preference in localStorage
  localStorage.setItem('preferred-language', newLocale);
};

const closeDropdown = (event) => {
  if (!event.target.closest('.relative')) {
    isOpen.value = false;
  }
};

// Load saved language preference on mount
onMounted(() => {
  const savedLanguage = localStorage.getItem('preferred-language');
  if (savedLanguage && languages.some(lang => lang.code === savedLanguage)) {
    locale.value = savedLanguage;
  }

  document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown);
});
</script>
