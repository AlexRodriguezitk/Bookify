<template>
  <main class="container mt-3">
    <!-- Alerta flotante -->
    <AlertC
      v-if="errorMessages"
      :message="errorMessages"
      :color="alertClass ? alertClass : 'danger'"
    />

    <div class="d-flex align-items-center mb-3">
      <h1 class="mb-0 me-2 me-md-auto">Usuarios</h1>
      <button class="btn btn-success btn-sm" @click="openAddUserModal">
        <i class="fas fa-plus"></i>
        <span class="d-none d-sm-inline ms-2">Añadir Usuario</span>
      </button>
    </div>
    <!-- Buscador -->
    <form @submit.prevent="onSearch" class="input-group mb-2">
      <input
        v-model="searchInput"
        type="text"
        class="form-control"
        placeholder="Buscar usuario..."
        aria-label="Buscar usuario"
      />

      <!-- Botón limpiar con Bootstrap -->
      <button
        v-if="searchInput"
        type="button"
        class="btn btn-outline-primary"
        @click="((searchInput = ''), onSearch())"
        aria-label="Limpiar búsqueda"
      >
        <i class="fas fa-times"></i>
      </button>

      <button class="btn btn-outline-primary" type="submit">
        <i class="fas fa-search"></i>
      </button>
    </form>

    <!-- Tabla de usuarios (vacía o no) -->
    <div v-if="users.length > 0 || search">
      <div class="user-grid-container p-2 border rounded">
        <UserListC
          :users="users"
          @user-activated="handleUserActivated"
          @user-deactivated="handleUserDeactivated"
        />
        <div v-if="users.length === 0" class="text-center my-3">
          <p>No se encontraron usuarios.</p>
        </div>
      </div>

      <div v-if="search">
        <p class="text-end mt-2">Resultados: {{ users.length ? pagination.total : '0' }}</p>
      </div>
      <!-- Paginación -->
      <nav v-if="pagination.total_pages > 1" class="d-flex justify-content-center mt-4">
        <ul class="pagination">
          <li class="page-item" :class="{ disabled: pagination.page <= 1 }">
            <button class="page-link" @click="changePage(pagination.page - 1)">
              <i class="fas fa-chevron-left"></i>
            </button>
          </li>

          <li
            v-for="page in visiblePages"
            :key="page"
            class="page-item"
            :class="{ active: page === pagination.page, disabled: page === '...' }"
          >
            <button class="page-link" @click="changePage(page)" :disabled="page === '...'">
              {{ page }}
            </button>
          </li>

          <li class="page-item" :class="{ disabled: pagination.page >= pagination.total_pages }">
            <button class="page-link" @click="changePage(pagination.page + 1)">
              <i class="fas fa-chevron-right"></i>
            </button>
          </li>
        </ul>
      </nav>
    </div>

    <!-- Spinner solo en carga inicial (sin búsqueda ni error) -->
    <div v-if="!search && !errorMessages && users.length === 0" class="text-center my-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-3">Cargando Usuarios...</p>
    </div>

    <!-- User Modal -->
    <div class="modal fade" ref="userModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Añadir usuario</h5>
            <button type="button" class="btn-close" @click="closeUserModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="addUser()">
              <div class="row">
                <div class="col">
                  <div class="mb-3">
                    <label for="userName" class="form-label">Nombre completo</label>
                    <input
                      id="userName"
                      v-model="newUser.name"
                      class="form-control"
                      type="text"
                      placeholder="Ingrese el nombre"
                      required
                      autocomplete="off"
                    />
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input
                      id="username"
                      v-model="newUser.username"
                      class="form-control"
                      type="text"
                      placeholder="Ingrese el nombre de usuario"
                      required
                      autocomplete="off"
                    />
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <!--Imput group-->
                <div class="input-group">
                  <input
                    id="password"
                    ref="password"
                    type="text"
                    placeholder="Ingrese la contraseña"
                    class="form-control"
                    :class="{
                      'is-valid':
                        newUser.password !== '' &&
                        newUser.password_confirmation !== '' &&
                        newUser.password === newUser.password_confirmation,
                      'is-invalid':
                        newUser.password !== '' &&
                        newUser.password_confirmation !== '' &&
                        newUser.password !== newUser.password_confirmation,
                    }"
                    v-model="newUser.password"
                    autocomplete="new-password"
                    required
                  />
                  <!-- Generate Random Password -->
                  <button class="btn btn-primary" @click="Generate" type="button">
                    <i class="fas fa-key"></i>
                  </button>
                </div>
              </div>

              <div class="mb-3">
                <label for="c-password" class="form-label">Confirmar contraseña</label>
                <input
                  id="c-password"
                  ref="cPassword"
                  v-model="newUser.password_confirmation"
                  class="form-control"
                  :class="{
                    'is-valid':
                      newUser.password !== '' &&
                      newUser.password_confirmation !== '' &&
                      newUser.password === newUser.password_confirmation,
                    'is-invalid':
                      newUser.password !== '' &&
                      newUser.password_confirmation !== '' &&
                      newUser.password !== newUser.password_confirmation,
                  }"
                  type="text"
                  placeholder="Confirme la contraseña"
                  autocomplete="new-password"
                  required
                />
              </div>

              <div class="mb-3">
                <label for="phone" class="form-label">Teléfono</label>
                <input
                  id="phone"
                  v-model="newUser.phone"
                  class="form-control"
                  type="tel"
                  placeholder="Ingrese el número de teléfono"
                />
              </div>
              <label for="rol" class="form-label">Rol</label>
              <div class="row g-3 mb-3">
                <!-- Selector de rol -->
                <div class="col-12 col-md-8 d-flex align-items-center">
                  <select id="rol" v-model="newUser.rol" class="form-select" required>
                    <option disabled value="">Seleccione un rol</option>
                    <option v-for="rol in roles" :key="rol.id" :value="rol.id">
                      {{ rol.name }}
                    </option>
                  </select>
                </div>

                <!-- Checkbox de usuario activo -->
                <div class="col-12 col-md-4 d-flex align-items-center">
                  <div class="form-check form-switch">
                    <input
                      id="isActive"
                      v-model="newUser.is_active"
                      class="form-check-input"
                      type="checkbox"
                    />
                    <label class="form-check-label ms-2" for="isActive"> Activo </label>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeUserModal">
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

