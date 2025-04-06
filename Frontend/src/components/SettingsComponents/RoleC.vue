<template>
  <div class="role-list container">
    <ul class="list-group">
      <li
        v-for="role in roles"
        :key="role.id"
        class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center"
      >
        <span class="mb-2 mb-md-0">{{ role.name }}</span>
        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
          <button class="btn btn-sm btn-warning" @click="editRole(role)">
            <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Editar</span>
          </button>

          <button
            v-if="role.id !== 1 && role.id !== 2"
            class="btn btn-sm btn-danger"
            @click="deleteRole(role.id)"
          >
            <i class="fas fa-trash"></i>
          </button>

          <router-link :to="`/roles/${role.id}`" v-if="role.id !== 1" class="btn btn-sm btn-info">
            <i class="fas fa-cogs"></i>
          </router-link>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'RoleC',
  props: {
    roles: {
      type: Array,
      required: true,
    },
  },
  methods: {
    editRole(role) {
      this.$emit('edit-role', role)
    },
    deleteRole(id) {
      this.$emit('delete-role', id)
    },
  },
}
</script>

<style scoped>
.role-list {
  margin-top: 20px;
}
</style>
