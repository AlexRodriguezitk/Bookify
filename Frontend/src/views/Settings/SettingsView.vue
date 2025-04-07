<template>
  <main class="p-4">
    <h1 class="mb-4">Configuración</h1>
    <hr class="my-4" />
    <h4>Configuración del Sistema</h4>
    <p>A continuación podra editar los parametros esenciales del sistema.</p>
    <br />
    <div class="row g-4">
      <!-- Roles Card -->
      <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title mb-0">Roles</h5>
              <!-- Roles -->
              <button class="btn btn-success btn-sm" @click="openAddRoleModal">
                <i class="fas fa-plus"></i>
                <span class="d-none d-sm-inline ms-2">Añadir Rol</span>
              </button>
            </div>
            <RoleC
              :roles="roles"
              @edit-role="openEditRoleModal"
              @delete-role="deleteRole"
              class="mb-3"
            />
          </div>
        </div>
      </div>

      <!-- Categories Card -->
      <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title mb-0">Categories</h5>
              <!-- Categories -->
              <button class="btn btn-success btn-sm" @click="openAddCategoryModal">
                <i class="fas fa-plus"></i>
                <span class="d-none d-sm-inline ms-2">Añadir Categoría</span>
              </button>
            </div>
            <CategoriesC
              :categories="categories"
              @edit-category="openEditCategoryModal"
              @delete-category="deleteCategory"
              class="mb-3"
            />
          </div>
        </div>
      </div>

      <!-- Terminals Card -->
      <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title mb-0">Terminals</h5>
              <!-- Terminals -->
              <button class="btn btn-success btn-sm" @click="openAddTerminalModal">
                <i class="fas fa-plus"></i>
                <span class="d-none d-sm-inline ms-2">Añadir Terminal</span>
              </button>
            </div>
            <TerminalsC
              :terminals="terminals"
              @edit-terminal="openEditTerminalModal"
              @delete-terminal="deleteTerminal"
              class="mb-3"
            />
          </div>
        </div>
      </div>
      <hr class="my-4" />
    </div>

    <!-- Role Modal -->
    <div class="modal fade" ref="roleModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ isEditingRole ? 'Editar rol' : 'Añadir rol' }}
            </h5>
            <button type="button" class="btn-close" @click="closeRoleModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="isEditingRole ? updateRole() : addRole()">
              <div class="mb-3">
                <label for="roleName" class="form-label">Nombre del rol</label>
                <input
                  id="roleName"
                  v-model="roleToEdit.name"
                  class="form-control"
                  :class="{
                    'is-invalid': roleToEdit.name.length > 10,
                  }"
                  type="text"
                  placeholder="Ingrese el nombre del rol"
                  maxlength="10"
                  required
                />
                <div class="invalid-feedback">
                  El nombre del rol es obligatorio y debe tener como máximo 6 caracteres.
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeRoleModal">
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

    <!-- Terminal Modal -->
    <div class="modal fade" ref="terminalModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ isEditingTerminal ? 'Editar terminal' : 'Añadir terminal' }}
            </h5>
            <button type="button" class="btn-close" @click="closeTerminalModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="isEditingTerminal ? updateTerminal() : addTerminal()">
              <div class="mb-3">
                <label for="terminalExt" class="form-label">Terminal Ext</label>
                <input
                  id="terminalExt"
                  v-model="terminalToEdit.terminal_ext"
                  class="form-control"
                  :class="{
                    'is-invalid': terminalToEdit.terminal_ext.length > 15,
                  }"
                  :disabled="isEditingTerminal"
                  type="text"
                  placeholder="Ingrese la extensión de la terminal"
                  maxlength="15"
                  required
                />
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeTerminalModal">
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
    <!-- Confirm Delete Modal -->
    <div class="modal fade" ref="confirmDeleteModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Confirmar Eliminación</h5>
            <button type="button" class="btn-close" @click="closeConfirmDeleteModal"></button>
          </div>
          <div class="modal-body">
            <p>
              ¿Estás seguro que deseas eliminar este
              <strong>{{ deleteTarget.type }}</strong
              >? Esta acción no se puede deshacer.
            </p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeConfirmDeleteModal">Cancelar</button>
            <button class="btn btn-danger" @click="confirmDelete">Eliminar</button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import RoleC from '@/components/SettingsComponents/RoleC.vue'
import CategoriesC from '@/components/SettingsComponents/CategoriesC.vue'
import TerminalsC from '@/components/SettingsComponents/TerminalsC.vue'
import { makeQuery } from '@/services/api.js'
import { Modal } from 'bootstrap'

