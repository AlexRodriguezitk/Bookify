<template>
  <main class="p-4 bg-gray-100 min-h-screen flex items-center justify-center">
    <div v-if="Profile" class="bg-white shadow-lg rounded-xl p-6 max-w-md w-full text-center">
      
      <!-- Avatar centrado -->
      <img
        :src="`https://ui-avatars.com/api/?name=${Profile.name}&background=random`"
        alt="Avatar"
        class="w-24 h-24 rounded-full mx-auto mb-4"
      />

      <!-- Datos del perfil -->
      <div class="card-body">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ Profile.name }}</h2>
        <p class="text-gray-600 text-sm">@{{ Profile.username }}</p>
        <p class="text-gray-600 text-sm">{{ Profile.phone }}</p>
        <p class="text-gray-600 text-sm">{{ Profile.rol.name }}</p>
      </div>
    </div>

    <div v-else class="text-center text-gray-500">
      Cargando perfil...
    </div>
  </main>
</template>

<script>
import { makeQuery } from '@/services/api';

export default {
  data() {
    return {
      Profile: null,
    };
  },
  created() {
    this.fetchProfile();
  },
  methods: {
    async fetchProfile() {
      try {
        const response = await makeQuery('/profile', 'GET');
        this.Profile = response.data[0];
        console.log(this.Profile);
      } catch (error) {
        console.error('Error al cargar el perfil:', error);
      }
    },
  },
};
</script>

<style scoped>
/* Estilos opcionales si no usas Tailwind */
</style>
