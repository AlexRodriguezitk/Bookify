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
      userProfileImage: 'https://randomuser.me/api/portraits/men/76.jpg', // Imagen de prueba
      mainLinks: [
        { icon: 'home', url: '/' }, // Siempre visible
      ],
      secondaryLinks: [
        { icon: 'sign-out-alt', url: '/logout' }, // Siempre visible
      ],
    }
  },
  async created() {
    const userPermissions = await Permissions.checkPermissions([
      'TICKETS.VIEW',
      'SETTINGS.VIEW',
      'ACTIVITY.VIEW',
    ])

    this.mainLinks = [
      { icon: 'home', url: '/' }, // Siempre visible
      Permissions.hasPermission(userPermissions, 'TICKETS.VIEW') && {
        icon: 'cube',
        url: '/tickets',
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
      { icon: 'sign-out-alt', url: '/logout' }, // Siempre visible
    ].filter(Boolean)
  },
}
</script>

<style scoped>
/* Contenedor principal */
.app-container {
  display: flex;
  height: 100vh; /* Usa toda la altura disponible */
  overflow: hidden; /* Evita desbordamiento */
}

/* Contenido principal */
.content {
  flex-grow: 1;
  padding: 20px;
  overflow-y: auto; /* Permite scroll solo en el contenido */
  margin-left: 80px; /* Espacio para el sidebar */
}
</style>
