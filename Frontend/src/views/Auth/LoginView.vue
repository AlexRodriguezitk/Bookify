<script setup name="UserLogin">
import { ref } from 'vue'
import AuthService from '@/services/auth.js'
import { useRouter } from 'vue-router'

const username = ref('')
const password = ref('')
const otpCode = ref('')
const errorMessage = ref('')
const is2faRequired = ref(false)
const router = useRouter()

const login = async () => {
  try {
    const response = await AuthService.login(username.value, password.value)

    // ✅ Corregido: Accedemos a la propiedad del objeto data que está anidado.
    if (response.data && response.data.hasOwnProperty('2fa_required')) {
      is2faRequired.value = true
      errorMessage.value = ''
    } else {
      router.push('/dashboard')
    }
  } catch (error) {
    if (error.response && error.response.status === 401) {
      errorMessage.value = 'Usuario o contraseña incorrectos'
    } else {
      errorMessage.value = 'Ha ocurrido un error. Inténtelo de nuevo más tarde.'
    }
  }
}

const verify2fa = async () => {
  try {
    const response = await AuthService.verify2fa(username.value, otpCode.value)
    router.push('/dashboard')
  } catch (error) {
    if (error.response && error.response.status === 401) {
      errorMessage.value = 'Código de 2FA incorrecto'
    } else {
      errorMessage.value = 'Ha ocurrido un error. Inténtelo de nuevo más tarde.'
    }
  }
}
</script>

<template>
  <main class="d-flex align-items-center justify-content-center vh-100">
    <div class="container mt-5" style="max-width: 400px">
      <div class="card">
        <div class="card-header text-center">
          <h2>{{ is2faRequired ? 'Verificación 2FA' : 'Iniciar Sesión' }}</h2>
        </div>
        <div class="card-body">
          <form v-if="!is2faRequired" @submit.prevent="login">
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
          </form>

          <form v-else @submit.prevent="verify2fa">
            <div class="mb-3">
              <p class="text-center">
                Por favor, ingrese el código de su aplicación de autenticación.
              </p>
              <label for="otpCode" class="form-label">Código 2FA:</label>
              <input
                id="otpCode"
                v-model="otpCode"
                type="text"
                class="form-control"
                maxlength="6"
                required
              />
            </div>
            <button type="submit" class="btn btn-success w-100">Verificar</button>
            <p class="text-center mt-2">
              <a href="#" @click.prevent="is2faRequired = false">Volver al login</a>
            </p>
          </form>

          <p v-if="errorMessage" class="text-danger mt-3 text-center">
            {{ errorMessage }}
          </p>
        </div>
      </div>
    </div>
  </main>
</template>
