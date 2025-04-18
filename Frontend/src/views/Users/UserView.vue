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
        </ul>

        <div class="d-flex justify-content-end">
          <button class="btn btn-primary me-2" @click="saveUser">
            <i class="fas fa-floppy-disk"></i>
            <span class="ms-2">Guardar</span>
          </button>
          <button class="btn btn-danger" @click="openDeleteModal">
            <i class="fas fa-trash"></i>
            <span class="ms-2">Eliminar</span>
          </button>
        </div>
      </section>
    </div>
    <!-- Preloader -->
    <div v-else class="text-center my-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-3">Cargando Usuario</p>
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

export default {
  name: 'UserView',
  components: {
    AlertC,
  },

  data() {
    return {
      user: null,
      userp: '',
      roles: [],
      EditTarget: null,
      deleteModalInstance: null,
      alertMessage: null,
      alertClass: null,
    }
  },
  created() {
    this.fetchUser()
    this.fetchRoles()
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

    openDeleteModal() {
      this.deleteModalInstance = new Modal(this.$refs.deleteModal)
      this.deleteModalInstance.show()
    },

    closeDeleteModal() {
      if (this.deleteModalInstance) this.deleteModalInstance.hide()
    },

    async saveUser() {
      try {
        if (this.userp) {
          this.user.password = this.userp
        }
        await makeQuery(`/users/${this.user.id}`, 'PUT', this.user)
        this.alertMessage = 'User saved successfully'
        this.alertClass = 'success'
        this.fetchUser()
      } catch (error) {
        console.error('Error saving user:', error)
        this.alertMessage = error.response.data.message
        this.alertClass = 'danger'
        this.fetchUser()
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
        this.closeDeleteModal()
        this.alertMessage = error.response.data.message
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
