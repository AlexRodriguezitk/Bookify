<template>
  <main>
    <div
      class="d-flex mb-1"
      :class="{
        'justify-content-end': isOwnInteraction,
        'justify-content-start': !isOwnInteraction,
      }"
    >
      <div
        class="message-bubble p-2 shadow-sm"
        :class="{
          'own-message text-white bg-primary': isOwnInteraction,
          'other-message text-dark bg-light': !isOwnInteraction,
        }"
      >
        <!-- Encabezado -->
        <div class="d-flex align-items-center mb-2">
          <!-- Avatar -->
          <img
            :src="avatarImage"
            alt="User Profile Image"
            class="rounded-circle border me-2"
            width="32"
            height="32"
          />

          <small class="fw-bold" :class="{ 'text-white': isOwnInteraction }">
            {{ interaction.user.username }}
          </small>
        </div>

        <!-- Mensaje enriquecido -->
        <div class="px-1" v-html="safeMessage"></div>
      </div>
    </div>

    <!-- Fecha -->
    <div
      class="mb-3"
      :class="{
        'text-end me-2': isOwnInteraction,
        'text-start ms-2': !isOwnInteraction,
      }"
    >
      <small class="text-muted">
        {{ formatDate(interaction.interaction_date) }}
      </small>
    </div>
  </main>
</template>

<script>
import { useUserStore } from '@/stores/user'
import DOMPurify from 'dompurify'

export default {
  name: 'InteractionC',
  props: { interaction: Object },
  data() {
    return { userStore: useUserStore() }
  },
  computed: {
    isOwnInteraction() {
      return this.userStore.username === this.interaction.user.username
    },
    avatarImage() {
      const name = this.interaction.user.name
      const username = this.interaction.user.username
      const image =
        username === this.userStore.username
          ? this.userStore.profile_image
          : this.interaction.user.profile_image
      return (
        image || `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=random`
      )
    },
    safeMessage() {
      const raw = this.interaction.message || ''
      const container = document.createElement('div')
      container.innerHTML = raw
      const isOwn = this.isOwnInteraction

      // 1️⃣ Reemplazar imágenes por anchors "Ver"
      container.querySelectorAll('img').forEach((img) => {
        const url = img.src
        const a = document.createElement('a')
        a.href = url
        a.target = '_blank'
        a.rel = 'noopener noreferrer'
        a.className = `btn btn-sm ms-1 ${isOwn ? 'btn-outline-light' : 'btn-outline-primary'} image-link`
        a.textContent = 'Ver'
        img.replaceWith(a)
      })

      // 2️⃣ Reemplazar links existentes por anchors "Ir" solo si no son imágenes
      container.querySelectorAll('a:not(.image-link)').forEach((a) => {
        const url = a.href
        a.href = url
        a.target = '_blank'
        a.rel = 'noopener noreferrer'
        a.className = `btn btn-sm ms-1 ${isOwn ? 'btn-outline-light' : 'btn-outline-success'}`
        // Mantener texto original
      })

      // 3️⃣ Detectar URLs en texto plano
      const urlRegex = /https?:\/\/[^\s<>"']+/g
      function linkifyTextNode(textNode) {
        const text = textNode.nodeValue
        let lastIndex = 0
        const frag = document.createDocumentFragment()
        let m
        while ((m = urlRegex.exec(text)) !== null) {
          const url = m[0]
          const idx = m.index
          if (idx > lastIndex) frag.appendChild(document.createTextNode(text.slice(lastIndex, idx)))

          const isImage = /\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i.test(url)
          const a = document.createElement('a')
          a.href = url
          a.target = '_blank'
          a.rel = 'noopener noreferrer'
          a.className = `btn btn-sm ms-1 ${isImage ? (isOwn ? 'btn-outline-light' : 'btn-outline-primary') : isOwn ? 'btn-outline-light' : 'btn-outline-success'}`
          a.textContent = isImage ? 'Ver' : 'Ir'
          frag.appendChild(a)
          lastIndex = idx + url.length
        }
        if (lastIndex < text.length)
          frag.appendChild(document.createTextNode(text.slice(lastIndex)))
        if (frag.childNodes.length) textNode.parentNode.replaceChild(frag, textNode)
      }

      function walk(node) {
        Array.from(node.childNodes).forEach((child) => {
          if (child.nodeType === Node.TEXT_NODE) {
            if (urlRegex.test(child.nodeValue)) linkifyTextNode(child)
          } else if (child.nodeType === Node.ELEMENT_NODE) walk(child)
        })
      }
      walk(container)

      return DOMPurify.sanitize(container.innerHTML, {
        ALLOWED_TAGS: ['a', 'b', 'i', 'u', 'code', 'br', 'small', 'strong', 'em', 'p', 'span'],
        ALLOWED_ATTR: ['href', 'target', 'rel', 'class', 'title'],
      })
    },
  },
  methods: {
    formatDate(value) {
      if (!value) return '-'
      return new Date(value).toLocaleDateString('es-CO', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      })
    },
  },
}
</script>

<style scoped>
.message-bubble {
  max-width: 70%;
  border-radius: 16px;
  padding: 10px;
  word-break: break-word;
}

.own-message {
  border-bottom-right-radius: 4px;
  border-bottom-left-radius: 16px;
  border-top-left-radius: 16px;
}

.other-message {
  border-bottom-left-radius: 4px;
  border-bottom-right-radius: 16px;
  border-top-right-radius: 16px;
}
</style>
