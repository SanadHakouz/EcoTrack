import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createI18n } from 'vue-i18n';
import App from './components/App.vue';
import router from './router';

// Import axios setup (this sets up interceptors)
import './utils/axios';

// Import external JSON translation files
import en from './locales/en.json';
import de from './locales/de.json';

// Import auth store to initialize
import { useAuthStore } from './stores/auth';

// Create i18n instance with external files
const i18n = createI18n({
  legacy: false,
  locale: localStorage.getItem('preferred-language') || 'en',
  fallbackLocale: 'en',
  messages: {
    en,
    de
  }
});

// Create Vue app
const app = createApp(App);

// Create and use Pinia store
const pinia = createPinia();
app.use(pinia);

// Use i18n and router
app.use(i18n);
app.use(router);

// Initialize auth state on app start
const authStore = useAuthStore();
authStore.initAuth();

// Mount the app
app.mount('#app');