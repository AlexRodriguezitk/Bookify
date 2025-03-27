<script setup>
import { onMounted, ref, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { checkBackendStatus } from '@/services/api'
import AuthService from '@/services/auth'

const backendAvailable = ref(true)
const isAuthenticated = ref(AuthService.isAuthenticated())
const router = useRouter()
const route = useRoute()
let backendInterval = null
let authInterval = null

// Comprobar el estado del backend periódicamente
const checkStatus = async () => {
  try {
    backendAvailable.value = await checkBackendStatus()
  } catch (error) {
    console.error('Error checking backend status:', error)
    backendAvailable.value = false
  }
}

// Verificar autenticación y renovar token
const checkAuth = async () => {
  if (AuthService.isAuthenticated()) {
    try {
      await AuthService.renewToken()
      isAuthenticated.value = true
    } catch (error) {
      console.error('Error renewing token:', error)
      AuthService.logout()
      isAuthenticated.value = false
      router.push('/login')
    }
  } else {
    isAuthenticated.value = false
    router.push('/login')
  }
}

// Configurar intervalos de backend
const setupBackendInterval = () => {
  checkStatus()
  backendInterval = setInterval(checkStatus, 600000) // 10 minutos
}

// Configurar o limpiar el intervalo de autenticación según la ruta
const updateAuthInterval = () => {
  if (authInterval) {
    clearInterval(authInterval)
    authInterval = null
  }
  if (route.meta.requiresAuth) {
    //console.log('Starting auth interval')
    checkAuth()
    authInterval = setInterval(checkAuth, 300000) // 5 minutos
  }
}

// Redirigir a /install si el backend se desconecta
watch(backendAvailable, (isAvailable) => {
  if (!isAvailable) {
    router.push('/install')
  } else if (router.currentRoute.value.path === '/install') {
    router.push('/')
  }
})

// Verificar cambios en la ruta para actualizar autenticación
watch(() => route.meta.requiresAuth, updateAuthInterval, { immediate: true })

onMounted(() => {
  setupBackendInterval()
  updateAuthInterval()
})

onUnmounted(() => {
  if (backendInterval) clearInterval(backendInterval)
  if (authInterval) clearInterval(authInterval)
})
</script>

<template>
  <div>
    <RouterView />
  </div>
</template>

<style scoped></style>
