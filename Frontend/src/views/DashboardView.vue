<template>
  <div>
    <h1>Dashboard</h1>

    <!-- Mensaje especial para usuarios con el permiso "ALL" -->
    <p v-if="hasAllPermission" class="admin-message">Tienes acceso total al sistema.</p>

    <!-- Mostrar botón solo si el usuario tiene permiso de "CREATE.API" -->
    <button v-if="canCreateApi" @click="createApi">Crear API</button>

    <button class="btn btn-primary" v-if="canViewTickets" @click="createApi">Ver Tickets</button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Permissions from '@/services/permissions'

const hasAllPermission = ref(false)
const canCreateApi = ref(false)
const canViewTickets = ref(false)

onMounted(async () => {
  try {
    const permissions = await Permissions.checkPermissions(['ALL', 'CREATE.API', 'VIEW.TICKETS'])

    hasAllPermission.value = Permissions.hasPermission(permissions, 'ALL')
    canCreateApi.value = Permissions.hasPermission(permissions, 'CREATE.API')
    canViewTickets.value = Permissions.hasPermission(permissions, 'VIEW.TICKETS')
  } catch (error) {
    console.error('Error al verificar permisos:', error)
  }
})

const createApi = () => {
  console.log('Creando API...')
}
</script>

<style scoped>
.admin-message {
  background: #007bff;
  color: white;
  padding: 10px;
  border-radius: 5px;
  text-align: center;
  font-weight: bold;
  margin-bottom: 20px;
}
</style>