<script>
import UserListC from '@/components/UsersComponets/UserListC.vue'
import { makeQuery } from '@/services/api'
import _ from 'lodash' // ⬅️ importante
import AlertC from '@/components/AlertC.vue'
import { Modal } from 'bootstrap'

export default {
  name: 'UsersView',
  components: {
    UserListC,
    AlertC,
  },
  data() {
    return {
      users: [],
      roles: [],
      modalUserInstance: null,
      pagination: {
        page: 1,
        limit: 15,
        total: 0,
        total_pages: 0,
      },
      newUser: {
        name: '',
        username: '',
        password: '',
        password_confirmation: '',
        phone: '',
        rol: '',
        is_active: true,
      },
      search: null,
      errorMessages: null,
      alertClass: null,
      searchInput: this.$route.query.search || '',
    }
  },
  created() {
    this.fetchUsers()
    this.fetchRoles()
  },
  watch: {
    // Se ejecuta cuando cambia la URL (page, search, etc.)
    '$route.query': {
      handler() {
        this.fetchUsers()
      },
      immediate: false,
    },

    // Alerta que desaparece sola
    errorMessages(val) {
      if (val) {
        setTimeout(() => {
          this.errorMessages = null
        }, 4000)
      }
    },

    // 🧠 Buscador dinámico con debounce
    searchInput: _.debounce(function (val) {
      // Evita actualizar si es igual a lo actual
      if (val !== (this.$route.query.search || '')) {
        this.$router.push({
          path: this.$route.path,
          query: {
            ...this.$route.query,
            page: 1,
            search: val.trim() || undefined,
          },
        })
      }
    }, 500),
  },

  methods: {
    async fetchUsers() {
      this.pagination.page = parseInt(this.$route.query.page) || 1
      this.search = this.$route.query.search || null
      try {
        const response = await makeQuery(
          `/users?limit=${this.pagination.limit}&page=${this.pagination.page}` +
            (this.search ? `&search=${this.search}` : ''),
          'GET',
        )
        this.users = response.data.users
        this.pagination.total = response.data.pagination.total
        this.pagination.total_pages = response.data.pagination.total_pages

        console.log(this.pagination)
      } catch (error) {
        console.error('Error fetching Users:', error)

        if (error.response && error.response.status === 404) {
          const cleanQuery = { ...this.$route.query }
          delete cleanQuery.page

          this.users = []
          this.errorMessages = error.response.data.message

          await this.$router.replace({
            path: this.$route.path,
            query: cleanQuery,
          })
        }
      }
    },

    async fetchRoles() {
      try {
        const response = await makeQuery('/roles', 'GET')
        this.roles = response.data || []
      } catch (error) {
        console.error('Error fetching roles:', error)
      }
    },

    openAddUserModal() {
      this.newUser = {
        name: '',
        username: '',
        password: '',
        password_confirmation: '',
        phone: '',
        rol: '',
        is_active: true,
      }
      this.modalUserInstance = new Modal(this.$refs.userModal)
      this.modalUserInstance.show()
    },

    closeUserModal() {
      this.modalUserInstance.hide()
    },

    async addUser() {
      if (this.newUser.password !== this.newUser.password_confirmation) {
        this.errorMessages = 'Las contraseñas no coinciden'
        //Add boostrapp style wrong to passowrd input
        document.getElementById('password').classList.add('is-invalid')
        document.getElementById('c-password').classList.add('is-invalid')
        return
      }
      this.closeUserModal()
      //unset password_confirmation
      delete this.newUser.password_confirmation

      try {
        await makeQuery('/users', 'POST', this.newUser)
        await this.fetchUsers()
      } catch (error) {
        console.error('Error adding user:', error)
        this.errorMessages = error.response.data.message
      }
    },

    async handleUserActivated(userId, done) {
      try {
        await makeQuery(`/users/${userId}/active`, 'PUT')
        done(true)
      } catch (error) {
        console.error('Error activating user:', error)
        done(false)
      }
    },

    async handleUserDeactivated(userId, done) {
      try {
        await makeQuery(`/users/${userId}/inactive`, 'DELETE')
        done(true)
      } catch (error) {
        console.error('Error deactivating user:', error)
        this.errorMessages = error.response.data.message
        done(false)
      }
    },

    changePage(newPage) {
      if (newPage < 1 || newPage > this.pagination.total_pages) return

      this.$router.replace({
        path: this.$route.path,
        query: {
          ...this.$route.query,
          page: newPage,
        },
      })
    },

    onSearch() {
      this.$router.push({
        path: this.$route.path,
        query: {
          ...this.$route.query,
          page: 1,
          search: this.searchInput.trim() || undefined,
        },
      })
    },

    async Generate() {
      try {
        const response = await makeQuery(`/auth/generate`, 'GET')
        this.newUser.password = response.data[0]
        this.newUser.password_confirmation = response.data[0]
        this.errorMessages = 'Password generated successfully'
        this.alertClass = 'success'
      } catch (error) {
        console.error('Error deleting user:', error)
        this.errorMessages = error.response.data.message
        this.alertClass = 'danger'
      }
    },
  },

  computed: {
    visiblePages() {
      const total = this.pagination.total_pages
      const current = this.pagination.page
      const pages = []

      if (total <= 5) {
        for (let i = 1; i <= total; i++) pages.push(i)
      } else {
        pages.push(1)
        if (current > 3) pages.push('...')
        for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
          pages.push(i)
        }
        if (current < total - 2) pages.push('...')
        pages.push(total)
      }

      return pages
    },
  },
}
</script>

<style scoped>
.user-grid-container {
  max-height: 390px;
  overflow-y: auto;
  overflow-x: hidden;
}
</style>
