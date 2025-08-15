<template>
  <main class="p-4">
    <h1 class="mb-4">Configuración</h1>
    <hr class="my-4" />
    <h4>Configuración del Sistema</h4>
    <p>A continuación podrá editar los parámetros esenciales del sistema.</p>
    <br />
    <div>
      <!-- Tabs -->
      <ul class="nav nav-tabs" id="configTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button
            class="nav-link active"
            id="roles-tab"
            data-bs-toggle="tab"
            data-bs-target="#roles"
            type="button"
            role="tab"
            aria-controls="roles"
            aria-selected="true"
          >
            Roles
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button
            class="nav-link"
            id="categories-tab"
            data-bs-toggle="tab"
            data-bs-target="#categories"
            type="button"
            role="tab"
            aria-controls="categories"
            aria-selected="false"
          >
            Categorías
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button
            class="nav-link"
            id="terminals-tab"
            data-bs-toggle="tab"
            data-bs-target="#terminals"
            type="button"
            role="tab"
            aria-controls="terminals"
            aria-selected="false"
          >
            Terminals
          </button>
        </li>
      </ul>

      <!-- Tab Content -->
      <div class="tab-content" id="configTabsContent">
        <!-- Roles -->
        <div
          class="tab-pane fade show active"
          id="roles"
          role="tabpanel"
          aria-labelledby="roles-tab"
        >
          <div class="card shadow-sm h-100 mt-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0 me-auto">Roles</h5>
                <button class="btn btn-success btn-sm me-2" @click="openAddRoleModal">
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

        <!-- Categories -->
        <div class="tab-pane fade" id="categories" role="tabpanel" aria-labelledby="categories-tab">
          <div class="card shadow-sm h-100 mt-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Categorías</h5>
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

        <!-- Terminals -->
        <div class="tab-pane fade" id="terminals" role="tabpanel" aria-labelledby="terminals-tab">
          <div class="card shadow-sm h-100 mt-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Terminals</h5>
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
      </div>
      <!-- Dynamic System Settings -->
      <div class="card mt-4">
        <div class="card-body">
          <h5 class="card-title mb-4">
            <i class="fa-solid fa-cogs me-2"></i>Configuraciones del Sistema
          </h5>

          <!-- Settings Tabs -->
          <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button
                class="nav-link active"
                id="text-tab"
                data-bs-toggle="tab"
                data-bs-target="#text"
                type="button"
                role="tab"
                aria-controls="text"
                aria-selected="true"
              >
                Texto
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                id="number-tab"
                data-bs-toggle="tab"
                data-bs-target="#number"
                type="button"
                role="tab"
                aria-controls="number"
                aria-selected="false"
              >
                Números
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                id="boolean-tab"
                data-bs-toggle="tab"
                data-bs-target="#boolean"
                type="button"
                role="tab"
                aria-controls="boolean"
                aria-selected="false"
              >
                Opciones
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                id="json-tab"
                data-bs-toggle="tab"
                data-bs-target="#json"
                type="button"
                role="tab"
                aria-controls="json"
                aria-selected="false"
              >
                JSON
              </button>
            </li>
          </ul>

          <div class="px-2">
            <!-- Tab Content -->
            <div class="tab-content mt-3" id="settingsTabsContent">
              <!-- TEXT SETTINGS -->
              <div
                class="tab-pane fade show active"
                id="text"
                role="tabpanel"
                aria-labelledby="text-tab"
              >
                <div class="row g-3">
                  <div
                    v-for="s in settings.filter((s) => s.type === 'string')"
                    :key="s.key"
                    class="col-12 col-md-6 col-lg-4"
                  >
                    <label class="form-label">
                      {{ s.key }}
                      <i
                        class="fa-solid fa-circle-info ms-1 text-muted"
                        v-if="s.description"
                        data-bs-toggle="tooltip"
                        :title="s.description"
                      ></i>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="s.value"
                      placeholder="Ingrese valor"
                    />
                  </div>
                </div>
              </div>

              <!-- NUMBER SETTINGS -->
              <div class="tab-pane fade" id="number" role="tabpanel" aria-labelledby="number-tab">
                <div class="row g-3">
                  <div
                    v-for="s in settings.filter((s) => s.type === 'number')"
                    :key="s.key"
                    class="col-12 col-md-6 col-lg-4"
                  >
                    <label class="form-label">
                      {{ s.key }}
                      <i
                        class="fa-solid fa-circle-info ms-1 text-muted"
                        v-if="s.description"
                        data-bs-toggle="tooltip"
                        :title="s.description"
                      ></i>
                    </label>
                    <input
                      type="number"
                      class="form-control"
                      v-model.number="s.value"
                      placeholder="Ingrese número"
                    />
                  </div>
                </div>
              </div>

              <!-- BOOLEAN SETTINGS -->
              <div class="tab-pane fade" id="boolean" role="tabpanel" aria-labelledby="boolean-tab">
                <div class="row g-3">
                  <div
                    v-for="s in settings.filter((s) => s.type === 'boolean')"
                    :key="s.key"
                    class="col-12 col-md-6 col-lg-4"
                  >
                    <div class="form-check form-switch mt-2">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        v-model="s.value"
                        :true-value="true"
                        :false-value="false"
                        :id="'chk_' + s.key"
                      />
                      <label class="form-check-label" :for="'chk_' + s.key">
                        {{ s.key }}
                        <i
                          class="fa-solid fa-circle-info ms-1 text-muted"
                          v-if="s.description"
                          data-bs-toggle="tooltip"
                          :title="s.description"
                        ></i>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- JSON SETTINGS -->
              <div class="tab-pane fade" id="json" role="tabpanel" aria-labelledby="json-tab">
                <div class="row g-3">
                  <div
                    v-for="s in settings.filter((s) => s.type === 'json')"
                    :key="s.key"
                    class="col-12"
                  >
                    <label class="form-label">
                      {{ s.key }}
                      <i
                        class="fa-solid fa-circle-info ms-1 text-muted"
                        v-if="s.description"
                        data-bs-toggle="tooltip"
                        :title="s.description"
                      ></i>
                    </label>
                    <textarea
                      class="form-control"
                      rows="4"
                      v-model="s.jsonValue"
                      placeholder='{"key": "value"}'
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary" @click="saveSettings">
              <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
            </button>
          </div>
        </div>
      </div>
      <!-- Alert -->
      <AlertC v-if="alert.message" :message="alert.message" :color="alert.color" />
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
                  :class="{ 'is-invalid': roleToEdit.name.length > 10 }"
                  type="text"
                  placeholder="Ingrese el nombre del rol"
                  maxlength="10"
                  required
                />
                <div class="invalid-feedback">
                  El nombre del rol es obligatorio y debe tener como máximo 10 caracteres.
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
                  <i class="fa-solid fa-floppy-disk"></i>
                  <span class="ms-2">Guardar</span>
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
                  :class="{ 'is-invalid': terminalToEdit.terminal_ext.length > 20 }"
                  type="text"
                  placeholder="Ingrese la extensión de la terminal"
                  maxlength="20"
                  required
                />
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeTerminalModal">
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
import AlertC from '@/components/AlertC.vue'
import { makeQuery } from '@/services/api.js'
import { Modal, Tooltip } from 'bootstrap'

