<template>
  <div class="sidebar d-flex flex-column align-items-center">
    <!-- Perfil: Muestra imagen o icono -->
    <div class="profile">
      <RouterLink to="/profile">
        <img v-if="profileImage" :src="profileImage" alt="Profile" class="profile-img" />
        <i v-else class="fas fa-user-circle icon profile-icon"></i>
      </RouterLink>
    </div>

    <!-- Links principales -->
    <nav class="nav flex-column main-links">
      <RouterLink v-for="(item, index) in mainLinks" :key="index" :to="item.url" class="nav-link">
        <i :class="`fas fa-${item.icon} icon`"></i>
      </RouterLink>
    </nav>

    <!-- Separador -->
    <div class="mt-auto w-100">
      <hr class="separator" />
    </div>

    <!-- Links secundarios -->
    <nav class="nav flex-column secondary-links">
      <RouterLink
        v-for="(item, index) in secondaryLinks"
        :key="index"
        :to="item.url"
        class="nav-link"
      >
        <i :class="`fas fa-${item.icon} icon`"></i>
      </RouterLink>
    </nav>
  </div>
</template>

<script>
export default {
  props: {
    profileImage: {
      type: String,
      default: null, // Si no se pasa, usa el icono
    },
    mainLinks: {
      type: Array,
      default: () => [],
    },
    secondaryLinks: {
      type: Array,
      default: () => [],
    },
  },
}
</script>

<style scoped>
/* Sidebar flotante a la izquierda */
.sidebar {
  position: fixed;
  left: 1em;
  top: 1.5em;
  height: calc(100vh - 3em);
  width: 60px;
  background: #f8f9fa;
  color: #333;
  padding: 15px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Íconos */
.icon {
  font-size: 24px;
  color: #333;
  transition: color 0.3s;
}

/* Perfil */
.profile {
  margin-bottom: 20px;
}

.profile a {
  text-decoration: none;
}

.profile-icon {
  font-size: 32px;
}

/* Imagen de perfil */
.profile-img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #ddd;
}

/* Estilos de los links */
.nav-link {
  text-align: center;
  padding: 10px 0;
}

.nav-link:hover .icon {
  color: #0d6efd;
}

/* Separador */
.separator {
  border-color: rgba(0, 0, 0, 0.2);
  width: 80%;
  margin: 10px auto;
}
</style>
