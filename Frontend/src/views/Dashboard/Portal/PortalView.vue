<template>
  <div class="container my-5">
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>

    <AlertC v-if="alertMessage" :message="alertMessage" :color="alertColor" />

    <div v-if="ticketCreated" class="card shadow-sm p-5 text-center">
      <h2 class="text-success fw-bold mb-3">Ticket enviado</h2>
      <p class="mb-4 lead">
        Tu solicitud ha sido recibida correctamente. Puedes dar seguimiento con el siguiente enlace:
      </p>
      <router-link :to="`/follow/${ticketCreated.id}`" class="btn btn-primary btn-lg">
        Ver seguimiento del ticket
      </router-link>
    </div>

    <div v-else>
      <h1 class="mb-4 text-dark fw-bold">Crear ticket: {{ category?.name || 'Sin categoría' }}</h1>

      <form @submit.prevent="openConfirmModal" class="card p-4 shadow-sm">
        <div class="mb-4">
          <div class="mb-3">
            <label for="title" class="form-label fw-bold">Título</label>
            <input
              type="text"
              id="title"
              v-model="form.title"
              class="form-control"
              placeholder="Ingrese el título del ticket"
              required
            />
          </div>

          <div class="mb-3">
            <label for="description" class="form-label fw-bold">Descripción</label>
            <textarea
              id="description"
              v-model="form.description"
              class="form-control"
              rows="4"
              placeholder="Describa el problema en detalle"
              required
            ></textarea>
          </div>
        </div>

        <hr />

        <div v-if="fields.length > 0">
          <h4 class="mb-3 fw-bold">Detalles adicionales</h4>
          <div v-for="field in paginatedFields" :key="field.id" class="mb-3">
            <label class="form-label">{{ field.name }}</label>

            <textarea
              v-if="field.type === 'TEXT'"
              class="form-control"
              rows="3"
              v-model="dynamicValues[field.id]"
            ></textarea>

            <input
              v-else-if="field.type === 'NUMBER'"
              type="number"
              class="form-control"
              v-model="dynamicValues[field.id]"
            />

            <input
              v-else-if="field.type === 'DATE'"
              type="date"
              class="form-control"
              v-model="dynamicValues[field.id]"
            />

            <div v-else-if="field.type === 'BOOLEAN'" class="form-check form-switch">
              <input type="checkbox" class="form-check-input" v-model="dynamicValues[field.id]" />
              <label class="form-check-label">Sí / No</label>
            </div>
          </div>
        </div>

        <div v-if="totalPages > 1" class="d-flex justify-content-center mt-3">
          <nav>
            <ul class="pagination">
              <li
                class="page-item"
                v-for="page in totalPages"
                :key="page"
                :class="{ active: currentPage === page }"
              >
                <button type="button" class="page-link" @click="changePage(page)">
                  {{ page }}
                </button>
              </li>
            </ul>
          </nav>
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-primary btn-lg mt-3">Crear Ticket</button>
        </div>
      </form>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-light">
            <h5 class="modal-title">Confirmar creación</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">¿Está seguro de crear este ticket?</div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Cancelar
            </button>
            <button type="button" class="btn btn-primary" @click="handleSubmit">Confirmar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { makeQuery } from '@/services/api'
import { Modal } from 'bootstrap'
import AlertC from '@/components/AlertC.vue'

export default {
  name: 'PortalView',
  components: { AlertC },
  data() {
    return {
      form: {
        title: '',
        description: '',
      },
      category: null,
      fields: [],
      dynamicValues: {},
      loading: true,
      currentPage: 1,
      fieldsPerPage: 5,
      ticketCreated: null,
      alertMessage: '',
      alertColor: '',
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.fields.length / this.fieldsPerPage) || 1
    },
    paginatedFields() {
      const start = (this.currentPage - 1) * this.fieldsPerPage
      return this.fields.slice(start, start + this.fieldsPerPage)
    },
  },
  created() {
    this.fetchCategory()
    this.fetchFields()
  },
  methods: {
    async handleSubmit() {
      try {
        const custom_values = this.fields.reduce((acc, f) => {
          const raw = this.dynamicValues[f.id]

          if (f.type === 'BOOLEAN') {
            acc.push({
              custom_field_id: String(f.id),
              value: raw ? '1' : '0',
            })
          } else {
            if (raw !== undefined && raw !== null && String(raw).trim() !== '') {
              acc.push({
                custom_field_id: String(f.id),
                value: raw,
              })
            }
          }
          return acc
        }, []) // Se corrigió para que sea un array vacío, no null

        const payload = {
          ...this.form,
          category: this.category.id,
          status: 'NEW',
          priority: 'LOW',
          custom_values,
        }

        const response = await makeQuery('/tickets', 'POST', payload)

        this.ticketCreated = response.data[0]
        this.showAlert('Ticket creado con éxito', 'success')
        this.closeConfirmModal()
      } catch (error) {
        console.error('Error creando ticket:', error)
        this.showAlert('Error al crear el ticket', 'danger')
      }
    },
    async fetchCategory() {
      try {
        const category_id = this.$route.params.id
        const response = await makeQuery(`/categories/${category_id}`, 'GET')

        // Verifica si la categoría fue encontrada
        if (response.data && response.data.length > 0) {
          this.category = response.data[0]
        } else {
          // Si no hay datos, redirige al dashboard
          this.$router.push('/dashboard')
        }
      } catch (error) {
        console.error(error)
        // En caso de un error en la solicitud (ej. 404), redirige al dashboard
        this.$router.push('/dashboard')
      } finally {
        this.loading = false
      }
    },
    async fetchFields() {
      const categoryId = this.$route.params.id
      try {
        const response = await makeQuery(`/fields/category/${categoryId}`, 'GET')
        this.fields = response.data || []
      } catch (error) {
        console.error('Error fetching fields:', error)
      }
    },
    changePage(page) {
      this.currentPage = page
    },
    openConfirmModal() {
      const modal = new Modal(document.getElementById('confirmModal'))
      modal.show()
    },
    closeConfirmModal() {
      const modalEl = document.getElementById('confirmModal')
      const modal = Modal.getInstance(modalEl)
      modal.hide()
    },
    showAlert(message, color) {
      this.alertMessage = message
      this.alertColor = color
      setTimeout(() => {
        this.alertMessage = ''
      }, 3000)
    },
  },
}
</script>
