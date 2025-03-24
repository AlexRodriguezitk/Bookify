<script setup>
import { onMounted } from 'vue'
import AuthService from '@/services/auth'
import { useRouter } from 'vue-router'

const router = useRouter()

onMounted(() => {
  AuthService.logout()
  setTimeout(() => router.push('/login'), 1500) // Ahora redirige a la raíz `/`
})
</script>

<template>
  <div class="container mt-5 text-center">
    <div class="alert alert-warning" role="alert">
      <h4 class="alert-heading">Cerrando sesión...</h4>
      <p>Serás redirigido a la página principal en unos segundos.</p>
    </div>
  </div>
</template>
