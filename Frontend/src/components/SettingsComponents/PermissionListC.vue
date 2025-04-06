<template>
  <div class="permission-list container">
    <ul class="list-group">
      <li
        v-for="permission in permissions"
        :key="permission.id"
        class="list-group-item d-flex justify-content-between align-items-center"
      >
        <div>
          <strong>{{ permission.name }}</strong>
          <div class="text-muted">{{ permission.description }}</div>
        </div>
        <div class="form-check form-switch">
          <input
            class="form-check-input"
            type="checkbox"
            :id="`perm-${permission.id}`"
            :checked="grantedIds.includes(permission.id)"
            @change="togglePermission(permission.id, $event.target.checked)"
          />
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'PermissionListC',
  props: {
    permissions: {
      type: Array,
      required: true,
    },
    grantedIds: {
      // IDs de permisos que ya están concedidos
      type: Array,
      default: () => [],
    },
  },
  methods: {
    togglePermission(id, checked) {
      if (checked) {
        this.$emit('permission-granted', id)
      } else {
        this.$emit('permission-denied', id)
      }
    },
  },
}
</script>

<style scoped>
.permission-list {
  margin-top: 20px;
}
</style>
