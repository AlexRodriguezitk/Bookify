<template>
  <main class="container py-5 d-flex justify-content-center align-items-center min-vh-80 bg-light">
    <AlertC v-if="alertMessage" :message="alertMessage" :color="alertColor" />

    <div
      v-if="Profile"
      class="card shadow rounded text-center position-relative"
      style="max-width: 400px; width: 100%"
    >
      <div class="card-body">
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
              <span v-if="Profile.is_active == '1'" class="badge bg-success">Sí</span>
              <span v-else class="badge bg-danger">No</span>
            </span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <strong>Creado el:</strong>
            <span>{{ formatDate(Profile.created_at) }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <strong>Autenticación de dos factores (2FA):</strong>
            <div v-if="Profile.totp_secret">
              <span class="badge bg-success">Activado</span>
            </div>
            <div v-else>
              <div class="d-flex align-items-center">
                <span class="badge bg-secondary">Desactivado</span>
                <button @click="openTotpModal" class="btn btn-sm btn-outline-primary ms-2">
                  Activar
                </button>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

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

    <div
      class="modal fade"
      ref="totpModal"
      tabindex="-1"
      aria-labelledby="totpModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="totpModalLabel">Activar 2FA</h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              data-bs-dismiss="modal"
              aria-label="Cerrar"
            ></button>
          </div>
          <div class="modal-body text-center">
            <div v-if="!showQRCode">
              <p>Haga clic en el botón para generar su código de activación de 2FA.</p>
              <button @click="generateTotpSecret" class="btn btn-primary">Generar Código QR</button>
            </div>
            <div v-else>
              <p>
                Escanee el siguiente código QR con su aplicación de autenticación (ej. Google
                Authenticator).
              </p>
              <div class="d-flex justify-content-center my-3">
                <QRCodeVue3
                  :value="totpUri"
                  :width="200"
                  :height="200"
                  :qr-options="{ typeNumber: 0, mode: 'Byte', errorCorrectionLevel: 'H' }"
                  :image-options="{ hideBackgroundDots: true, imageSize: 0.4, margin: 0 }"
                  :dots-options="{ type: 'dots', color: '#2c3e50' }"
                  :background-options="{ color: '#ffffff' }"
                  :corners-square-options="{ type: 'square', color: '#2c3e50' }"
                  :corners-dot-options="{ type: 'dot', color: '#2c3e50' }"
                />
              </div>
              <p class="mt-3">
                Luego, ingrese el código de 6 dígitos de la aplicación para confirmar:
              </p>
              <form @submit.prevent="enable2fa" class="d-flex flex-column align-items-center">
                <input
                  v-model="otpCode"
                  type="text"
                  class="form-control mb-3"
                  maxlength="6"
                  required
                  placeholder="Código 2FA"
                  style="max-width: 150px"
                />
                <button type="submit" class="btn btn-success">Activar 2FA</button>
              </form>
              <p v-if="totpAlert" class="text-danger mt-2">{{ totpAlert }}</p>
            </div>
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
import QRCodeVue3 from 'qrcode-vue3'

export default {
  components: { AlertC, QRCodeVue3 },
  data() {
    return {
      Profile: null,
      selectedFile: null,
      previewUrl: null,
      uploading: false,
      modalInstance: null,
      totpModalInstance: null,
      alertMessage: '',
      alertColor: 'danger',
      totpUri: null,
      totpSecret: null,
      otpCode: '',
      showQRCode: false,
      totpAlert: '',
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
        console.log(this.Profile)
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
    openTotpModal() {
      if (!this.totpModalInstance) {
        this.totpModalInstance = new bootstrap.Modal(this.$refs.totpModal)
      }
      this.totpModalInstance.show()
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
          // ✅ Corregido: La validación de tamaño ahora es 500x500
          if (img.width > 500 || img.height > 500) {
            this.showAlert('La imagen no debe superar 500x500 px.', 'danger')
            this.selectedFile = null
            this.previewUrl = null
          } else {
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

        const uploadResponse = await makeQuery('/Upload', 'POST', formData, true)
        const newImageUrl = uploadResponse.file_url
        console.log('Imagen subida:', newImageUrl)

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
    async generateTotpSecret() {
      try {
        const response = await makeQuery('/auth/generate-totp-secret', 'GET')
        this.totpUri = response.data.totp_uri
        this.totpSecret = response.data.totp_secret
        this.showQRCode = true
      } catch (error) {
        this.totpAlert = 'Error al generar el código. Por favor, intente de nuevo.'
      }
    },
    async enable2fa() {
      try {
        const response = await makeQuery('/auth/enable-2fa', 'POST', {
          otp_code: this.otpCode,
          totp_secret: this.totpSecret,
        })

        this.totpAlert = '¡2FA activado correctamente!'
        this.Profile.totp_secret = this.totpSecret
        this.totpModalInstance.hide()
        this.showAlert('¡2FA activado correctamente!', 'success')
      } catch (error) {
        this.totpAlert = 'Código de 2FA inválido. Inténtelo de nuevo.'
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

.modal-content {
  border-radius: 1rem;
  overflow: hidden;
}

.modal-header {
  border-bottom: none;
  border-top-left-radius: 1rem;
  border-top-right-radius: 1rem;
}

.modal-body {
  padding: 2rem;
}
</style>
