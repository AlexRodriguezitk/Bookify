<template>
  <div class="container mt-4">
    <!-- Loader -->
    <div v-if="loading" class="text-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>

    <!-- ✅ Vista después de crear ticket -->
    <div v-else-if="ticketCreated">
      <div class="card shadow-sm p-4 text-center">
        <h2 class="text-success mb-3">✅ Gracias por enviar el ticket</h2>
        <p class="mb-3">
          Hemos recibido tu solicitud correctamente. Puedes dar seguimiento con el siguiente enlace:
        </p>
        <a :href="`/follow/${ticketCreated.id}`" class="btn btn-outline-primary">
          Ver seguimiento del ticket
        </a>
      </div>
    </div>

    <!-- Formulario -->
    <div v-else>
      <h1 class="mb-4 text-dark">Crear ticket: {{ category?.name || 'Sin categoría' }}</h1>

      <form @submit.prevent="openConfirmModal" class="card p-4 shadow-sm">
        <!-- Sección: campos base -->
        <div class="mb-4">
          <div class="mb-3">
            <label for="title" class="form-label">Título</label>
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
            <label for="description" class="form-label">Descripción</label>
            <textarea
              id="description"
              v-model="form.description"
              class="form-control"
              rows="3"
              placeholder="Describa el problema"
              required
            ></textarea>
          </div>
        </div>

        <hr />

        <!-- Sección: campos dinámicos -->
        <div>
          <div v-for="field in paginatedFields" :key="field.id" class="mb-3">
            <label class="form-label">{{ field.name }}</label>

            <!-- Campo tipo TEXT -->
            <textarea
              v-if="field.type === 'TEXT'"
              class="form-control"
              rows="3"
              style="resize: vertical"
              v-model="dynamicValues[field.id]"
            ></textarea>

            <!-- Campo tipo NUMBER -->
            <input
              v-else-if="field.type === 'NUMBER'"
              type="number"
              class="form-control"
              v-model="dynamicValues[field.id]"
            />

            <!-- Campo tipo DATE -->
            <input
              v-else-if="field.type === 'DATE'"
              type="date"
              class="form-control"
              v-model="dynamicValues[field.id]"
            />

            <!-- Campo tipo BOOLEAN -->
            <div v-else-if="field.type === 'BOOLEAN'" class="form-check">
              <input type="checkbox" class="form-check-input" v-model="dynamicValues[field.id]" />
              <label class="form-check-label">Sí / No</label>
            </div>
          </div>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-3">
          <nav>
            <ul class="pagination">
              <li
                class="page-item"
                v-for="page in totalPages"
                :key="page"
                :class="{ active: currentPage === page }"
              >
                <button
                  type="button"
                  class="page-link rounded-circle mx-1"
                  style="width: 40px; height: 40px; text-align: center"
                  @click="changePage(page)"
                >
                  {{ page }}
                </button>
              </li>
            </ul>
          </nav>
        </div>

        <!-- Botón enviar -->
        <div class="text-end">
          <button type="submit" class="btn btn-primary mt-3">Crear Ticket</button>
        </div>
      </form>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
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

export default {
  name: 'PortalView',
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
      ticketCreated: null, // ✅ Guardamos el ticket creado
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
        }, [])

        const payload = {
          ...this.form,
          category: this.category.id,
          status: 'NEW',
          priority: 'LOW',
          custom_values,
        }

        const response = await makeQuery('/tickets', 'POST', payload)

        // ✅ Guardamos ticket creado para mostrar la vista de agradecimiento
        this.ticketCreated = response.data[0]

        this.closeConfirmModal()
      } catch (error) {
        console.error('Error creando ticket:', error)
        alert('❌ Error al crear ticket')
      }
    },
    async fetchCategory() {
      try {
        const category_id = this.$route.params.id
        const response = await makeQuery(`/categories/${category_id}`, 'GET')
        this.category = response.data[0]
      } catch (error) {
        console.error(error)
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
  },
}
</script>
