<template>
  <main class="h-100">
    <div class="chat-container h-100 d-flex flex-column" style="max-height: 500px">
      <div ref="chatContainer" class="chat-messages flex-grow-1 overflow-auto">
        <template v-if="publict">
          <div v-for="interaction in filteredInteractions" :key="interaction.id">
            <interaction-c :interaction="interaction" />
          </div>
        </template>
        <template v-else>
          <div v-for="interaction in filteredInteractions" :key="interaction.id">
            <interaction-c :interaction="interaction" />
          </div>
        </template>
      </div>

      <MessageInputC :buttonText="'Enviar'" :sending="sending" @send="handleSend" />
    </div>
  </main>
</template>

<script>
import InteractionC from './InteractionC.vue'
import MessageInputC from '../Utils/MessageInputC.vue'

export default {
  name: 'InteractionsC',
  components: {
    InteractionC,
    MessageInputC,
  },
  props: {
    interactions: {
      type: Array,
      required: true,
    },
    publict: {
      type: Boolean,
      required: true,
    },
    ticketId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      sending: false,
    }
  },
  computed: {
    filteredInteractions() {
      if (this.publict) {
        return this.interactions.filter((i) => i.type === 'INTERN')
      } else {
        return this.interactions.filter((i) => i.type === 'COMMENT')
      }
    },
  },
  watch: {
    // Watch for a change in the number of filtered interactions
    filteredInteractions(newInteractions, oldInteractions) {
      // Only scroll if the number of interactions has increased
      if (newInteractions.length > oldInteractions.length) {
        this.$nextTick(() => {
          this.scrollToBottom()
        })
      }
    },
  },
  mounted() {
    // Initial scroll on page load
    this.$nextTick(() => {
      this.scrollToBottom()
    })
  },
  methods: {
    handleSend(content) {
      if (!content) return
      this.sending = true
      const type = this.publict ? 'INTERN' : 'COMMENT'
      this.$emit('send', { content, type })
    },

    scrollToBottom() {
      const container = this.$refs.chatContainer
      if (container) {
        container.scrollTop = container.scrollHeight
      }
    },
  },
}
</script>

<style scoped>
.chat-container {
  background-color: #f8f9fa; /* Fondo claro */
  border: 2px solid #dee2e6;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.chat-messages {
  padding: 1rem;
  scroll-behavior: smooth;
}

/* Scroll más fino */
.chat-messages::-webkit-scrollbar {
  width: 6px;
}
.chat-messages::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.2);
  border-radius: 4px;
}
</style>
