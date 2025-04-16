<template>
  <main class="container mt-3">
    <!-- Alerta flotante -->
    <AlertC v-if="errorMessages" :message="errorMessages" color="danger" />

    <h2>Usuarios</h2>

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
  </main>
</template>

<script>
import UserListC from '@/components/UsersComponets/UserListC.vue'
import { makeQuery } from '@/services/api'
import _ from 'lodash' // ⬅️ importante
import AlertC from '@/components/AlertC.vue'

export default {
  name: 'UsersView',
  components: {
    UserListC,
    AlertC,
  },
  data() {
    return {
      users: [],
      pagination: {
        page: 1,
        limit: 15,
        total: 0,
        total_pages: 0,
      },
      search: null,
      errorMessages: null,
      searchInput: this.$route.query.search || '',
    }
  },
  created() {
    this.fetchUsers()
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
        this.pagination.total = response.data.total
        this.pagination.total_pages = response.data.total_pages
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
.floating-alert {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  min-width: 250px;
  max-width: 400px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
.user-grid-container {
  max-height: 390px;
  overflow-y: auto;
  overflow-x: hidden;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
