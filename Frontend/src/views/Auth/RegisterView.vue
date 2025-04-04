<script setup>
import { ref } from 'vue'
import AuthService from '@/services/auth.js'
import { useRouter } from 'vue-router'

const name = ref('')
const username = ref('')
const password = ref('')
const phone = ref('')
const successMessage = ref('')
const errorMessage = ref('')
const router = useRouter()

const register = async () => {
  try {
    await AuthService.register(name.value, username.value, password.value, phone.value)
    successMessage.value = 'Registro exitoso. Redirigiendo a inicio...'
    setTimeout(() => router.push('/'), 2000)
  } catch (error) {
    console.error('Registration failed:', error)
    errorMessage.value = 'Error en el registro'
  }
}
</script>

<template>
  <div class="container mt-5" style="max-width: 400px">
    <div class="card">
      <div class="card-header text-center">
        <h2>Registrarse</h2>
      </div>
      <div class="card-body">
        <form @submit.prevent="register" autocomplete="off">
          <div class="mb-3">
            <label for="name" class="form-label">Nombre:</label>
            <input
              id="name"
              v-model="name"
              type="text"
              class="form-control"
              required
              autocomplete="off"
            />
          </div>
          <div class="mb-3">
            <label for="username" class="form-label">Usuario:</label>
            <input
              id="username"
              v-model="username"
              type="text"
              class="form-control"
              required
              autocomplete="off"
            />
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Contraseña:</label>
            <input
              id="password"
              v-model="password"
              type="password"
              class="form-control"
              required
              autocomplete="new-password"
            />
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">Teléfono:</label>
            <input
              id="phone"
              v-model="phone"
              type="text"
              class="form-control"
              required
              autocomplete="off"
            />
          </div>
          <button type="submit" class="btn btn-success w-100">Registrarse</button>
          <p v-if="successMessage" class="text-success mt-3 text-center">
            {{ successMessage }}
          </p>
          <p v-if="errorMessage" class="text-danger mt-3 text-center">
            {{ errorMessage }}
          </p>
        </form>
      </div>
    </div>
  </div>
</template>
