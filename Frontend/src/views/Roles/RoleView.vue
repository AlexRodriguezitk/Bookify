<template>
  <main class="container my-4">
    <div v-if="role" class="d-flex flex-wrap gap-4">
      <!-- Columna: Detalles del rol -->
      <section class="flex-grow-1 flex-basis-100 flex-md-basis-45">
        <h2 class="h5 mb-3 text-center text-md-start">Detalles del Rol</h2>
        <ul class="list-group mb-3">
          <li class="list-group-item">ID: {{ role.id }}</li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span
              >Nombre: <strong>{{ role.name }}</strong></span
            >
            <button class="btn btn-sm" @click="openEditRoleModal(role)">
              <i class="fas fa-edit"></i>
            </button>
          </li>
        </ul>

        <div class="text-end">
          <button
            v-if="role.id !== 1 && role.id !== 2"
            class="btn btn-sm btn-danger"
            @click="deleteRole(role.id)"
          >
            <i class="fas fa-trash"></i>
            <span class="d-none d-md-inline ms-2">Eliminar</span>
          </button>
        </div>
      </section>

      <!-- Columna: Permisos -->
      <section class="flex-grow-1 flex-basis-100 flex-md-basis-50">
        <h2 class="h5 mb-3 text-center text-md-start">Permisos</h2>
        <div class="permissions-grid-container p-2 border rounded">
          <PermissionList
            :permissions="allPermissions"
            :granted-ids="rolePermissions"
            @permission-granted="grantPermission"
            @permission-denied="revokePermission"
          />
        </div>
      </section>
    </div>

    <!-- Preloader -->
    <div v-else class="text-center my-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-3">Cargando rol...</p>
    </div>

    <!-- Modal de Rol -->
    <div class="modal fade" ref="roleModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ isEditingRole ? 'Editar rol' : 'Añadir rol' }}
            </h5>
            <button type="button" class="btn-close" @click="closeRoleModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="isEditingRole ? updateRole() : addRole()">
              <div class="mb-3">
                <label for="roleName" class="form-label">Nombre del rol</label>
                <input
                  id="roleName"
                  v-model="roleToEdit.name"
                  class="form-control"
                  :class="{ 'is-invalid': roleToEdit.name.length > 10 }"
                  type="text"
                  placeholder="Ingrese el nombre del rol"
                  maxlength="10"
                  required
                />
                <div class="invalid-feedback">
                  El nombre del rol es obligatorio y debe tener como máximo 6 caracteres.
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeRoleModal">
                  <i class="fa-solid fa-xmark"></i>
                  <span class="ms-2">Cerrar</span>
                </button>
                <button type="submit" class="btn btn-primary">
                  <i class="fa-solid fa-floppy-disk"></i>
                  <span class="ms-2">Guardar</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
.permissions-grid-container {
  max-height: 600px;
  overflow-y: auto;
  overflow-x: hidden;
}
</style>

<script>
import { makeQuery } from '@/services/api'
import { Modal } from 'bootstrap'
import Permissions from '@/services/permissions'
import PermissionList from '@/components/SettingsComponents/PermissionListC.vue'
export default {
  name: 'RoleView',
  components: {
    PermissionList,
  },
  data() {
    return {
      roleid: null,
      role: null,
      roleToEdit: { id: null, name: '' },
      isEditingRole: false,
      allPermissions: [],
      rolePermissions: [],
      modalRoleInstance: null,
    }
  },
  created() {
    this.fetchRole()
    this.fetchAllPermissions()
    this.fetchRolePermissions()
  },
  methods: {
    async fetchRole() {
      const roleId = this.$route.params.id
      try {
        const response = await makeQuery(`/roles/${roleId}`, 'GET')
        this.role = response.data[0]
      } catch (error) {
        console.error('Error fetching role:', error)
        if (error.response?.status === 404) {
          this.$router.push('/settings')
        }
      }
    },

    async fetchAllPermissions() {
      try {
        const response = await makeQuery('/permissions', 'GET') // [{id, name, description}, ...]

        const permissionsCheck = await Permissions.checkPermissions(
          response.data.map((p) => p.name),
        )

        // Filtramos los permisos que tienen access: true (ignorando ALL como dijiste)
        const grantedPermissionNames = permissionsCheck
          .filter((p) => p.access)
          .map((p) => p.permission)

        console.log('Permisos concedidos:', grantedPermissionNames)
        this.allPermissions = response.data.filter((p) => grantedPermissionNames.includes(p.name))
      } catch (error) {
        console.error('Error fetching permissions:', error)
      }
    },
    async fetchRolePermissions() {
      try {
        const roleId = this.$route.params.id
        const response = await makeQuery(`/permissions/Assignments/${roleId}`, 'GET')
        console.log('Permisos del rol:', response.data)
        this.rolePermissions = response.data.map((perm) => perm.id)
      } catch (error) {
        console.error('Error fetching role permissions:', error)
      }
    },
    openEditRoleModal(role) {
      this.roleToEdit = { ...role }
      this.isEditingRole = true
      this.modalRoleInstance = new Modal(this.$refs.roleModal)
      this.modalRoleInstance.show()
    },
    openAddRoleModal() {
      this.roleToEdit = { id: null, name: '' }
      this.isEditingRole = false
      this.modalRoleInstance = new Modal(this.$refs.roleModal)
      this.modalRoleInstance.show()
    },
    closeRoleModal() {
      if (this.modalRoleInstance) this.modalRoleInstance.hide()
    },
    async updateRole() {
      await makeQuery(`/roles/${this.roleToEdit.id}`, 'PUT', { name: this.roleToEdit.name })
      this.closeRoleModal()
      await this.fetchRole()
    },
    async addRole() {
      await makeQuery('/roles', 'POST', { name: this.roleToEdit.name })
      this.closeRoleModal()
      await this.fetchRole()
    },
    async deleteRole(roleId) {
      try {
        await makeQuery(`/roles/${roleId}`, 'DELETE')
        this.$router.push('/settings')
      } catch (error) {
        console.error('Error al eliminar el rol:', error)
      }
    },

    async grantPermission(id) {
      try {
        await makeQuery(`/permissions/assign/${id}`, 'POST', { rol: this.role.id })
        this.fetchAllPermissions()
        this.rolePermissions.push(id) // Actualiza la lista de permisos del rol localmente
        console.log('Permiso asignado:', id)
      } catch (error) {
        console.error('Error al asignar permiso:', id, error)
      }
    },

    async revokePermission(id) {
      try {
        await makeQuery(`/permissions/unassign/${id}`, 'POST', { rol: this.role.id })
        this.fetchAllPermissions()
        this.rolePermissions = this.rolePermissions.filter((permId) => permId !== id) // Actualiza la lista de permisos del rol localmente

        console.log('Permiso revocado:', id)
      } catch (error) {
        console.error('Error al revocar permiso:', id, error)
      }
    },
  },
}
</script>
