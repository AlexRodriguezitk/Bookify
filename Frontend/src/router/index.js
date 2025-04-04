import { createRouter, createWebHistory } from 'vue-router'
import AuthService from '@/services/auth'
import { checkBackendStatus } from '@/services/api'
import Permissions from '@/services/permissions'

// Importar vistas
import Home from '@/views/HomeView.vue'
import Login from '@/views/Auth/LoginView.vue'
import Register from '@/views/Auth/RegisterView.vue'
import Logout from '@/views/Auth/LogoutView.vue'
import Install from '@/views/Setup/InstallView.vue'
import Dashboard from '@/views/Dashboard/DashboardView.vue'
import Inbox from '@/views/Dashboard/Tickets/InboxView.vue'
import Ticket from '@/views/Dashboard/Tickets/TicketView.vue'
import Settings from '@/views/Settings/SettingsView.vue'

// Definir rutas
const routes = [
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
        path: '/tickets',
        component: Ticket,
        meta: { requiresPermission: 'TICKET.INDEX' },
        children: [
          {
            path: 'settings',
            component: Home,
            meta: { requiresPermission: 'TICKET.SETTINGS' },
          },
          {
            path: ':id/',
            component: Inbox,
            meta: { requiresPermission: 'TICKET.VIEW' },
          },
        ],
      },
      {
        path: '/settings',
        component: Settings,
        meta: { requiresPermission: 'SETTINGS.VIEW' },
      },
    ],
  },
]

// Crear router
const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Middleware de autenticación y permisos
router.beforeEach(async (to, from, next) => {
  try {
    const backendAvailable = await checkBackendStatus()

    if (backendAvailable && to.path === '/install') return next('/')
    if (!backendAvailable && to.path !== '/install') return next('/install')

    if (to.meta.requiresAuth) {
      if (!AuthService.isAuthenticated()) {
        return next('/login')
      }
      try {
        await AuthService.renewToken()
      } catch (error) {
        console.error('Error renovando token:', error)
        AuthService.logout()
        return next('/login')
      }
    }

    if (to.meta.requiresPermission) {
      try {
        const permissions = await Permissions.checkPermissions([to.meta.requiresPermission])
        if (!Permissions.hasPermission(permissions, to.meta.requiresPermission)) {
          return next('/dashboard')
        }
      } catch (error) {
        console.error('Error al verificar permisos:', error)
        return next('/dashboard')
      }
    }

    next()
  } catch (error) {
    console.error('Error verificando el estado del backend:', error)
    return next('/install')
  }
})

export default router
