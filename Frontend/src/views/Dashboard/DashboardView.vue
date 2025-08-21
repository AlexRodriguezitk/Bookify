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
        <div class="text-center mb-5">
          <h1 class="display-4 fw-bold text-dark mb-2">¡Hola! ¿En qué podemos ayudarte?</h1>
          <p class="lead text-muted">
            Selecciona una categoría a continuación para iniciar el proceso de creación de un ticket
            de soporte.
          </p>
        </div>

        <div v-if="loading" class="text-center my-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
        </div>

        <div
          v-else-if="categories.length > 0"
          class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4"
        >
          <div v-for="category in categories" :key="category.id" class="col">
            <div class="card h-100 shadow-sm border-0 transition-hover">
              <div class="card-body d-flex flex-column text-center p-4">
                <i class="fas fa-folder-open fa-2x text-primary mb-3"></i>
                <h5 class="card-title fw-bold text-primary mb-2">{{ category.name }}</h5>
                <p class="card-text text-muted flex-grow-1">
                  {{ category.description }}
                </p>
                <router-link :to="`/portal/${category.id}`" class="btn btn-primary mt-3">
                  Crear Ticket
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center my-5">
          <p class="text-muted">No hay categorías de tickets disponibles en este momento.</p>
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
  },
}
</script>

<style scoped>
.app-container {
  display: flex;
}

.content {
  flex-grow: 1;
  padding: 20px;
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

.transition-hover {
  transition:
    transform 0.2s ease-in-out,
    box-shadow 0.2s ease-in-out;
}

.transition-hover:hover {
  transform: translateY(-0.5rem);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.card {
  border-radius: 0.75rem; /* Bordes más redondeados */
}

.card-title {
  font-weight: 600; /* Fuente más gruesa para los títulos */
}
</style>
