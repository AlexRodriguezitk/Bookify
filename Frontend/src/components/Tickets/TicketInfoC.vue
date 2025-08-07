<template>
  <div class="ticket-info">
    <ul class="nav nav-tabs mb-3 w-100" style="display: flex">
      <li class="nav-item flex-fill text-center">
        <a class="nav-link active" data-bs-toggle="tab" href="#i">
          <i class="fa-solid fa-circle-info"></i>
        </a>
      </li>
      <li class="nav-item flex-fill text-center">
        <a class="nav-link" data-bs-toggle="tab" href="#custom">
          <i class="fa-solid fa-database"></i>
        </a>
      </li>
    </ul>
    <div class="overflow-auto" style="max-height: 350px">
      <div class="tab-content">
        <div class="tab-pane fade show active" id="i">
          <ul class="list-group">
            <li class="list-group-item"><strong>ID:</strong> {{ ticket.id }}</li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div class="flex-grow-1">
                <div v-if="!isEditingTitle">
                  <strong>Titulo:</strong> <span id="ticket-title">{{ ticket.title }}</span>
                </div>
                <div v-else class="input-group">
                  <input
                    class="form-control form-control-sm"
                    v-model="ticket.title"
                    @focusout="ToggleEditTitle"
                    type="text"
                  />
                </div>
              </div>
              <button class="btn btn-sm btn-outline-secondary ms-2" @click="ToggleEditTitle">
                <i class="fa-solid fa-edit"></i>
              </button>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><strong>Estado:</strong></span>
              <select
                v-model="ticket.status"
                class="form-select form-select-sm w-auto"
                @change="updateStatus"
              >
                <option value="NEW">Nuevo</option>
                <option value="IN_PROGRESS">En progreso</option>
                <option value="CLOSED">Cerrado</option>
              </select>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>
                <strong>Created At:</strong>
              </span>
              <span class="badge bg-primary ms-2">
                {{
                  new Date(ticket.creation_date).toLocaleString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                  })
                }}
              </span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <strong>Cliente:</strong>
              <span class="d-flex align-items-center">
                <img
                  :src="
                    ticket.client.profile_image
                      ? ticket.client.profile_image
                      : `https://ui-avatars.com/api/?name=${ticket.client.name}&background=random`
                  "
                  alt="Profile Image"
                  class="rounded-circle"
                  width="30"
                  height="30"
                />
                <router-link
                  :to="`/users/${ticket.client.id}`"
                  class="ms-2 text-decoration-none text-dark"
                >
                  {{ ticket.client.name }}
                </router-link>
              </span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <strong>Asignado a:</strong>
              <span class="d-flex align-items-center">
                <img
                  :src="
                    ticket.asesor.profile_image
                      ? ticket.asesor.profile_image
                      : `https://ui-avatars.com/api/?name=${ticket.asesor.name}&background=random`
                  "
                  alt="Profile Image"
                  class="rounded-circle"
                  width="30"
                  height="30"
                />
                <router-link
                  :to="`/users/${ticket.asesor.id}`"
                  class="ms-2 text-decoration-none text-dark"
                >
                  {{ ticket.asesor.name }}
                </router-link>
              </span>
            </li>
            <li class="list-group-item">
              <div class="mb-1">
                <strong>Descripción del caso:</strong>
              </div>
              <p v-html="ticket.description.replace(/\n/g, '<br>')"></p>
            </li>
          </ul>
        </div>
        <div class="tab-pane fade" id="custom">
          <ul class="list-group">
            <li class="list-group-item" v-for="field in ticket.custom_fields" :key="field.id">
              <template v-if="getFieldValue(field.id)">
                <div v-if="field.type === 'TEXT'">
                  <div class="mb-1">
                    <strong>{{ field.name }}:</strong>
                  </div>
                  <p v-html="getFieldValue(field.id).replace(/\n/g, '<br>')" class="mb-0"></p>
                </div>
                <div v-else-if="field.type === 'NUMBER'">
                  <strong>{{ field.name }}:</strong>
                  <span class="ms-2">{{ getFieldValue(field.id) }}</span>
                </div>
                <div v-else-if="field.type === 'BOOLEAN'">
                  <strong>{{ field.name }}:</strong>
                  <span
                    class="badge ms-2"
                    :class="getFieldValue(field.id) === '1' ? 'bg-success' : 'bg-danger'"
                  >
                    {{ getFieldValue(field.id) === '1' ? 'Sí' : 'No' }}
                  </span>
                </div>
                <div v-else-if="field.type === 'DATE'">
                  <strong>{{ field.name }}:</strong>
                  <span class="badge bg-primary ms-2">
                    {{ formatDate(getFieldValue(field.id)) }}
                  </span>
                </div>
                <div v-else>
                  <strong>{{ field.name }}:</strong>
                  <span class="ms-2">{{ getFieldValue(field.id) }}</span>
                </div>
              </template>
              <template v-else>
                <strong>{{ field.name }}:</strong>
                <span class="text-muted ms-2">-</span>
              </template>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TicketInfoC',
  props: {
    ticket: {
      type: Object,
      required: true,
      default: () => ({
        id: '',
        title: '',
        status: '',
        creation_date: '',
        client: '',
        asesor: '',
        custom_values: [],
        custom_fields: [],
      }),
    },
  },
  data() {
    return {
      isEditingTitle: false,
      originalTitle: this.ticket.title,
    }
  },
  methods: {
    updateStatus() {
      this.$emit('update-status', this.ticket.status)
    },
    ToggleEditTitle() {
      this.isEditingTitle = !this.isEditingTitle
      //Focus imput when editing title
      if (this.isEditingTitle) {
        this.$nextTick(() => {
          const input = this.$el.querySelector('input[type="text"]')
          if (input) {
            input.focus()
          }
        })
      }
      // Emit the updated title when editing is toggled off
      if (!this.isEditingTitle) {
        if (this.ticket.title !== this.originalTitle) {
          this.$emit('update-title', this.ticket.title)
          this.originalTitle = this.ticket.title // Update original title to the new value
        }
      }
    },

    getFieldValue(customFieldId) {
      const match = this.ticket.custom_values.find((v) => v.custom_field_id === customFieldId)
      return match ? match.value : null
    },

    getFieldValueRef(customFieldId) {
      return {
        get: () => this.customValuesMap[customFieldId],
        set: (val) => {
          this.customValuesMap[customFieldId] = val
        },
      }
    },
    formatDate(value) {
      if (!value) return '-'
      // Ajusta formato según tus necesidades
      return new Date(value).toLocaleDateString('en-US')
    },
  },
}
</script>
