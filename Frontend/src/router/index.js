import { createRouter, createWebHistory } from 'vue-router'
import AuthService from '@/services/auth'
import { checkBackendStatus } from '@/services/api'
import Permissions from '@/services/permissions'
import Login from '@/views/LoginView.vue'
import Logout from '@/views/LogoutView.vue'
import Register from '@/views/RegisterView.vue'
import Install from '@/views/InstallView.vue'
import Dashboard from '@/views/DashboardView.vue'
import Ticket from '@/views/TicketView.vue'
import Home from '@/views/HomeView.vue'

const getBasePath = () => {
  if (import.meta.env.PROD) {
    // Intenta obtener la base desde el <base> en index.html
    const baseElement = document.querySelector('base')
    if (baseElement) {
      return baseElement.getAttribute('href') || '/'
    }

    // Si no hay <base>, usa el path actual para detectar la subruta
    const pathSegments = window.location.pathname.split('/').filter((segment) => segment)
    if (pathSegments.length > 0) {
      return '/' + pathSegments[0] + '/' // Toma la primera parte de la URL como base
    }
  }
  return '/' // Para desarrollo local
}

const router = createRouter({
  history: createWebHistory(getBasePath()),
  routes: [
    { path: '/', component: Home },
    { path: '/login', component: Login },
    { path: '/register', component: Register },
    { path: '/logout', component: Logout, meta: { requiresAuth: true } },
    { path: '/install', component: Install },
    {
      path: '/dashboard',
      component: Dashboard,
      meta: { requiresAuth: true },
      children: [
        {
          path: 'tickets', // Ahora es una subruta de /dashboard
          component: Ticket,
          meta: { requiresAuth: true, requiresPermission: 'TICKET.INDEX' },
        },
      ],
    },
  ],
})

// Middleware de autenticación y permisos
router.beforeEach(async (to, from, next) => {
  const isAuthenticated = AuthService.isAuthenticated()

  try {
    const backendAvailable = await checkBackendStatus()

    // Redirección en base al estado del backend
    if (backendAvailable && to.path === '/install') {
      return next('/')
    }
    if (!backendAvailable && to.path !== '/install') {
      return next('/install')
    }

    // Verificar autenticación
    if (to.meta.requiresAuth && !isAuthenticated) {
      return next('/login')
    }

    // Verificar permisos específicos si la ruta lo requiere
    if (to.meta.requiresPermission) {
      try {
        const permissions = await Permissions.checkPermissions([to.meta.requiresPermission])
        const hasAccess = Permissions.hasPermission(permissions, to.meta.requiresPermission)

        if (!hasAccess) {
          return next('/dashboard') // Si no tiene permiso, redirigir a Dashboard
        }
      } catch (error) {
        console.error('Error checking permissions:', error)
        return next('/dashboard')
      }
    }

    next()
  } catch (error) {
    console.error('Error checking backend status:', error)
    return next('/install')
  }
})

export default router
