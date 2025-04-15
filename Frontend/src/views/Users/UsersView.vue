<template>
  <main class="container my-4">
    <h2>Usuarios</h2>

    <div v-if="users.length > 0">
      <UserListC
        :users="users"
        @user-activated="handleUserActivated"
        @user-deactivated="handleUserDeactivated"
      />

      <!-- Paginación -->
      <nav class="d-flex justify-content-center mt-4">
        <ul class="pagination">
          <!-- Anterior -->
          <li class="page-item" :class="{ disabled: pagination.page <= 1 }">
            <button class="page-link" @click="changePage(pagination.page - 1)">
              <i class="fas fa-chevron-left"></i>
            </button>
          </li>

          <!-- Números de página -->
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

          <!-- Siguiente -->
          <li class="page-item" :class="{ disabled: pagination.page >= pagination.total_pages }">
            <button class="page-link" @click="changePage(pagination.page + 1)">
              <i class="fas fa-chevron-right"></i>
            </button>
          </li>
        </ul>
      </nav>
    </div>

    <div v-else class="text-center my-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-3">Cargando Usuarios...</p>
    </div>
  </main>
</template>

<script>
import UserListC from '@/components/UsersComponets/UserListC.vue'
import { makeQuery } from '@/services/api'

export default {
  name: 'UsersView',
  components: {
    UserListC,
  },
  data() {
    return {
      users: [],
      pagination: {
        page: 1,
        limit: 9,
        total: 0,
        total_pages: 0,
      },
    }
  },
  created() {
    this.fetchUsers()
  },
  watch: {
    '$route.query': {
      handler() {
        this.fetchUsers()
      },
      immediate: false,
    },
  },
  methods: {
    async fetchUsers() {
      this.pagination.page = parseInt(this.$route.query.page) || 1
      try {
        const response = await makeQuery(
          `users?limit=${this.pagination.limit}&page=${this.pagination.page}`,
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

          await this.$router.replace({
            path: this.$route.path,
            query: cleanQuery,
          })
        }
      }
    },

    async handleUserActivated(userId, done) {
      try {
        await makeQuery(`users/${userId}/active`, 'PUT')
        done(true)
      } catch (error) {
        console.error('Error activating user:', error)
        done(false)
      }
    },

    async handleUserDeactivated(userId, done) {
      try {
        await makeQuery(`users/${userId}/inactive`, 'DELETE')
        done(true)
      } catch (error) {
        console.error('Error deactivating user:', error)
        done(false)
      }
    },

    changePage(newPage) {
      if (newPage < 1 || newPage > this.pagination.total_pages) return

      this.$router.push({
        path: this.$route.path,
        query: {
          ...this.$route.query,
          page: newPage,
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
