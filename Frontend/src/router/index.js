import { createRouter, createWebHistory } from 'vue-router'
import Home from '@/views/HomeView.vue'
import About from '@/views/AboutView.vue'

// Detectar en qué subdirectorio está instalada la app
const getBasePath = () => {
  const pathSegments = window.location.pathname.split('/')
  return pathSegments.length > 1 ? `/${pathSegments.slice(1, -1).join('/')}/` : '/'
}

const router = createRouter({
  history: createWebHistory(getBasePath()), // 🔹 Base dinámica
  routes: [
    { path: '/', component: Home },
    { path: '/about', component: About },
  ],
})

export default router
