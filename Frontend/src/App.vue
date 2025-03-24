<script setup>
import { onMounted, ref, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { checkBackendStatus } from '@/services/api'

const backendAvailable = ref(true)
const router = useRouter()
let interval = null

// Comprobar el estado del backend
const checkStatus = async () => {
  try {
    const status = await checkBackendStatus()
    backendAvailable.value = status
  } catch (error) {
    console.error('Error checking backend status:', error)
    backendAvailable.value = false
  }
}

// Configurar verificaciones periódicas
const setupIntervals = () => {
  checkStatus()
  const delay = 30000 // 30 segundos es un delay saludable
  interval = setInterval(checkStatus, delay)
}

// Redirigir a /install si el backend se desconecta
watch(backendAvailable, (isAvailable) => {
  if (!isAvailable) {
    router.push('/install')
  } else if (router.currentRoute.value.path === '/install') {
    router.push('/') // Si vuelve el backend y estamos en /install, redirigir a /
  }
})

onMounted(setupIntervals)
onUnmounted(() => {
  if (interval) clearInterval(interval)
})
</script>

<template>
  <div>
    <RouterView />
  </div>
</template>

<style scoped></style>
