import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// Import your existing components
import LandingPage from '../components/LandingPage.vue'
import RegularUserDashboard from '../components/pages/user/RegularUserDashboard.vue'
import Profile from '../components/pages/user/Profile.vue'

// Helper function to get role-based dashboard route
function getRoleDashboard(userRole) {
  switch (userRole) {
    case 'admin':
      return '/dashboard/admin'
    case 'moderator':
      return '/dashboard/moderator'
    case 'user':
    default:
      return '/dashboard/user'
  }
}

const routes = [
  {
    path: '/',
    name: 'home',
    component: LandingPage,
    meta: {
      requiresGuest: true, // Only show to non-authenticated users
      title: 'Welcome to EcoTrack'
    }
  },
  {
    path: '/login',
    name: 'login',
    component: LandingPage, // Same component, but can show login form
    meta: {
      requiresGuest: true,
      title: 'Login - EcoTrack',
      formType: 'login' // Pass this to component to show login
    }
  },
  {
    path: '/register',
    name: 'register',
    component: LandingPage, // Same component, but can show register form
    meta: {
      requiresGuest: true,
      title: 'Register - EcoTrack',
      formType: 'register' // Pass this to component to show register
    }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    redirect: () => {
      // Get auth store and determine redirect based on user role
      const authStore = useAuthStore()
      const userRole = authStore.user?.role
      return getRoleDashboard(userRole)
    },
    meta: {
      requiresAuth: true,
    }
  },
  {
    path: '/dashboard/user',
    name: 'user-dashboard',
    component: RegularUserDashboard,
    meta: {
      requiresAuth: true,
      requiresRole: 'user',
      title: 'User Dashboard - EcoTrack'
    }
  },
  {
    path: '/dashboard/moderator',
    name: 'moderator-dashboard',
    component: () => import('../components/pages/moderator/ModeratorDashboard.vue'),
    meta: {
      requiresAuth: true,
      requiresRole: 'moderator',
      title: 'Moderator Dashboard - EcoTrack'
    }
  },
  {
    path: '/dashboard/admin',
    name: 'admin-dashboard',
    component: () => import('../components/pages/admin/AdminDashboard.vue'),
    meta: {
      requiresAuth: true,
      requiresRole: 'admin',
      title: 'Admin Dashboard - EcoTrack'
    }
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile,
    meta: {
      requiresAuth: true,
      title: 'Profile - EcoTrack'
    }
  },
  // Future routes for your roadmap
  {
    path: '/emissions',
    name: 'emissions',
    component: () => import('../components/pages/emissions/EmissionsList.vue'),
    meta: {
      requiresAuth: true,
      title: 'Emissions - EcoTrack'
    }
  },
  {
    path: '/posts',
    name: 'posts',
    component: () => import('../components/pages/posts/PostsList.vue'),
    meta: {
      requiresAuth: true,
      title: 'Community Posts - EcoTrack'
    }
  },
  // Legacy moderator route - redirect to new structure
  {
    path: '/moderator',
    redirect: { name: 'moderator-dashboard' }
  },
  // Legacy admin route - redirect to new structure
  {
    path: '/admin',
    redirect: { name: 'admin-dashboard' }
  },
  // 404 Catch all
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('../components/shared/NotFound.vue'),
    meta: {
      title: 'Page Not Found - EcoTrack'
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Helper function to get role-based dashboard route for navigation
function getRoleDashboardRoute(userRole) {
  switch (userRole) {
    case 'admin':
      return { name: 'admin-dashboard' }
    case 'moderator':
      return { name: 'moderator-dashboard' }
    case 'user':
    default:
      return { name: 'user-dashboard' }
  }
}

// Route Guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Set page title
  document.title = to.meta.title || 'EcoTrack'

  // Check if user data is loaded
  if (authStore.token && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch (error) {
      // Token is invalid, clear it
      authStore.clearAuth()
    }
  }

  // Check authentication requirements
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login', query: { redirect: to.fullPath } })
    return
  }

  // Check guest requirements (redirect authenticated users to their dashboard)
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    const userRole = authStore.user?.role
    next(getRoleDashboardRoute(userRole))
    return
  }

  // Check role requirements for protected routes
  if (to.meta.requiresRole && authStore.isAuthenticated) {
    const requiredRole = to.meta.requiresRole
    const userRole = authStore.user?.role

    // If user doesn't have the required role, redirect to their appropriate dashboard
    if (requiredRole === 'admin' && userRole !== 'admin') {
      next(getRoleDashboardRoute(userRole))
      return
    }

    if (requiredRole === 'moderator' && !['admin', 'moderator'].includes(userRole)) {
      next(getRoleDashboardRoute(userRole))
      return
    }

    if (requiredRole === 'user' && userRole !== 'user') {
      next(getRoleDashboardRoute(userRole))
      return
    }
  }

  next()
})

export default router
