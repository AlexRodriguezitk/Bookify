<template>
  <div class="app-container h-100">
    <!-- Sidebar con imagen de perfil -->
    <Sidebar
      :profileImage="userProfileImage"
      :Username="Profile"
      :mainLinks="mainLinks"
      :secondaryLinks="secondaryLinks"
    />

    <!-- Contenido Principal -->
    <div class="content">
      <RouterView />

      <!-- Contenido exclusivo de /dashboard -->
      <div v-if="$route.path.toLowerCase() === '/dashboard'" class="mt-4">
        <div class="container">
          <h1 class="mb-3">Bienvenido al portal de Bookify</h1>
          <p class="lead">A continuación, selecciona una categoría para crear un nuevo ticket:</p>

          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mt-3">
            <div v-for="category in categories" :key="category.id" class="col">
              <RouterLink :to="`/portal/${category.id}`" class="text-decoration-none text-dark">
                <div class="card h-100">
                  <div class="card-body text-center">
                    <i class="fas fa-folder-open fa-2x text-primary mb-3"></i>
                    <h5 class="card-title">{{ category.name }}</h5>
                  </div>
                </div>
              </RouterLink>
            </div>
          </div>

          <div v-if="categories.length === 0" class="alert alert-info mt-4">
            <i class="fas fa-info-circle me-2"></i> No hay categorías disponibles.
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
      try {
        const response = await makeQuery('/categories', 'GET')
        this.categories = response.data || []
      } catch (error) {
        console.error('Error fetching categories:', error)
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
</style>
