<template>
  <main class="container py-5 d-flex justify-content-center align-items-center min-vh-80 bg-light">
    <!-- Alerta flotante -->
    <AlertC v-if="alertMessage" :message="alertMessage" :color="alertColor" />

    <div
      v-if="Profile"
      class="card shadow rounded text-center position-relative"
      style="max-width: 400px; width: 100%"
    >
      <div class="card-body">
        <!-- Imagen con overlay -->
        <div class="profile-image-wrapper mx-auto mb-3 position-relative" @click="openModal">
          <img
            :src="
              Profile.profile_image ||
              `https://ui-avatars.com/api/?name=${Profile.name}&background=random`
            "
            @error="onImageError"
            alt="Avatar"
            class="rounded-circle"
            style="width: 120px; height: 120px; object-fit: cover; cursor: pointer"
          />
          <div class="edit-overlay d-flex justify-content-center align-items-center">
            <i class="fas fa-pencil-alt text-white"></i>
          </div>
        </div>

        <!-- Info del perfil -->
        <h4 class="card-title mb-1">{{ Profile.name }}</h4>
        <p class="text-muted mb-2">@{{ Profile.username }}</p>

        <ul class="list-group list-group-flush text-start">
          <li class="list-group-item d-flex justify-content-between">
            <strong>Teléfono:</strong>
            <span>{{ Profile.phone }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <strong>Rol:</strong>
            <span>{{ Profile.rol.name }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <strong>Usuario Activo:</strong>
            <span>
              <span v-if="Profile.is_active === '1'" class="badge bg-success">Sí</span>
              <span v-else class="badge bg-danger">No</span>
            </span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <strong>Creado el:</strong>
            <span>{{ formatDate(Profile.created_at) }}</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Modal Mejorado -->
    <div
      class="modal fade"
      ref="uploadModal"
      tabindex="-1"
      aria-labelledby="uploadModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="uploadModalLabel">
              <i class="fas fa-image me-2"></i>Cambiar foto de perfil
            </h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              data-bs-dismiss="modal"
              aria-label="Cerrar"
            ></button>
          </div>
          <div class="modal-body text-center">
            <div v-if="previewUrl" class="mb-3">
              <img
                :src="previewUrl"
                class="img-thumbnail rounded-circle shadow"
                style="width: 140px; height: 140px; object-fit: cover"
              />
              <div class="small text-muted mt-2">Vista previa</div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold" for="profileImageInput"
                >Selecciona una imagen</label
              >
              <input
                id="profileImageInput"
                type="file"
                accept="image/*"
                class="form-control"
                @change="onFileChange"
              />
              <div class="form-text">Máximo 500x500 px. Formato JPG, PNG.</div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Cancelar
            </button>
            <button class="btn btn-primary" @click="handleUpload" :disabled="uploading">
              <span v-if="uploading">
                <span class="spinner-border spinner-border-sm me-2"></span>Subiendo...
              </span>
              <span v-else> <i class="fas fa-upload me-1"></i>Subir imagen </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import { makeQuery } from '@/services/api'
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min'
import AlertC from '@/components/AlertC.vue'
import router from '@/router'

export default {
  components: { AlertC },
  data() {
    return {
      Profile: null,
      selectedFile: null,
      previewUrl: null,
      uploading: false,
      modalInstance: null,
      alertMessage: '',
      alertColor: 'danger',
    }
  },
  created() {
    this.fetchProfile()
  },
  methods: {
    async fetchProfile() {
      try {
        const response = await makeQuery('/profile', 'GET')
        this.Profile = response.data[0]
      } catch (error) {
        this.showAlert('Error al cargar el perfil.', 'danger')
      }
    },
    formatDate(dateStr) {
      const options = { year: 'numeric', month: 'short', day: 'numeric' }
      return new Date(dateStr).toLocaleDateString(undefined, options)
    },
    openModal() {
      if (!this.modalInstance) {
        this.modalInstance = new bootstrap.Modal(this.$refs.uploadModal)
      }
      this.modalInstance.show()
    },
    onImageError(e) {
      e.target.src = `https://ui-avatars.com/api/?name=${this.Profile?.name || 'User'}&background=random`
    },
    onFileChange(e) {
      const file = e.target.files[0]
      if (file) {
        const img = new Image()
        img.src = URL.createObjectURL(file)
        img.onload = () => {
          if (img.width > 1000 || img.height > 1000) {
            this.showAlert('La imagen no debe superar 500x500 px.', 'danger')
            this.selectedFile = null
            this.previewUrl = null
          } else {
            // Crear preview cuadrada recortada automáticamente
            const size = Math.min(img.width, img.height)
            const canvas = document.createElement('canvas')
            canvas.width = size
            canvas.height = size
            const ctx = canvas.getContext('2d')
            ctx.drawImage(
              img,
              (img.width - size) / 2,
              (img.height - size) / 2,
              size,
              size,
              0,
              0,
              size,
              size,
            )
            canvas.toBlob((blob) => {
              this.selectedFile = new File([blob], file.name, { type: file.type })
              this.previewUrl = URL.createObjectURL(blob)
            }, file.type)
          }
        }
      }
    },
    async handleUpload() {
      if (!this.selectedFile) {
        this.showAlert('Selecciona una imagen válida.', 'danger')
        return
      }
      try {
        this.uploading = true
        const formData = new FormData()
        formData.append('file', this.selectedFile)

        // Subir imagen
        const uploadResponse = await makeQuery('/Upload', 'POST', formData, true)
        const newImageUrl = uploadResponse.file_url
        console.log('Imagen subida:', newImageUrl)

        // Actualizar perfil
        const updateResponse = await makeQuery('/profile/image', 'POST', {
          image: newImageUrl,
        })
        this.Profile = updateResponse.data

        this.showAlert('Imagen de perfil actualizada.', 'success')
        this.modalInstance.hide()
        this.previewUrl = null
        router.push('/profile')
      } catch (error) {
        this.showAlert('Error al subir la imagen.' + error, 'danger')
      } finally {
        this.uploading = false
      }
    },
    showAlert(message, color = 'danger') {
      this.alertMessage = message
      this.alertColor = color
      setTimeout(() => {
        this.alertMessage = ''
      }, 3000)
    },
  },
}
</script>

<style scoped>
.profile-image-wrapper {
  position: relative;
  display: inline-block;
}

.edit-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  opacity: 0;
  transition: opacity 0.3s ease;
  border-radius: 50%;
}

.profile-image-wrapper:hover .edit-overlay {
  opacity: 1;
}

.edit-overlay i {
  font-size: 1.5rem;
}
</style>
