<template>
  <div class="d-flex flex-column gap-2 p-2 border-top">
    <!-- Toolbar con iconos -->
    <div class="d-flex gap-1 mb-1">
      <button
        class="btn btn-sm btn-outline-primary"
        @mousedown.prevent="openLinkModal"
        :disabled="!editorFocused"
      >
        <i class="fas fa-link"></i>
      </button>
      <button
        class="btn btn-sm btn-outline-success"
        @mousedown.prevent="openImageModal"
        :disabled="!editorFocused"
      >
        <i class="fas fa-image"></i>
      </button>
    </div>

    <!-- Editor de texto enriquecido -->
    <div
      ref="editor"
      contenteditable="true"
      class="form-control flex-grow-1"
      @input="onInput"
      @keydown.enter="handleEnter"
      @focus="editorFocused = true"
      @blur="editorFocused = false"
      placeholder="Escribe un mensaje..."
      style="min-height: 60px; overflow-y: auto"
    ></div>

    <!-- Botón enviar -->
    <div class="d-flex justify-content-end mt-1">
      <button class="btn btn-primary" :disabled="sending || !hasContent" @click="onSend">
        <i class="fas fa-paper-plane"></i>
      </button>
    </div>

    <!-- Modal Insertar Link -->
    <div class="modal fade" tabindex="-1" ref="linkModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Insertar enlace</h5>
            <button type="button" class="btn-close" @click="hideLinkModal"></button>
          </div>
          <div class="modal-body">
            <input
              type="text"
              v-model="linkTitle"
              class="form-control mb-2"
              placeholder="Título del enlace"
            />
            <input
              type="text"
              v-model="linkUrl"
              class="form-control"
              placeholder="URL del enlace"
            />
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="hideLinkModal">Cancelar</button>
            <button class="btn btn-primary" @click="confirmInsertLink">Insertar</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" ref="imageModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Insertar imagen</h5>
            <button type="button" class="btn-close" @click="hideImageModal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-bold" for="profileImageInput"
                >Selecciona una imagen</label
              >
              <div class="input-group">
                <input
                  id="profileImageInput"
                  type="file"
                  accept="image/*"
                  class="form-control"
                  @change="onFileChange"
                />
                <button class="btn btn-primary" type="button" @click="handleUploadImage">
                  Subir
                </button>
              </div>
              <div class="form-text">Máximo 500x500 px. Formato JPG, PNG.</div>
            </div>
            <label class="form-label fw-bold" for="profileImageInput"
              >O coloque la url de la imagen</label
            >
            <input
              type="text"
              v-model="imageUrl"
              class="form-control"
              placeholder="URL de la imagen"
            />
          </div>

          <div class="modal-footer">
            <button class="btn btn-secondary" @click="hideImageModal">Cancelar</button>
            <button class="btn btn-success" :disabled="imageUrl === ''" @click="confirmInsertImage">
              Insertar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import * as bootstrap from 'bootstrap'
