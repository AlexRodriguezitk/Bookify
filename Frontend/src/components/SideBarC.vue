<template>
  <div>
    <!-- Botón hamburguesa para móviles -->
    <button class="mobile-menu-btn d-md-none" @click="toggleMobileMenu">
      <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar desktop y móvil -->
    <div
      class="sidebar d-flex flex-column align-items-center rounded-3"
      :class="{ 'sidebar-mobile': isMobileMenuOpen }"
    >
      <!-- Perfil -->
      <div class="profile">
        <RouterLink to="/profile">
          <img v-if="profileImage" :src="profileImage" alt="Profile" class="profile-img" />
          <i v-else class="fas fa-user-circle icon profile-icon"></i>
        </RouterLink>
      </div>

      <!-- Enlaces principales -->
      <nav class="nav flex-column main-links">
        <RouterLink
          v-for="(item, index) in mainLinks"
          :key="index"
          :to="item.url"
          class="nav-link btn btn-light mb-2"
          @click="closeMobileMenu"
        >
          <i :class="`fas fa-${item.icon} icon`"></i>
        </RouterLink>
      </nav>

      <!-- Separador -->
      <div class="mt-auto w-100">
        <hr class="separator" />
      </div>

      <!-- Enlaces secundarios -->
      <nav class="nav flex-column secondary-links">
        <RouterLink
          v-for="(item, index) in secondaryLinks"
          :key="index"
          :to="item.url"
          class="nav-link btn btn-light mt-2"
          @click="closeMobileMenu"
        >
          <i :class="`fas fa-${item.icon} icon`"></i>
        </RouterLink>
      </nav>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    profileImage: {
      type: String,
      default: null,
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
  data() {
    return {
      isMobileMenuOpen: false,
    }
  },
  methods: {
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen
    },
    closeMobileMenu() {
      this.isMobileMenuOpen = false
    },
  },
}
</script>

<style scoped>
.sidebar {
  position: fixed;
  left: 1em;
  top: 1.5em;
  height: calc(100vh - 3em);
  width: 60px;
  color: #333;
  padding: 15px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  background-color: #fff;
  z-index: 1000;
  transition: transform 0.3s ease;
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
  padding: 8px 10px;
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

/* Botón menú móvil */
.mobile-menu-btn {
  position: fixed;
  top: 0.5em;
  right: 0.5em;
  background-color: #fff;
  border: none;
  padding: 10px;
  font-size: 24px;
  border-radius: 8px;
  z-index: 1100;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

/* Modo móvil */
@media screen and (max-width: 768px) {
  .sidebar {
    transform: translateX(-150%);
  }

  .sidebar.sidebar-mobile {
    transform: translateX(0);
    height: calc(100vh - 6em);
    width: calc(100vw - 2em);
    background-color: #fff;
  }
}
</style>
