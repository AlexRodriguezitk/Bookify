<template>
  <div class="app-container">
    <!-- Sidebar con imagen de perfil -->
    <Sidebar
      :profileImage="userProfileImage"
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

export default {
  components: { Sidebar },
  data() {
    return {
      userProfileImage: '', // Imagen de prueba
      mainLinks: [],
      secondaryLinks: [],
    }
  },
  async created() {
    const imageUrl = 'https://randomuser.me/api/portraits/men/90.jpg'
    const img = new Image()
    img.onload = () => {
      this.userProfileImage = imageUrl
    }
    img.onerror = () => {
      this.userProfileImage = null
    }
    img.src = imageUrl
    const userPermissions = await Permissions.checkPermissions([
      'TICKETS.VIEW',
      'SETTINGS.VIEW',
      'DASHBOARD.VIEW',
      'ACTIVITY.VIEW',
      'USERS.VIEW',
      'LOGOUT.VIEW',
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
  margin-left: 80px; /* Espacio para el sidebar */
}
</style>
