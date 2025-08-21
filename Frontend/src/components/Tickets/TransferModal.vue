<template>
  <div class="modal fade" ref="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" :class="mode === 'queue' ? '' : 'modal-md'">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Transferencia de Ticket</h5>
          <button type="button" class="btn-close" @click="closeModal" aria-label="Cerrar"></button>
        </div>

        <ul class="nav nav-tabs px-3 pt-2" role="tablist">
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              :class="{ active: mode === 'queue' }"
              @click="setMode('queue')"
              type="button"
            >
              Enviar a la cola
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              :class="{ active: mode === 'user' }"
              @click="setMode('user')"
              type="button"
            >
              Asignar a usuario
            </button>
          </li>
        </ul>

        <div class="modal-body">
          <div v-if="mode === 'queue'">
            <div class="container">
              <p>Estás a punto de enviar este ticket a la cola. ¿Estás seguro?</p>
            </div>
          </div>

          <div v-else-if="mode === 'user'">
            <!-- Buscador (idéntico a vista principal) -->
            <form @submit.prevent="onSearch" class="input-group mb-3">
              <input
                type="text"
                class="form-control"
                placeholder="Buscar usuario..."
                v-model="searchInput"
                aria-label="Buscar usuario"
              />
              <button
                v-if="searchInput"
                type="button"
                class="btn btn-outline-primary"
                @click="clearSearch"
                aria-label="Limpiar búsqueda"
              >
                <i class="fas fa-times"></i>
              </button>
              <button class="btn btn-outline-primary" type="submit">
                <i class="fas fa-search"></i>
              </button>
            </form>

            <!-- Lista de usuarios -->
            <UserListC :users="users" mode="seleccion-unica" @selection-changed="onUserSelected" />

            <!-- Loader -->
            <div v-if="loading" class="text-center my-2">
              <div class="spinner-border text-primary" role="status"></div>
            </div>

            <!-- Sin resultados -->
            <div v-if="!loading && users.length === 0" class="text-center my-2 text-muted">
              No se encontraron usuarios.
            </div>

            <!-- Errores -->
            <div v-if="errorMessages" class="alert alert-danger" role="alert">
              {{ errorMessages }}
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
                <li
                  class="page-item"
                  :class="{ disabled: pagination.page >= pagination.total_pages }"
                >
                  <button class="page-link" @click="changePage(pagination.page + 1)">
                    <i class="fas fa-chevron-right"></i>
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
          <button
            type="button"
            class="btn btn-primary"
            :disabled="mode === 'user' && !userID"
            @click="confirmTransfer"
          >
            Confirmar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap'
import UserListC from '../UsersComponets/UserListC.vue'
import { makeQuery } from '@/services/api'
import _ from 'lodash'

export default {
  name: 'TransferModal',
  components: {
    UserListC,
  },
  props: {
    ticketId: Number,
  },
  data() {
    return {
      mode: 'queue',
      userID: null,
      users: [],
      searchInput: this.$route.query.search || '',
      loading: false,
      errorMessages: null,
      bsModal: null,
      pagination: {
        page: parseInt(this.$route.query.page) || 1,
        limit: 5,
        total: 0,
        total_pages: 0,
      },
    }
  },
  watch: {
    '$route.query': {
      handler() {
        if (this.mode === 'user') this.fetchUsers()
      },
      immediate: true,
    },
    searchInput: _.debounce(function (val) {
      if (this.mode === 'user' && val !== (this.$route.query.search || '')) {
        this.$router.replace({
          path: this.$route.path,
          query: {
            ...this.$route.query,
            page: 1,
            search: val.trim() || undefined,
          },
        })
      }
    }, 500),
    mode(newMode) {
      if (newMode === 'user') {
        this.userID = null
        this.$router.replace({
          path: this.$route.path,
          query: {
            page: 1,
            search: this.searchInput || undefined,
          },
        })
      } else {
        this.resetState()
      }
    },
  },
  methods: {
    async fetchUsers() {
      this.loading = true
      this.errorMessages = null
      this.userID = null

      const page = parseInt(this.$route.query.page) || 1
      const search = this.$route.query.search || ''

      try {
        const { data } = await makeQuery(
          `/users?limit=${this.pagination.limit}&page=${page}` +
            (search ? `&search=${encodeURIComponent(search)}` : ''),
          'GET',
        )

        this.users = data.users || []
        this.pagination.total = data.pagination.total || 0
        this.pagination.total_pages = data.pagination.total_pages || 0
        this.pagination.page = page
      } catch (error) {
        this.errorMessages = error.response?.data?.message || 'Error al obtener usuarios'
        this.users = []
      } finally {
        this.loading = false
      }
    },

    onSearch() {
      this.$router.replace({
        path: this.$route.path,
        query: {
          ...this.$route.query,
          page: 1,
          search: this.searchInput.trim() || undefined,
        },
      })
    },

    clearSearch() {
      this.searchInput = ''
      this.onSearch()
    },

    openModal() {
      this.bsModal = new Modal(this.$refs.modal)
      this.bsModal.show()
      this.resetState()
    },

    closeModal() {
      this.bsModal?.hide()
    },

    setMode(newMode) {
      this.mode = newMode
    },

    confirmTransfer() {
      this.$emit('confirm', {
        ticketId: this.ticketId,
        mode: this.mode,
        user: this.userID,
      })
      this.closeModal()
    },

    resetState() {
      this.mode = 'queue'
      this.userID = null
      this.users = []
      this.errorMessages = null
      this.loading = false
      this.pagination.page = 1
      this.searchInput = ''
      this.$router.replace({
        path: this.$route.path,
        query: {
          page: undefined,
          search: undefined,
        },
      })
    },

    onUserSelected(selectedId) {
      this.userID = selectedId || null
    },

    changePage(newPage) {
      if (newPage < 1 || newPage > this.pagination.total_pages) return
      this.$router.replace({
        path: this.$route.path,
        query: {
          ...this.$route.query,
          page: newPage,
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
