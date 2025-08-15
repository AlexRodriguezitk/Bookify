<script setup>
import { ref, computed } from 'vue'
import AuthService from '@/services/auth.js'
import { useRouter } from 'vue-router'

const name = ref('')
const username = ref('')
const password = ref('')
const repeatPassword = ref('')
const phone = ref('')
const successMessage = ref('')
const errorMessage = ref('')
const router = useRouter()

// Validación en tiempo real
const passwordsMatch = computed(() => {
  return repeatPassword.value.length > 0 && password.value === repeatPassword.value
})
const passwordTooShort = computed(() => password.value.length > 0 && password.value.length < 6)

const register = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  if (!passwordsMatch.value) {
    errorMessage.value = 'Las contraseñas no coinciden'
    return
  }

  if (passwordTooShort.value) {
    errorMessage.value = 'La contraseña debe tener al menos 6 caracteres'
    return
  }

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
    <div class="card shadow">
      <div class="card-header text-center bg-success text-white">
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
              :class="{ 'is-invalid': passwordTooShort }"
              required
              autocomplete="new-password"
            />
            <div v-if="passwordTooShort" class="text-danger small">
              La contraseña debe tener al menos 6 caracteres
            </div>
          </div>

          <div class="mb-3">
            <label for="repeat-password" class="form-label">Repetir Contraseña:</label>
            <input
              id="repeat-password"
              v-model="repeatPassword"
              type="password"
              class="form-control"
              :class="{
                'is-valid': passwordsMatch,
                'is-invalid': repeatPassword.length > 0 && !passwordsMatch,
              }"
              required
              autocomplete="new-password"
            />
            <div v-if="repeatPassword.length > 0 && !passwordsMatch" class="text-danger small">
              Las contraseñas no coinciden
            </div>
            <div v-if="passwordsMatch" class="text-success small">✔ Las contraseñas coinciden</div>
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
