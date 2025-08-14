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
    </div>
  </div>
</template>

<script>
import Sidebar from '@/components/SideBarC.vue'
import Permissions from '@/services/permissions'
import { useUserStore } from '@/stores/user'

export default {
  components: { Sidebar },
  data() {
    return {
      mainLinks: [],
      secondaryLinks: [],
    }
  },
  computed: {
    // ✅ Computed para traer la imagen del store
    userProfileImage() {
      const userStore = useUserStore()
      return (
        userStore.profile_image ||
        `https://ui-avatars.com/api/?name=${userStore.name}&background=random`
      ) // fallback si no hay imagen
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
  },
}
</script>

<style scoped>
/* Contenedor principal */
.app-container {
  display: flex;
}

/* Contenido principal */
.content {
  flex-grow: 1;
  padding: 20px;
  margin-left: 70px; /* Espacio para el sidebar */
}

@media (max-width: 768px) {
  .content {
    margin-left: 0px; /* Sin margen en pantallas pequeñas */
  }
}
</style>