export default {
  components: {
    RoleC,
    CategoriesC,
    TerminalsC,
  },
  data() {
    return {
      roles: [],
      categories: [],
      terminals: [],
      deleteTarget: {
        type: '',
        id: null,
        callback: null,
      },
      modalConfirmDeleteInstance: null,

      roleToEdit: { id: null, name: '' },
      categoryToEdit: { id: null, name: '' },
      terminalToEdit: { id: null, terminal_ext: '' },
      isEditingRole: false,
      isEditingCategory: false,
      isEditingTerminal: false,
      modalRoleInstance: null,
      modalCategoryInstance: null,
      modalTerminalInstance: null,
    }
  },
  async created() {
    await this.fetchRoles()
    await this.fetchCategories()
    await this.fetchTerminals()
  },
  methods: {
    // --- ROLES ---
    async fetchRoles() {
      try {
        const response = await makeQuery('/roles', 'GET')
        this.roles = response.data || []
      } catch (error) {
        console.error('Error fetching roles:', error)
      }
    },
    openEditRoleModal(role) {
      this.roleToEdit = { ...role }
      this.isEditingRole = true
      this.modalRoleInstance = new Modal(this.$refs.roleModal)
      this.modalRoleInstance.show()
    },
    openAddRoleModal() {
      this.roleToEdit = { id: null, name: '' }
      this.isEditingRole = false
      this.modalRoleInstance = new Modal(this.$refs.roleModal)
      this.modalRoleInstance.show()
    },
    closeRoleModal() {
      if (this.modalRoleInstance) this.modalRoleInstance.hide()
    },
    async updateRole() {
      await makeQuery(`/roles/${this.roleToEdit.id}`, 'PUT', { name: this.roleToEdit.name })
      this.closeRoleModal()
      await this.fetchRoles()
    },
    async addRole() {
      await makeQuery('/roles', 'POST', { name: this.roleToEdit.name })
      this.closeRoleModal()
      await this.fetchRoles()
    },
    deleteRole(roleId) {
      this.openConfirmDeleteModal('rol', roleId, async (id) => {
        await makeQuery(`/roles/${id}`, 'DELETE')
        await this.fetchRoles()
      })
    },

    // --- CATEGORIES ---
    async fetchCategories() {
      try {
        const response = await makeQuery('/categories', 'GET')
        this.categories = response.data || []
      } catch (error) {
        console.error('Error fetching categories:', error)
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
      await this.fetchCategories()
    },
    async addCategory() {
      await makeQuery('/categories', 'POST', { name: this.categoryToEdit.name })
      this.closeCategoryModal()
      await this.fetchCategories()
    },
    deleteCategory(categoryId) {
      this.openConfirmDeleteModal('categoría', categoryId, async (id) => {
        await makeQuery(`/categories/${id}`, 'DELETE')
        await this.fetchCategories()
      })
    },

    // --- TERMINALS ---
    async fetchTerminals() {
      try {
        const response = await makeQuery('/terminals', 'GET')
        this.terminals = response.data || []
      } catch (error) {
        console.error('Error fetching terminals:', error)
      }
    },
    openEditTerminalModal(terminal) {
      this.terminalToEdit = { ...terminal }
      this.isEditingTerminal = true
      this.modalTerminalInstance = new Modal(this.$refs.terminalModal)
      this.modalTerminalInstance.show()
    },
    openAddTerminalModal() {
      this.terminalToEdit = { id: null, terminal_ext: '' }
      this.isEditingTerminal = false
      this.modalTerminalInstance = new Modal(this.$refs.terminalModal)
      this.modalTerminalInstance.show()
    },
    closeTerminalModal() {
      if (this.modalTerminalInstance) this.modalTerminalInstance.hide()
    },
    async updateTerminal() {
      await makeQuery(`/terminals/${this.terminalToEdit.id}`, 'PUT', {
        terminal_ext: this.terminalToEdit.terminal_ext,
      })
      this.closeTerminalModal()
      await this.fetchTerminals()
    },
    async addTerminal() {
      await makeQuery('/terminals', 'POST', { terminal_ext: this.terminalToEdit.terminal_ext })
      this.closeTerminalModal()
      await this.fetchTerminals()
    },
    deleteTerminal(terminalId) {
      this.openConfirmDeleteModal('terminal', terminalId, async (id) => {
        await makeQuery(`/terminals/${id}`, 'DELETE')
        await this.fetchTerminals()
      })
    },
    openConfirmDeleteModal(type, id, callback) {
      this.deleteTarget = { type, id, callback }
      this.modalConfirmDeleteInstance = new Modal(this.$refs.confirmDeleteModal)
      this.modalConfirmDeleteInstance.show()
    },
    closeConfirmDeleteModal() {
      if (this.modalConfirmDeleteInstance) this.modalConfirmDeleteInstance.hide()
    },
    async confirmDelete() {
      if (this.deleteTarget.callback) {
        await this.deleteTarget.callback(this.deleteTarget.id)
      }
      this.closeConfirmDeleteModal()
    },
  },
}
</script>
