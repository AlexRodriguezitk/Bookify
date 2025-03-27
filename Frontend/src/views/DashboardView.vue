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

export default {
  components: { Sidebar },
  data() {
    return {
      userProfileImage: 'https://randomuser.me/api/portraits/men/76.jpg', // Imagen de prueba
      mainLinks: [
        { icon: 'home', url: '/' },
        { icon: 'cube', url: '/tickets' },
        { icon: 'chart-line', url: '/activity' },
      ],
      secondaryLinks: [
        { icon: 'cog', url: '/settings' },
        { icon: 'sign-out-alt', url: '/logout' },
      ],
    }
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
