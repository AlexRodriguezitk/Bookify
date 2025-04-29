<template>
  <main class="container my-4">
    <AlertC v-if="alertMessage" :message="alertMessage" :color="alertClass" />
    <div v-if="user" class="row g-4">
      <section class="col-12 col-md-6">
        <h2 class="h5 mb-3 text-center text-md-start">Detalles del Usuario</h2>
        <ul class="list-group mb-3">
          <li class="list-group-item">ID: {{ user.id }}</li>

          <!-- NOMBRE -->
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <label class="me-2">
              Nombre:
              <span
                class="fw-bold user-name"
                v-if="EditTarget !== 'name'"
                @click="EditAtribute('name')"
              >
                {{ user.name }}
              </span>
            </label>
            <button v-if="EditTarget !== 'name'" class="btn btn-sm" @click="EditAtribute('name')">
              <i class="fas fa-edit"></i>
            </button>

            <input
              v-if="EditTarget === 'name'"
              ref="name"
              type="text"
              class="form-control"
              v-model="user.name"
              @focusout="EditTarget = null"
              required
            />
          </li>

          <!-- ROL -->
          <li class="list-group-item d-flex align-items-center">
            <span class="me-2">Rol:</span>
            <select id="rol" v-model="user.rol" class="form-select rol-select" required>
              <option disabled value="">Seleccione un rol</option>
              <option v-for="rol in roles" :key="rol.id" :value="rol.id">
                {{ rol.name }}
              </option>
            </select>
          </li>

          <!-- USERNAME -->
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <label class="me-2">
              Username:
              <span
                class="fw-bold user-name"
                v-if="EditTarget !== 'username'"
                @click="EditAtribute('username')"
              >
                {{ user.username }}
              </span>
            </label>
            <button
              v-if="EditTarget !== 'username'"
              class="btn btn-sm"
              @click="EditAtribute('username')"
            >
              <i class="fas fa-edit"></i>
            </button>

            <input
              v-if="EditTarget === 'username'"
              ref="username"
              type="text"
              class="form-control"
              v-model="user.username"
              @focusout="EditTarget = null"
              required
            />
          </li>
          <!-- PASSWORD-->
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <label class="me-2"> Contraseña: </label>

            <!--Imput group-->
            <div class="input-group">
              <input ref="password" type="text" class="form-control" v-model="userp" required />
              <!-- Generate Random Password -->
              <button class="btn btn-primary" @click="Generate" type="button">
                <i class="fas fa-key"></i>
              </button>
            </div>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <label class="me-2">
              Numero de Teléfono:
              <span
                class="fw-bold user-name"
                v-if="EditTarget !== 'phone'"
                @click="EditAtribute('phone')"
              >
                {{ user.phone }}
              </span>
            </label>
            <button v-if="EditTarget !== 'phone'" class="btn btn-sm" @click="EditAtribute('phone')">
              <i class="fas fa-edit"></i>
            </button>

            <input
              v-if="EditTarget === 'phone'"
              ref="username"
              type="text"
              class="form-control"
              v-model="user.phone"
              @focusout="EditTarget = null"
              required
            />
          </li>
          <!-- Active -->
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <label class="me-2"> Activo: </label>
            <div class="form-check form-switch">
              <input
                class="form-check-input"
                type="checkbox"
                :checked="user.is_active"
                @change="ToggleActive()"
              />
            </div>
          </li>
        </ul>

        <div class="d-flex justify-content-end">
          <button
            class="btn btn-primary me-2 d-flex align-items-center"
            @click="saveUser"
            :disabled="isSaving"
          >
            <i v-if="isSaving" class="fas fa-spinner fa-spin"></i>
            <i v-else-if="saveSuccess" class="fas fa-check"></i>
            <i v-else class="fas fa-floppy-disk"></i>
            <span class="ms-2">
              {{ isSaving ? 'Guardando...' : saveSuccess ? 'Guardado' : 'Guardar' }}
            </span>
          </button>

          <button class="btn btn-danger" @click="openDeleteModal">
            <i class="fas fa-trash"></i>
            <span class="ms-2">Eliminar</span>
          </button>
        </div>
      </section>
      <!-- Columna: Permisos -->
      <section class="col-12 col-md-6">
        <h2 class="h5 mb-3 text-center text-md-start">Asignación de Terminales</h2>
        <div class="fields-grid-container p-2 border rounded">
          <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
            <h5 class="card-title">Terminales</h5>
            <button class="btn btn-success btn-sm me-2" @click="openAssignTerminalModal">
              <i class="fas fa-plus"></i>
              <span class="d-none d-sm-inline ms-2">Asignar</span>
            </button>
          </div>
          <TerminalListC :terminals="userTerminals" @unassign-terminal="unassignTerminal" />
          <div v-if="userTerminals.length === 0">
            <p class="text-center">No se han asignado terminales a este usuario.</p>
          </div>
        </div>
      </section>
    </div>
    <!-- Preloader -->
    <div v-else class="text-center my-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-3">Cargando Usuario</p>
    </div>

    <!--Modal de Asignación de Terminal-->
    <div class="modal fade" ref="assignTerminalModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Asignación de Terminal</h5>
            <button type="button" class="btn-close" @click="closeAssignTerminalModal"></button>
          </div>
          <div class="modal-body">
            <select name="terminal" id="terminal" v-model="selectedTerminal" class="form-control">
              <option value="" disabled>Seleccione una Terminal</option>
              <option v-for="terminal in terminals" :key="terminal.id" :value="terminal.id">
                {{ terminal.terminal_ext }}
              </option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeAssignTerminalModal">
              <i class="fa-solid fa-xmark"></i>
              <span class="ms-2">Cancelar</span>
            </button>
            <button
              type="button"
              :disabled="selectedTerminal === ''"
              class="btn btn-primary"
              @click="assignTerminal(selectedTerminal)"
            >
              <i class="fa-solid fa-check"></i> <span class="ms-2">Asignar</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal de Confirmación de Eliminación -->
    <div class="modal fade" ref="deleteModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">¿Eliminar rol?</h5>
            <button type="button" class="btn-close" @click="closeDeleteModal"></button>
          </div>
          <div class="modal-body">
            <p>¿Estás seguro de que deseas eliminar este rol? Esta acción no se puede deshacer.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeDeleteModal">
              <i class="fa-solid fa-xmark"></i>
              <span class="ms-2">Cancelar</span>
            </button>
            <button type="button" class="btn btn-danger" @click="deleteUser">
              <i class="fa-solid fa-trash"></i>
              <span class="ms-2">Eliminar</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import { nextTick } from 'vue'
