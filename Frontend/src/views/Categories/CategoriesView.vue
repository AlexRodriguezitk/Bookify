<template>
  <main class="container my-4">
    <div v-if="category" class="row g-4">
      <!-- Columna: Detalles del rol -->
      <section class="col-12 col-md-6">
        <h2 class="h5 mb-3 text-center text-md-start">Detalles de la Categoría</h2>
        <ul class="list-group mb-3">
          <li class="list-group-item">ID: {{ category.id }}</li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span
              >Nombre: <strong>{{ category.name }}</strong></span
            >
            <button class="btn btn-sm" @click="openEditCategoryModal(category)">
              <i class="fas fa-edit"></i>
            </button>
          </li>
        </ul>
        <div class="text-end">
          <button class="btn btn-sm btn-danger" @click="openDeleteModal(category.id)">
            <i class="fas fa-trash"></i>
            <span class="d-none d-md-inline ms-2">Eliminar</span>
          </button>
        </div>

        <h2 class="h5 mb-3 text-center text-md-start">Tickets con esta categoría</h2>
        <span>proximamente...</span>
      </section>
      <!-- Columna: Permisos -->
      <section class="col-12 col-md-6">
        <h2 class="h5 mb-3 text-center text-md-start">Campos personalizados</h2>
        <div class="fields-grid-container p-2 border rounded">
          <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
            <h5 class="card-title mb-0 me-auto">Campos</h5>
            <button class="btn btn-success btn-sm me-2" @click="openAddFieldModal">
              <i class="fas fa-plus"></i>
              <span class="d-none d-sm-inline ms-2">Añadir Campo</span>
            </button>
          </div>
          <FieldsList
            :fields="fields"
            @edit-field="openEditFieldModal"
            @delete-field="deleteField"
          />
        </div>
      </section>
    </div>
    <!-- Preloader -->
    <div v-else class="text-center my-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-3">Cargando Categoría...</p>
    </div>
    <!-- Category Modal -->
    <div class="modal fade" ref="categoryModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ isEditingCategory ? 'Editar categoría' : 'Añadir categoría' }}
            </h5>
            <button type="button" class="btn-close" @click="closeCategoryModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="isEditingCategory ? updateCategory() : addCategory()">
              <div class="mb-3">
                <label for="categoryName" class="form-label">Nombre de la categoría</label>
                <input
                  id="categoryName"
                  v-model="categoryToEdit.name"
                  class="form-control"
                  type="text"
                  placeholder="Ingrese el nombre de la categoría"
                  maxlength="15"
                  required
                />
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeCategoryModal">
                  <i class="fa-solid fa-xmark"></i>
                  <span class="ms-2">Cerrar</span>
                </button>
                <button type="submit" class="btn btn-primary">
                  <i class="fa-solid fa-floppy-disk"></i> <span class="ms-2">Guardar</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal de Confirmación de Eliminación -->
    <div class="modal fade" ref="deleteModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">¿Eliminar Categoría?</h5>
            <button type="button" class="btn-close" @click="closeDeleteModal"></button>
          </div>
          <div class="modal-body">
            <p>
              ¿Estás seguro de que deseas eliminar esta Categoría? Esta acción no se puede deshacer.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeDeleteModal">
              <i class="fa-solid fa-xmark"></i>
              <span class="ms-2">Cancelar</span>
            </button>
            <button type="button" class="btn btn-danger" @click="confirmDelete">
              <i class="fa-solid fa-trash"></i>
              <span class="ms-2">Eliminar</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal de Campo Personalizado -->
    <div class="modal fade" ref="customFieldModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar campo personalizado</h5>
            <button type="button" class="btn-close" @click="closeCustomFieldModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="isEditingField ? updateField() : addCustomField()">
              <div class="mb-3">
                <label for="fieldName" class="form-label">Campo personalizado</label>
                <input
                  id="fieldName"
                  v-model="customFieldToEdit.name"
                  class="form-control"
                  :class="{ 'is-invalid': customFieldToEdit.name.length > 100 }"
                  type="text"
                  placeholder="Ingrese el nombre del campo"
                  required
                />
                <div class="invalid-feedback">
                  El nombre del campo debe tener menos de 20 caracteres
                </div>
              </div>

              <div class="mb-3">
                <label for="fieldType" class="form-label">Tipo</label>
                <select
                  id="fieldType"
                  v-model="customFieldToEdit.type"
                  class="form-select"
                  required
                >
                  <option disabled value="">Seleccione un tipo</option>
                  <option value="TEXT">Texto</option>
                  <option value="NUMBER">Número</option>
                  <option value="DATE">Fecha</option>
                  <option value="BOOLEAN">Booleano</option>
                </select>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeCustomFieldModal">
                  <i class="fa-solid fa-xmark"></i>
                  <span class="ms-2">Cerrar</span>
                </button>
                <button type="submit" class="btn btn-primary">
                  <i class="fa-solid fa-floppy-disk"></i>
                  <span class="ms-2">Guardar</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