export default {
  components: { RoleC, CategoriesC, TerminalsC, AlertC },
  data() {
    return {
      roles: [],
      categories: [],
      terminals: [],
      settings: [],
      alert: { message: '', color: 'danger' },
      deleteTarget: { type: '', id: null, callback: null },
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
    await this.fetchSettings()
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

    // --- CONFIRM DELETE ---
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

    async fetchSettings() {
      try {
        const res = await makeQuery('/settings', 'GET')
        this.settings = (res.data || []).map((s) => {
          let value = s.value
          if (s.type === 'boolean') {
            value = value === 1 || value === true || value === '1'
          }
          return {
            ...s,
            value,
            jsonValue: s.type === 'json' ? JSON.stringify(s.value, null, 2) : undefined,
          }
        })
        this.$nextTick(() => {
          // Inicializar tooltips después de renderizar
          const tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]'),
          )
          tooltipTriggerList.map((tooltipTriggerEl) => new Tooltip(tooltipTriggerEl))
        })
      } catch (e) {
        console.error(e)
      }
    },

    async saveSettings() {
      try {
        for (const s of this.settings) {
          let valueToSend
          if (s.type === 'json') {
            try {
              valueToSend = JSON.parse(s.jsonValue)
            } catch {
              this.alert = { message: `Error en el JSON de ${s.key}`, color: 'danger' }
              return
            }
          } else if (s.type === 'boolean') {
            valueToSend = s.value ? 1 : 0
          } else {
            valueToSend = s.value
          }

          await makeQuery(`/settings/${s.key}`, 'PUT', { value: valueToSend })
        }
        await this.fetchSettings()
        this.alert = { message: 'Configuraciones guardadas correctamente', color: 'success' }
      } catch (e) {
        console.error(e)
        this.alert = { message: 'Error al guardar configuraciones', color: 'danger' }
      }
    },
  },
}
</script>