import { makeQuery } from '@/services/api'
export default {
  name: 'MessageInputC',
  props: { sending: { type: Boolean, default: false } },
  data() {
    return {
      content: '',
      linkTitle: '', // nuevo campo para el título del enlace
      linkUrl: '',
      imageUrl: '',
      linkModalInstance: null,
      imageModalInstance: null,
      savedRange: null,
      selectedFile: null,
      editorFocused: false,
    }
  },
  computed: {
    hasContent() {
      return this.content.trim().length > 0
    },
  },
  mounted() {
    // Instancias de los modales
    this.linkModalInstance = new bootstrap.Modal(this.$refs.linkModal, { backdrop: 'static' })
    this.imageModalInstance = new bootstrap.Modal(this.$refs.imageModal, { backdrop: 'static' })
  },
  methods: {
    async handleUploadImage() {
      if (!this.selectedFile) {
        this.showAlert('Selecciona una imagen válida.', 'danger')
        return
      }
      this.uploading = true
      const formData = new FormData()
      formData.append('file', this.selectedFile)

      // Subir imagen
      const uploadResponse = await makeQuery('/Upload', 'POST', formData, true)
      const newImageUrl = uploadResponse.file_url
      console.log('Imagen subida:', newImageUrl)
      this.imageUrl = newImageUrl
    },
    onFileChange(e) {
      const file = e.target.files[0]

      if (file) {
        const img = new Image()
        img.src = URL.createObjectURL(file)

        img.onload = () => {
          // Check if image dimensions exceed 1000x1000
          if (img.width > 1000 || img.height > 1000) {
            // Show an error message and clear the file selection
            this.showAlert('La imagen no debe superar 500x500 px.', 'danger')
            this.selectedFile = null
            this.previewUrl = null

            // Revoke the object URL to free up memory
            URL.revokeObjectURL(img.src)
          } else {
            // If dimensions are fine, set the file and create a preview URL
            this.selectedFile = file
            this.previewUrl = URL.createObjectURL(file)
          }
        }
      }
    },
    onInput() {
      this.content = this.$refs.editor?.innerHTML || ''
    },
    handleEnter(e) {
      if (e.shiftKey) document.execCommand('insertHTML', false, '<br>')
      else {
        e.preventDefault()
        this.onSend()
      }
    },
    onSend() {
      if (!this.hasContent) return
      this.$emit('send', this.$refs.editor.innerHTML.trim())
      this.$refs.editor.innerHTML = ''
      this.content = ''
      this.$refs.editor.focus()
    },
    // Guardar/restaurar selección
    saveSelection() {
      const sel = window.getSelection()
      if (sel.rangeCount > 0) this.savedRange = sel.getRangeAt(0)
    },
    restoreSelection() {
      if (!this.savedRange) return
      const sel = window.getSelection()
      sel.removeAllRanges()
      sel.addRange(this.savedRange)
    },
    // Abrir modales
    openLinkModal() {
      this.saveSelection()
      this.linkModalInstance.show()
    },
    openImageModal() {
      this.saveSelection()
      this.imageModalInstance.show()
    },
    hideLinkModal() {
      this.linkTitle = ''
      this.linkUrl = ''
      this.linkModalInstance.hide()
    },
    hideImageModal() {
      this.imageUrl = ''
      this.imageModalInstance.hide()
    },
    // Insertar enlace con título y detección local/externa
    confirmInsertLink() {
      if (!this.linkUrl || !this.linkTitle) return

      this.restoreSelection()
      const a = document.createElement('a')
      let url = this.linkUrl.trim()

      // Detectar local vs externa
      if (url.startsWith('./') || url.startsWith('/')) {
        // Local
        a.style.color = '#28a745' // verde
      } else {
        // Externa
        a.style.color = '#0d6efd' // azul
        if (!url.startsWith('http://') && !url.startsWith('https://')) {
          url = 'https://' + url
        }
      }

      a.href = url
      a.target = '_blank'
      a.rel = 'noopener noreferrer'
      a.textContent = this.linkTitle

      const range = this.savedRange
      range.deleteContents()
      range.insertNode(a)
      range.setStartAfter(a)
      range.setEndAfter(a)
      this.onInput()
      this.$refs.editor.focus()
      this.hideLinkModal()
      this.savedRange = null
      this.linkTitle = ''
      this.linkUrl = ''
    },
    // Insertar imagen (opcional: puedes añadir lógica similar para detectar local/externa)
    confirmInsertImage() {
      if (!this.imageUrl) return
      this.restoreSelection()
      const img = document.createElement('img')
      img.src = this.imageUrl
      img.alt = 'Imagen'
      img.style.maxWidth = '150px'
      img.style.maxHeight = '150px'
      img.className = 'img-fluid rounded me-1'
      const range = this.savedRange
      range.insertNode(img)
      range.setStartAfter(img)
      range.setEndAfter(img)
      this.onInput()
      this.$refs.editor.focus()
      this.hideImageModal()
      this.savedRange = null
    },
  },
}
</script>

<style scoped>
[contenteditable][placeholder]:empty:before {
  content: attr(placeholder);
  color: #6c757d;
  pointer-events: none;
  display: block;
}
[contenteditable] {
  border-radius: 0.375rem;
  padding: 0.375rem 0.75rem;
}
</style>
