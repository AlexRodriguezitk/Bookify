<script setup name="UserLogin">
import { ref } from 'vue'
import AuthService from '@/services/auth.js'
import { useRouter } from 'vue-router'

const username = ref('')
const password = ref('')
const errorMessage = ref('')
const router = useRouter()

const login = async () => {
  try {
    // eslint-disable-next-line no-unused-vars
    const response = await AuthService.login(username.value, password.value)
    //console.log('Login exitoso:', response)
    router.push('/dashboard') // Redirigir a otra vista después de iniciar sesión
  } catch {
    errorMessage.value = 'Usuario o contraseña incorrectos'
  }
}
</script>

<template>
  <main class="d-flex align-items-center justify-content-center vh-100">
    <div class="container mt-5" style="max-width: 400px">
      <div class="card">
        <div class="card-header text-center">
          <h2>Iniciar Sesión</h2>
        </div>
        <div class="card-body">
          <form @submit.prevent="login">
            <div class="mb-3">
              <label for="username" class="form-label">Usuario:</label>
              <input id="username" v-model="username" type="text" class="form-control" required />
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Contraseña:</label>
              <input
                id="password"
                v-model="password"
                type="password"
                class="form-control"
                required
              />
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            <p v-if="errorMessage" class="text-danger mt-3 text-center">
              {{ errorMessage }}
            </p>
          </form>
        </div>
      </div>
    </div>
  </main>
</template>
