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
    <div class="overflow-auto" style="max-height: 400px">
      <div class="tab-content">
        <div class="tab-pane fade show active" id="i">
          <ul class="list-group">
            <li class="list-group-item"><strong>ID:</strong> {{ ticket.id }}</li>

            <!-- Título -->
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div class="flex-grow-1">
                <template v-if="!isEditingTitle || readOnly">
                  <strong>Titulo:</strong>
                  <span id="ticket-title">{{ ticket.title }}</span>
                </template>
                <template v-else>
                  <div class="input-group">
                    <input
                      class="form-control form-control-sm"
                      v-model="editedTitle"
                      @focusout="ToggleEditTitle"
                      type="text"
                    />
                  </div>
                </template>
              </div>
              <button
                v-if="!readOnly"
                class="btn btn-sm btn-outline-secondary ms-2"
                @click="ToggleEditTitle"
              >
                <i class="fa-solid fa-edit"></i>
              </button>
            </li>

            <!-- Categoría -->
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <strong>Categoría:</strong>
              <span class="ms-2">
                {{
                  ticket.category && ticket.category.name ? ticket.category.name : 'Sin categoría'
                }}
              </span>
            </li>

            <!-- Estado -->
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><strong>Estado:</strong></span>
              <template v-if="readOnly">
                <span class="badge bg-secondary ms-2">{{ ticket.status }}</span>
              </template>
              <template v-else>
                <select
                  v-model="localStatus"
                  class="form-select form-select-sm w-auto"
                  @change="updateStatus"
                >
                  <option value="NEW">Nuevo</option>
                  <option value="IN_PROGRESS">En progreso</option>
                  <option value="CLOSED">Cerrado</option>
                </select>
              </template>
            </li>

            <!-- Creación -->
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><strong>Created At:</strong></span>
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

            <!-- Cliente -->
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <strong>Cliente:</strong>
              <span class="d-flex align-items-center">
                <img
                  :src="
                    ticket.client?.profile_image
                      ? ticket.client.profile_image
                      : `https://ui-avatars.com/api/?name=${ticket.client?.name}&background=random`
                  "
                  alt="Profile Image"
                  class="rounded-circle"
                  width="30"
                  height="30"
                />
                <router-link
                  v-if="!readOnly"
                  :to="`/users/${ticket.client?.id}`"
                  class="ms-2 text-decoration-none text-dark"
                >
                  {{ ticket.client?.name }}
                </router-link>
                <span v-else class="ms-2">{{ ticket.client?.name }}</span>
              </span>
            </li>

            <!-- Asesor -->
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <strong>Asignado a:</strong>
              <span class="d-flex align-items-center">
                <template v-if="ticket.asesor">
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
                    v-if="!readOnly"
                    :to="`/users/${ticket.asesor.id}`"
                    class="ms-2 text-decoration-none text-dark"
                  >
                    {{ ticket.asesor.name }}
                  </router-link>
                  <span v-else class="ms-2">{{ ticket.asesor.name }}</span>
                </template>
                <template v-else>
                  <span class="text-muted ms-2">Sin asignar</span>
                </template>
              </span>
            </li>

            <!-- Descripción -->
            <li class="list-group-item">
              <div class="mb-1"><strong>Descripción del caso:</strong></div>
              <p v-html="ticket.description?.replace(/\n/g, '<br>')"></p>
            </li>
          </ul>
        </div>

        <!-- Campos personalizados -->
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
        client: {},
        asesor: null,
        custom_values: [],
        custom_fields: [],
      }),
    },
    readOnly: {
      // 👈 nuevo prop
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      isEditingTitle: false,
      originalTitle: this.ticket.title,
      localStatus: this.ticket.status,
      editedTitle: this.ticket.title,
    }
  },
  watch: {
    'ticket.status'(newVal) {
      this.localStatus = newVal
    },
  },
  methods: {
    updateStatus() {
      if (!this.readOnly) {
        this.$emit('update-status', this.localStatus)
      }
    },
    ToggleEditTitle() {
      if (this.readOnly) return
      this.isEditingTitle = !this.isEditingTitle
      if (this.isEditingTitle) {
        this.editedTitle = this.ticket.title
        this.$nextTick(() => {
          const input = this.$el.querySelector('input[type="text"]')
          if (input) input.focus()
        })
      } else {
        if (this.editedTitle !== this.originalTitle) {
          this.$emit('update-title', this.editedTitle)
          this.originalTitle = this.editedTitle
        }
      }
    },
    getFieldValue(customFieldId) {
      const match = this.ticket.custom_values.find((v) => v.custom_field_id === customFieldId)
      return match ? match.value : null
    },
    formatDate(value) {
      if (!value) return '-'
      return new Date(value).toLocaleDateString('es-ES')
    },
  },
}
</script>
