<template>
  <div class="app-container h-100">
    <Sidebar
      :profileImage="userProfileImage"
      :Username="Profile"
      :mainLinks="mainLinks"
      :secondaryLinks="secondaryLinks"
    />

    <div class="content">
      <RouterView />

      <div v-if="$route.path.toLowerCase() === '/dashboard'" class="dashboard-content">
        <!-- Tabs -->
        <nav class="mb-4">
          <div class="nav nav-tabs custom-tabs" id="nav-tab" role="tablist">
            <button
              class="nav-link active"
              id="nav-home-tab"
              data-bs-toggle="tab"
              data-bs-target="#nav-home"
              type="button"
              role="tab"
              aria-controls="nav-home"
              aria-selected="true"
            >
              <i class="fas fa-plus-circle me-2"></i> Crear Ticket
            </button>
            <button
              class="nav-link"
              id="nav-profile-tab"
              data-bs-toggle="tab"
              data-bs-target="#nav-profile"
              type="button"
              role="tab"
              aria-controls="nav-profile"
              aria-selected="false"
            >
              <i class="fas fa-ticket-alt me-2"></i> Mis Tickets
            </button>
          </div>
        </nav>

        <div class="tab-content" id="nav-tabContent">
          <!-- Crear Ticket -->
          <div
            class="tab-pane fade show active"
            id="nav-home"
            role="tabpanel"
            aria-labelledby="nav-home-tab"
          >
            <div class="text-center mb-5">
              <h1 class="fw-bold text-dark mb-3">👋 ¡Hola! ¿En qué podemos ayudarte?</h1>
              <p class="lead text-muted">
                Selecciona una categoría para iniciar el proceso de creación de un ticket.
              </p>
            </div>

            <!-- Loader -->
            <div v-if="loading" class="text-center my-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
            </div>

            <!-- Categorías -->
            <div
              v-else-if="categories.length > 0"
              class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4"
            >
              <div v-for="category in categories" :key="category.id" class="col">
                <div class="card h-100 shadow-sm border-0 transition-hover">
                  <div class="card-body d-flex flex-column text-center p-4">
                    <i class="fas fa-folder-open fa-3x text-primary mb-3"></i>
                    <h5 class="card-title fw-bold text-dark">{{ category.name }}</h5>
                    <p class="card-text text-muted flex-grow-1">
                      {{ category.description }}
                    </p>
                    <router-link
                      :to="`/portal/${category.id}`"
                      class="btn btn-outline-primary mt-3 rounded-pill"
                    >
                      Crear Ticket
                    </router-link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Sin categorías -->
            <div v-else class="text-center my-5">
              <p class="text-muted">No hay categorías de tickets disponibles en este momento.</p>
            </div>
          </div>

          <!-- Mis Tickets -->
          <div
            class="tab-pane fade"
            id="nav-profile"
            role="tabpanel"
            aria-labelledby="nav-profile-tab"
          >
            <ul class="list-group mt-3 shadow-sm rounded">
              <li
                v-for="ticket in tickets"
                :key="ticket.id"
                class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
              >
                <div>
                  <span class="badge bg-primary me-2">#{{ ticket.id }}</span>
                  <span class="fw-bold">{{ ticket.title }}</span>
                  <small class="d-block text-muted">Creado el {{ ticket.creation_date }}</small>
                </div>
                <router-link
                  :to="`/follow/${ticket.id}`"
                  class="btn btn-sm btn-success rounded-pill"
                >
                  Ver seguimiento
                </router-link>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '@/components/SideBarC.vue'
import Permissions from '@/services/permissions'
import { useUserStore } from '@/stores/user'
import { makeQuery } from '@/services/api'

export default {
  components: { Sidebar },
  data() {
    return {
      mainLinks: [],
      secondaryLinks: [],
      categories: [],
      loading: true,
      tickets: [],
    }
  },
  computed: {
    userProfileImage() {
      const userStore = useUserStore()
      return (
        userStore.profile_image ||
        `https://ui-avatars.com/api/?name=${userStore.name}&background=random`
      )
    },
    Profile() {
      const userStore = useUserStore()
      return userStore.username
    },
  },
  async created() {
    const userPermissions = await Permissions.checkPermissions([
      'TICKETS.VIEW',
      'SETTINGS.VIEW',
      'DASHBOARD.VIEW',
      'ACTIVITY.VIEW',
      'USERS.VIEW',
    ])

    this.mainLinks = [
      { icon: 'home', url: '/dashboard' },
      Permissions.hasPermission(userPermissions, 'TICKETS.VIEW') && {
        icon: 'cube',
        url: '/tickets',
      },
      Permissions.hasPermission(userPermissions, 'USERS.VIEW') && {
        icon: 'user-group',
        url: '/users',
      },
      Permissions.hasPermission(userPermissions, 'ACTIVITY.VIEW') && {
        icon: 'chart-line',
        url: '/activity',
      },
    ].filter(Boolean)

    this.secondaryLinks = [
      Permissions.hasPermission(userPermissions, 'SETTINGS.VIEW') && {
        icon: 'cog',
        url: '/settings',
      },
      {
        icon: 'sign-out-alt',
        url: '/logout',
      },
    ].filter(Boolean)

    if (this.$route.path.toLowerCase() === '/dashboard') {
      this.fetchCategories()
    }

    this.fetchTickets()
  },
  watch: {
    '$route.path'(newPath) {
      if (newPath.toLowerCase() === '/dashboard') {
        this.fetchCategories()
      }
    },
  },
  methods: {
    async fetchCategories() {
      this.loading = true
      try {
        const response = await makeQuery('/categories', 'GET')
        this.categories = response.data || []
      } catch (error) {
        console.error('Error fetching categories:', error)
      } finally {
        this.loading = false
      }
    },

    async fetchTickets() {
      this.loading = true
      try {
        const response = await makeQuery('/tickets/mytickets', 'GET')
        this.tickets = response.data[0] || []
      } catch (error) {
        console.error('Error fetching tickets:', error)
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<style scoped>
.app-container {
  display: flex;
  background: #f8fafc;
}

.content {
  flex-grow: 1;
  padding: 25px;
  margin-left: 70px;
}

@media (max-width: 768px) {
  .content {
    margin-left: 0;
  }
}

.dashboard-content {
  padding-top: 20px;
}

/* Tabs personalizadas */
.custom-tabs .nav-link {
  border: none;
  color: #6c757d;
  font-weight: 500;
  padding: 12px 20px;
  border-radius: 10px 10px 0 0;
  transition: all 0.3s ease;
}

.custom-tabs .nav-link.active {
  background: #fff;
  color: #0d6efd;
  font-weight: 600;
  border-bottom: 3px solid #0d6efd;
  box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.05);
}

/* Cards */
.transition-hover {
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}

.transition-hover:hover {
  transform: translateY(-0.4rem);
  box-shadow: 0 0.8rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}

.card {
  border-radius: 1rem;
}

.card-title {
  font-weight: 600;
}
</style>