.fields-grid-container {
  max-height: 520px;
  overflow-y: auto;
  overflow-x: hidden;
}
.user-grid-container {
  max-height: 340px;
  overflow-y: auto;
  overflow-x: hidden;
}
</style>

<script>
import { makeQuery } from '@/services/api'
import { Modal } from 'bootstrap'
import FieldsList from '@/components/SettingsComponents/FieldsListC.vue'
export default {
  name: 'CategoriesView',
  components: { FieldsList },
  data() {
    return {
      categoryID: null,
      category: null,
      categoryToEdit: { id: null, name: '' },
      categoryIdToDelete: null,
      fields: [],
      isEditingCategory: false,
      modalCategoryInstance: null,
      deleteModalInstance: null,
      customFieldToEdit: { id: null, name: '', type: '' },
      isEditingField: false,
    }
  },
  created() {
    this.fetchCategory()
    this.fetchFields()
  },
  methods: {
    async fetchCategory() {
      const categoryId = this.$route.params.id
      try {
        const response = await makeQuery(`/categories/${categoryId}`, 'GET')
        this.category = response.data[0]
      } catch (error) {
        console.error('Error fetching category:', error)
        if (error.response?.status === 404) {
          this.$router.push('/settings')
        }
      }
    },

    async fetchFields() {
      const categoryId = this.$route.params.id
      try {
        const response = await makeQuery(`/fields/category/${categoryId}`, 'GET')
        this.fields = response.data
      } catch (error) {
        console.error('Error fetching fields:', error)
      }
    },

    openEditCategoryModal(category) {
      this.categoryToEdit = { ...category }
      this.isEditingCategory = true
      this.modalCategoryInstance = new Modal(this.$refs.categoryModal)
      this.modalCategoryInstance.show()
    },
    openAddCategoryModal() {
      this.categoryToEdit = { id: null, name: '' }
      this.isEditingCategory = false
      this.modalCategoryInstance = new Modal(this.$refs.categoryModal)
      this.modalCategoryInstance.show()
    },
    closeCategoryModal() {
      if (this.modalCategoryInstance) this.modalCategoryInstance.hide()
    },
    async updateCategory() {
      await makeQuery(`/categories/${this.categoryToEdit.id}`, 'PUT', {
        name: this.categoryToEdit.name,
      })
      this.closeCategoryModal()
      await this.fetchCategory()
    },
    async addCategory() {
      await makeQuery('/categories', 'POST', { name: this.categoryToEdit.name })
      this.closeCategoryModal()
      await this.fetchCategories()
    },
    openDeleteModal(categoryId) {
      this.categoryIdToDelete = categoryId
      this.deleteModalInstance = new Modal(this.$refs.deleteModal)
      this.deleteModalInstance.show()
    },
    closeDeleteModal() {
      if (this.deleteModalInstance) this.deleteModalInstance.hide()
    },
    async confirmDelete() {
      try {
        await makeQuery(`/categories/${this.categoryIdToDelete}`, 'DELETE')
        this.closeDeleteModal()
        this.$router.push('/settings')
      } catch (error) {
        console.error('Error al eliminar la categoría:', error)
      }
    },

    async deleteField(fieldId) {
      try {
        await makeQuery(`/fields/${fieldId}`, 'DELETE')
        await this.fetchFields()
      } catch (error) {
        console.error('Error al eliminar el campo:', error)
      }
    },

    openEditFieldModal(field) {
      this.customFieldToEdit = { ...field }
      this.isEditingField = true
      this.modalFieldInstance = new Modal(this.$refs.customFieldModal)
      this.modalFieldInstance.show()
    },

    openAddFieldModal() {
      this.customFieldToEdit = { id: null, name: '', type: '' }
      this.isEditingField = false
      this.modalFieldInstance = new Modal(this.$refs.customFieldModal)
      this.modalFieldInstance.show()
    },

    closeCustomFieldModal() {
      if (this.modalFieldInstance) this.modalFieldInstance.hide()
    },

    async updateField() {
      await makeQuery(`/fields/${this.customFieldToEdit.id}`, 'PUT', {
        name: this.customFieldToEdit.name,
        type: this.customFieldToEdit.type,
      })
      this.closeCustomFieldModal()
      await this.fetchFields()
    },

    async addCustomField() {
      await makeQuery('/fields', 'POST', {
        category: this.$route.params.id,
        name: this.customFieldToEdit.name,
        type: this.customFieldToEdit.type,
      })
      this.closeCustomFieldModal()
      await this.fetchFields()
    },
  },
}
</script>
