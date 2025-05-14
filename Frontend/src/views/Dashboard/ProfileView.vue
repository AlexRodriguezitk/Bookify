<template>
  <main class="p-4 bg-gray-100 min-h-screen flex items-center justify-center">
    <div v-if="Profile" class="bg-white shadow-lg rounded-xl p-6 max-w-xl w-full flex items-start gap-6">
      
      <!-- Avatar actual o previsualizado -->
      <div class="flex flex-col items-center gap-2">
        <img
          :src="avatarPreview || `https://ui-avatars.com/api/?name=${Profile.name}&background=random`"
          alt="Avatar"
          class="w-24 h-24 rounded-full object-cover"
        />
        
        <!-- Input para cambiar avatar -->
        <input type="file" accept="image/*" @change="onFileChange" class="text-sm" />
        <button
          v-if="selectedAvatar"
          @click="uploadAvatar"
          class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
        >
          Guardar Avatar
        </button>
      </div>

      <!-- Datos del perfil -->
      <div class="text-left flex-1">
        <h2 class="text-2xl font-semibold text-gray-800 mb-1">{{ Profile.name }}</h2>
        <p class="text-gray-600 text-sm mb-1">@{{ Profile.username }}</p>
        <p class="text-indigo-500 text-sm font-medium capitalize">Rol: {{ Profile.role || 'Usuario' }}</p>
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
      selectedAvatar: null,
      avatarPreview: null,
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
      } catch (error) {
        console.error('Error al cargar el perfil:', error);
      }
    },

    onFileChange(event) {
      const file = event.target.files[0];
      if (file && file.type.startsWith('image/')) {
        this.selectedAvatar = file;
        this.avatarPreview = URL.createObjectURL(file);
      }
    },

    async uploadAvatar() {
      if (!this.selectedAvatar) return;

      try {
        const formData = new FormData();
        formData.append('avatar', this.selectedAvatar);

        // Simula llamada al backend
        await makeQuery('/profile/avatar', 'POST', formData);

        // Limpia el estado
        this.selectedAvatar = null;
        this.avatarPreview = null;

        // Podrías recargar el perfil si el backend devuelve la nueva imagen
        this.fetchProfile();
        alert('Avatar actualizado correctamente');
      } catch (error) {
        console.error('Error al subir el avatar:', error);
      }
    }
  }
};
</script>

<style scoped>
/* Puedes añadir estilos adicionales si no usas Tailwind */
</style>
