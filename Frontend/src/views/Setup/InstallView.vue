<template>
  <main class="d-flex align-items-center justify-content-center vh-100">
    <div class="container" style="max-width: 800px">
      <div class="card">
        <div class="card-header">Instalación de Bookify</div>
        <div class="card-body">
          <h3 class="card-title text-center">Bienvenido a Bookify</h3>
          <div class="container mt-3">
            <p class="card-text">
              Vamos a comenzar con la instalación.<br />
              Es muy sencillo, comencemos por la conexión a la base de datos.
            </p>
            <form @submit.prevent="submitForm" autocomplete="off">
              <h4>Instalación de la base de datos</h4>
              <div class="row">
                <div class="col">
                  <div class="mb-3">
                    <label for="DB_HOST" class="form-label">Host</label>
                    <input
                      type="text"
                      id="DB_HOST"
                      v-model="formData.DB_HOST"
                      class="form-control"
                      required
                      autocomplete="new-password"
                    />
                  </div>
                  <div class="mb-3">
                    <label for="DB_USER" class="form-label">Usuario</label>
                    <input
                      type="text"
                      id="DB_USER"
                      v-model="formData.DB_USER"
                      class="form-control"
                      required
                      autocomplete="new-password"
                    />
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <label for="DB_PASSWORD" class="form-label">Contraseña</label>
                    <input
                      type="password"
                      id="DB_PASSWORD"
                      v-model="formData.DB_PASSWORD"
                      class="form-control"
                      autocomplete="new-password"
                    />
                  </div>
                  <div class="mb-3">
                    <label for="DB_NAME" class="form-label">Nombre de la Base de Datos</label>
                    <input
                      type="text"
                      id="DB_NAME"
                      v-model="formData.DB_NAME"
                      class="form-control"
                      autocomplete="new-password"
                      required
                    />
                  </div>
                </div>
              </div>
              <h4>
                <button
                  class="btn btn-secondary"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#securityDetails"
                  aria-expanded="false"
                  aria-controls="securityDetails"
                >
                  Detalles de Seguridad (Opcional)
                </button>
              </h4>
              <div class="collapse" id="securityDetails">
                <div class="row">
                  <div class="col">
                    <div class="mb-3">
                      <label for="JWT_SECRET" class="form-label">JWT Secret</label>
                      <input
                        type="text"
                        id="JWT_SECRET"
                        v-model="formData.JWT_SECRET"
                        class="form-control"
                        autocomplete="new-password"
                      />
                    </div>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                Instalar
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { reactive, ref } from 'vue'
import router from '@/router'

const formData = reactive({
  DB_HOST: '',
  DB_USER: '',
  DB_PASSWORD: '',
  DB_NAME: '',
  JWT_SECRET: '',
})

const isSubmitting = ref(false)

const submitForm = async () => {
  isSubmitting.value = true
  try {
    const payload = { ...formData }
    if (!payload.JWT_SECRET) {
      delete payload.JWT_SECRET
    }

    const response = await fetch('./api/install', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(payload),
    })

    if (!response.ok) {
      throw new Error('Failed to install')
    }

    const data = await response.json()
    console.log('Installation successful:', data)
    // Redirect to login without reloading the SPA
    router.replace({ path: '/login' })
  } catch (error) {
    console.error('Error during installation:', error)
  } finally {
    isSubmitting.value = false
  }
}
</script>