import { makeQuery } from '../../services/api'
import { Modal } from 'bootstrap'
import AlertC from '../../components/AlertC.vue'
import TerminalListC from '@/components/UsersComponets/TerminalListC.vue'

export default {
  name: 'UserView',
  components: {
    AlertC,
    TerminalListC,
  },

  data() {
    return {
      user: null,
      userp: '',
      roles: [],
      terminals: [],
      userTerminals: [],
      EditTarget: null,
      deleteModalInstance: null,
      alertMessage: null,
      alertClass: null,
      isSaving: false,
      saveSuccess: false,
      selectedTerminal: '',
      assignTerminalModalInstance: null,
    }
  },
  created() {
    this.fetchUser()
    this.fetchRoles()
    this.fetchTerminals()
    this.fetchUserTerminals()
  },
  watch: {
    alertMessage(val) {
      if (val) {
        setTimeout(() => {
          this.alertMessage = null
          this.alertClass = null
        }, 4000)
      }
    },
  },
  methods: {
    async fetchUser() {
      try {
        const response = await makeQuery(`/users/${this.$route.params.id}`, 'GET')
        this.user = response.data[0]
      } catch (error) {
        console.error('Error fetching user:', error)
        this.$router.push('/users')
      }
    },

    async fetchRoles() {
      try {
        const response = await makeQuery('/roles', 'GET')
        this.roles = response.data
      } catch (error) {
        console.error('Error fetching roles:', error)
        this.alertMessage = error.response.data.message
        this.alertClass = 'danger'
      }
    },

    async fetchTerminals() {
      try {
        const response = await makeQuery('/terminals', 'GET')
        this.terminals = response.data
      } catch (error) {
        console.error('Error fetching terminals:', error)
        this.alertMessage = error.response.data.message
        this.alertClass = 'danger'
      }
    },

    async fetchUserTerminals() {
      try {
        const response = await makeQuery(`/terminals/Assignments/${this.$route.params.id}`, 'GET')
        this.userTerminals = response.data
      } catch (error) {
        console.error('Error fetching user terminals:', error)
        if (error.response.status !== 404) {
          this.alertMessage = error.response.data.message
          this.alertClass = 'danger'
        }
        this.userTerminals = []
      }
    },

    EditAtribute(attribute) {
      this.EditTarget = attribute
      nextTick(() => {
        const input = this.$refs[attribute]
        if (input) {
          input.focus()
          input.select?.()
        }
      })
    },

    async ToggleActive() {
      const prev = this.user.is_active
      this.user.is_active = !prev // Cambio visual inmediato

      const handleToggleResult = async (success) => {
        if (!success) {
          this.user.is_active = prev // Revertir el cambio si falla
        }
      }

      try {
        if (prev) {
          await this.handleUserDeactivated(this.user.id, handleToggleResult)
        } else {
          await this.handleUserActivated(this.user.id, handleToggleResult)
        }
      } catch (error) {
        console.error('Error toggling user active state:', error)
        const errorResponse = error.response.data
        this.alertMessage = errorResponse.message || errorResponse.error || error.message
        this.alertClass = 'danger'
        this.user.is_active = prev // Revertir el cambio en caso de error
      }
    },

    async handleUserActivated(userId, done) {
      try {
        await makeQuery(`/users/${userId}/active`, 'PUT')
        done(true)
      } catch (error) {
        console.error('Error activating user:', error)
        const errorResponse = error.response.data
        this.alertMessage = errorResponse.message || errorResponse.error || error.message
        this.alertClass = 'danger'
        done(false)
      }
    },

    async handleUserDeactivated(userId, done) {
      try {
        await makeQuery(`/users/${userId}/inactive`, 'DELETE')
        done(true)
      } catch (error) {
        console.error('Error deactivating user:', error)
        const errorResponse = error.response.data
        this.alertMessage = errorResponse.message || errorResponse.error || error.message
        this.alertClass = 'danger'
        done(false)
      }
    },

    async unassignTerminal(terminalId) {
      try {
        await makeQuery(`/terminals/${terminalId}/unassign/${this.$route.params.id}`, 'DELETE')
        this.fetchUserTerminals()
      } catch (error) {
        console.error('Error unassigning terminal:', error)
        this.alertMessage = error.response.data.message
        this.alertClass = 'danger'
      }
    },

    openDeleteModal() {
      this.deleteModalInstance = new Modal(this.$refs.deleteModal)
      this.deleteModalInstance.show()
    },

    closeDeleteModal() {
      if (this.deleteModalInstance) this.deleteModalInstance.hide()
    },

    async saveUser() {
      this.isSaving = true
      this.saveSuccess = false

      try {
        if (this.userp) {
          this.user.password = this.userp
        }

        await makeQuery(`/users/${this.user.id}`, 'PUT', this.user)

        this.alertMessage = 'Usuario guardado exitosamente'
        this.alertClass = 'success'
        this.saveSuccess = true

        // Indicador de éxito momentáneo en el botón
        setTimeout(() => {
          this.saveSuccess = false
        }, 3000)

        this.fetchUser()
      } catch (error) {
        console.error('Error saving user:', error)
        this.alertMessage = error.response?.data?.message || error.message
        this.alertClass = 'danger'
        this.fetchUser()
      } finally {
        this.isSaving = false
      }
    },

    async deleteUser() {
      try {
        await makeQuery(`/users/${this.user.id}`, 'DELETE')
        this.closeDeleteModal()
        this.alertMessage = 'User deleted successfully'
        this.alertClass = 'success'
        this.$router.push('/users')
      } catch (error) {
        console.error('Error deleting user:', error)
        this.closeDeleteModal()
        this.alertMessage = error.response.data.message
        this.alertClass = 'danger'
      }
    },

    async Generate() {
      try {
        const response = await makeQuery(`/auth/generate`, 'GET')
        this.userp = response.data[0]
        this.alertMessage = 'Password generated successfully'
        this.alertClass = 'success'
      } catch (error) {
        console.error('Error deleting user:', error)
        this.alertMessage = error.response.data.message
        this.alertClass = 'danger'
      }
    },

    openAssignTerminalModal() {
      this.assignTerminalModalInstance = new Modal(this.$refs.assignTerminalModal)
      this.assignTerminalModalInstance.show()
    },

    closeAssignTerminalModal() {
      if (this.assignTerminalModalInstance) {
        this.assignTerminalModalInstance.hide()
      }
      this.selectedTerminal = ''
    },

    async assignTerminal(terminalId) {
      if (!terminalId) {
        this.alertMessage = 'Debe seleccionar una terminal'
        this.alertClass = 'danger'
        return
      }

      try {
        await makeQuery(`/terminals/${terminalId}/assign/${this.user.id}`, 'PUT')
        this.alertMessage = 'Terminal asignada correctamente'
        this.alertClass = 'success'
        this.fetchUserTerminals()
        this.closeAssignTerminalModal()
      } catch (error) {
        console.error('Error asignando terminal:', error)
        this.closeAssignTerminalModal()
        this.alertMessage = error.response?.data?.message || error.message
        this.alertClass = 'danger'
      }
    },
  },
}
</script>

<style scoped>
.rol-select {
  max-width: 12em;
}

.user-name {
  cursor: pointer;
}
.user-name:hover {
  text-decoration: underline;
}
</style>
