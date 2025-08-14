<template>
  <div class="d-flex flex-column gap-2 p-2 border-top">
    <!-- Toolbar -->
    <div class="d-flex gap-1 mb-1 flex-wrap align-items-center">
      <!-- Estilos básicos -->
      <button class="btn btn-sm btn-primary" @mousedown.prevent="execCommand('bold')">
        <i class="fas fa-bold"></i>
      </button>
      <button class="btn btn-sm btn-primary" @mousedown.prevent="execCommand('italic')">
        <i class="fas fa-italic"></i>
      </button>
      <button class="btn btn-sm btn-primary" @mousedown.prevent="execCommand('underline')">
        <i class="fas fa-underline"></i>
      </button>

      <!-- Listas -->
      <button
        class="btn btn-sm btn-primary"
        @mousedown.prevent="execCommand('insertUnorderedList')"
      >
        <i class="fas fa-list-ul"></i>
      </button>
      <button class="btn btn-sm btn-primary" @mousedown.prevent="execCommand('insertOrderedList')">
        <i class="fas fa-list-ol"></i>
      </button>

      <!-- Alineación -->
      <button class="btn btn-sm btn-primary" @mousedown.prevent="execCommand('justifyLeft')">
        <i class="fas fa-align-left"></i>
      </button>
      <button class="btn btn-sm btn-primary" @mousedown.prevent="execCommand('justifyCenter')">
        <i class="fas fa-align-center"></i>
      </button>
      <button class="btn btn-sm btn-primary" @mousedown.prevent="execCommand('justifyRight')">
        <i class="fas fa-align-right"></i>
      </button>
      <button class="btn btn-sm btn-primary" @mousedown.prevent="execCommand('justifyFull')">
        <i class="fas fa-align-justify"></i>
      </button>

      <!-- Colores -->
      <input
        type="color"
        v-model="foreColor"
        @input="execCommand('foreColor', foreColor)"
        title="Color de texto"
      />
      <input
        type="color"
        v-model="backColor"
        @input="execCommand('hiliteColor', backColor)"
        title="Color de fondo"
      />

      <!-- Enlaces, imágenes y archivos -->
      <button class="btn btn-sm btn-primary" @mousedown.prevent="openLinkModal">
        <i class="fas fa-link"></i>
      </button>
      <button class="btn btn-sm btn-primary" @mousedown.prevent="openImageModal">
        <i class="fas fa-image"></i>
      </button>
      <button class="btn btn-sm btn-primary" @click="openFileInput">
        <i class="fas fa-file"></i>
      </button>
      <input type="file" ref="fileInput" style="display: none" @change="onFileSelected" multiple />
    </div>

    <!-- Editor -->
    <div
      id="editor"
      ref="editor"
      contenteditable="true"
      class="form-control flex-grow-1 editor-scroll"
      @input="onInput"
      @keydown.enter="handleEnter"
      placeholder="Escribe un mensaje..."
    ></div>

    <!-- Botón enviar -->
    <div class="d-flex justify-content-end mt-1">
      <button class="btn btn-primary" :disabled="sending || !hasContent" @click="onSend">
        <i class="fas fa-paper-plane"></i> Enviar
      </button>
    </div>

    <!-- Modales de enlace e imagen -->
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
            <input type="file" accept="image/*" class="form-control mb-2" @change="onFileChange" />
            <input
              type="text"
              v-model="imageUrl"
              class="form-control"
              placeholder="URL de la imagen"
            />
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="hideImageModal">Cancelar</button>
            <button class="btn btn-success" :disabled="!imageUrl" @click="confirmInsertImage">
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
  data() {
    return {
      content: '',
      savedRange: null,
      linkTitle: '',
      linkUrl: '',
      imageUrl: '',
      selectedFile: null,
      linkModalInstance: null,
      imageModalInstance: null,
      foreColor: '#000000',
      backColor: '#ffffff',
    }
  },
  computed: {
    hasContent() {
      return this.content.trim().length > 0
    },
  },
  mounted() {
    this.linkModalInstance = new bootstrap.Modal(this.$refs.linkModal, { backdrop: 'static' })
    this.imageModalInstance = new bootstrap.Modal(this.$refs.imageModal, { backdrop: 'static' })
  },
  methods: {
    execCommand(command, value = null) {
      this.restoreSelection()
      document.execCommand(command, false, value)
      this.onInput()
      this.$refs.editor.focus()
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
      this.$emit('send', this.content)
      this.$refs.editor.innerHTML = ''
      this.content = ''
    },
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
    openLinkModal() {
      this.saveSelection()
      this.linkModalInstance.show()
    },
    hideLinkModal() {
      this.linkTitle = ''
      this.linkUrl = ''
      this.linkModalInstance.hide()
    },
    openImageModal() {
      this.saveSelection()
      this.imageModalInstance.show()
    },
    hideImageModal() {
      this.imageUrl = ''
      this.selectedFile = null
      this.imageModalInstance.hide()
    },
    confirmInsertLink() {
      if (!this.linkTitle || !this.linkUrl) return
      const editor = this.$refs.editor
      const a = document.createElement('a')
      let url = this.linkUrl.trim()
      if (!url.startsWith('http')) url = 'https://' + url
      a.href = url
      a.target = '_blank'
      a.rel = 'noopener noreferrer'
      a.textContent = this.linkTitle
      if (this.savedRange && editor.contains(this.savedRange.commonAncestorContainer)) {
        this.restoreSelection()
        this.savedRange.deleteContents()
        this.savedRange.insertNode(a)
      } else {
        editor.appendChild(a)
      }
      this.onInput()
      this.savedRange = null
      this.hideLinkModal()
      this.$refs.editor.focus()
    },
    onFileChange(e) {
      const file = e.target.files[0]
      if (!file) return
      this.selectedFile = file
      this.imageUrl = URL.createObjectURL(file)
    },
    async confirmInsertImage() {
      if (!this.imageUrl) return
      let url = this.imageUrl
      if (this.selectedFile) {
        const fd = new FormData()
        fd.append('file', this.selectedFile)
        const res = await makeQuery('/Upload', 'POST', fd, true)
        url = res.file_url
      }
      const editor = this.$refs.editor
      const img = document.createElement('img')
      img.src = url
      img.alt = 'Imagen'
      img.style.maxWidth = '150px'
      img.style.maxHeight = '150px'
      img.className = 'img-fluid rounded me-1'
      if (this.savedRange && editor.contains(this.savedRange.commonAncestorContainer)) {
        this.restoreSelection()
        this.savedRange.insertNode(img)
      } else {
        editor.appendChild(img)
      }
      this.onInput()
      this.savedRange = null
      this.hideImageModal()
      this.$refs.editor.focus()
    },
    openFileInput() {
      this.$refs.fileInput.click()
    },
    async onFileSelected(e) {
      const files = Array.from(e.target.files)
      if (!files.length) return
      const editor = this.$refs.editor
      const filesContainer = document.createElement('files')
      for (let file of files) {
        const fd = new FormData()
        fd.append('file', file)
        const res = await makeQuery('/Upload', 'POST', fd, true)
        const a = document.createElement('a')
        a.href = res.file_url
        a.textContent = file.name
        a.target = '_blank'
        a.className = 'd-block'
        filesContainer.appendChild(a)
      }
      editor.appendChild(filesContainer)
      this.onInput()
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

.editor-scroll {
  min-height: 80px;
  max-height: 110px; /* límite de altura antes del scroll */
  overflow-y: auto;
  border-radius: 0.375rem;
  padding: 0.375rem 0.75rem;
}
</style>
