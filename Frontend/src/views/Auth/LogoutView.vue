<script setup>
import { ref, onMounted } from 'vue'
import AuthService from '@/services/auth'
import { useRouter } from 'vue-router'

const router = useRouter()
const showMessage = ref(false)

onMounted(() => {
  AuthService.logout()
  showMessage.value = true

  setTimeout(() => {
    showMessage.value = false
    setTimeout(() => router.push('/login'), 600)
  }, 1600)
})
</script>

<template>
  <div class="container mt-5 d-flex justify-content-center align-items-center flex-column">
    <transition name="fade-zoom">
      <div
        v-if="showMessage"
        class="alert bg-primary text-white text-center shadow-lg py-4 px-4 rounded-4 border"
        role="alert"
        style="max-width: 480px"
      >
        <div class="mb-3">
          <i class="fas fa-user-lock fa-3x text-white"></i>
        </div>
        <h4 class="alert-heading mb-2 fw-bold text-white">Sesión finalizada</h4>
        <p class="mb-0">Gracias por tu visita. Serás redirigido en breve al inicio de sesión.</p>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.fade-zoom-enter-active {
  animation: fadeZoomIn 0.6s ease;
}
.fade-zoom-leave-active {
  animation: fadeZoomOut 0.4s ease forwards;
}

@keyframes fadeZoomIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes fadeZoomOut {
  from {
    opacity: 1;
    transform: scale(1);
  }
  to {
    opacity: 0;
    transform: scale(0.95);
  }
}
</style>
