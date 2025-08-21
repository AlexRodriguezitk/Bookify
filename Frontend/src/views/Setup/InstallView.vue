<template>
  <main class="d-flex align-items-center justify-content-center vh-100">
    <div class="container" style="max-width: 800px">
      <div class="card">
        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs" id="installTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button
                class="nav-link active"
                id="db-tab"
                data-bs-toggle="tab"
                data-bs-target="#db"
                type="button"
                role="tab"
                aria-controls="db"
                aria-selected="true"
              >
                1. Configuración de Base de Datos
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                id="admin-tab"
                data-bs-toggle="tab"
                data-bs-target="#admin"
                type="button"
                role="tab"
                aria-controls="admin"
                aria-selected="false"
                :disabled="!dbFormCompleted"
              >
                2. Crear usuario Administrador
              </button>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content" id="installTabContent">
            <div class="tab-pane fade show active" id="db" role="tabpanel" aria-labelledby="db-tab">
              <h3 class="card-title text-center">Bienvenido a Bookify</h3>
              <p class="card-text">
                Vamos a comenzar con la instalación.<br />
                Es muy sencillo, comencemos por la conexión a la base de datos.
              </p>
              <form @submit.prevent="moveToAdminTab" autocomplete="off">
                <h4>Detalles de la Base de Datos</h4>
                <div class="row">
                  <div class="col-md-6">
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
                  <div class="col-md-6">
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
                    class="btn btn-secondary mt-2"
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
                    <div class="col-md-6">
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
                <button type="submit" class="btn btn-primary mt-3">Siguiente</button>
              </form>
            </div>

            <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">
              <h4 class="card-title text-center">Crear usuario Administrador</h4>
              <p class="card-text">
                Ahora, crea el primer usuario que tendrá permisos de administrador.
              </p>
              <form @submit.prevent="submitForm" autocomplete="off">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="admin_name" class="form-label">Nombre</label>
                      <input
                        type="text"
                        id="admin_name"
                        v-model="formData.admin_name"
                        class="form-control"
                        required
                        autocomplete="new-password"
                      />
                    </div>
                    <div class="mb-3">
                      <label for="admin_username" class="form-label">Usuario</label>
                      <input
                        type="text"
                        id="admin_username"
                        v-model="formData.admin_username"
                        class="form-control"
                        required
                        autocomplete="new-password"
                      />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="admin_password" class="form-label">Contraseña</label>
                      <input
                        type="password"
                        id="admin_password"
                        v-model="formData.admin_password"
                        class="form-control"
                        required
                        autocomplete="new-password"
                      />
                    </div>
                    <div class="mb-3">
                      <label for="admin_phone" class="form-label">Teléfono</label>
                      <input
                        type="text"
                        id="admin_phone"
                        v-model="formData.admin_phone"
                        class="form-control"
                        required
                        autocomplete="new-password"
                      />
                    </div>
                  </div>
                </div>
                <div v-if="apiError" class="alert alert-danger mt-3" role="alert">
                  <strong>Error:</strong> {{ apiError }}
                </div>
                <button type="submit" class="btn btn-success mt-3" :disabled="isSubmitting">
                  <span
                    v-if="isSubmitting"
                    class="spinner-border spinner-border-sm me-2"
                    role="status"
                    aria-hidden="true"
                  ></span>
                  Finalizar Instalación
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import router from '@/router'
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min'

const formData = reactive({
  DB_HOST: '',
  DB_USER: '',
  DB_PASSWORD: '',
  DB_NAME: '',
  admin_name: '',
  admin_username: '',
  admin_password: '',
  admin_phone: '',
  JWT_SECRET: '',
})

const isSubmitting = ref(false)
const apiError = ref('')

// Controla si el formulario de la DB está completo para habilitar el siguiente tab
const dbFormCompleted = computed(() => {
  return formData.DB_HOST && formData.DB_USER && formData.DB_NAME
})

// Función para mover al siguiente tab
const moveToAdminTab = () => {
  if (dbFormCompleted.value) {
    const adminTab = new bootstrap.Tab(document.getElementById('admin-tab'))
    adminTab.show()
  }
}

const submitForm = async () => {
  isSubmitting.value = true
  apiError.value = ''

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

    const data = await response.json()

    if (!response.ok || data.status === false) {
      const errorMessage = data.data && data.data.error ? data.data.error : data.message
      throw new Error(errorMessage || 'Failed to install')
    }

    console.log('Installation successful:', data)
    router.replace({ path: '/login' })
  } catch (error) {
    console.error('Error during installation:', error)
    apiError.value = error.message
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.nav-tabs .nav-item .nav-link:disabled {
  color: #6c757d;
  cursor: not-allowed;
}

.tab-content {
  padding: 1.5rem;
}
</style>
