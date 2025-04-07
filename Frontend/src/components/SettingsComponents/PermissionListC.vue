<template>
  <div class="permission-list">
    <ul class="list-group">
      <li
        v-for="permission in permissions"
        :key="permission.id"
        class="list-group-item d-flex justify-content-between align-items-start flex-column flex-sm-row"
      >
        <div class="me-3 flex-grow-1">
          <strong>{{ permission.name }}</strong>
          <div class="text-muted small text-break">{{ permission.description }}</div>
        </div>
        <div class="form-check form-switch mt-2 mt-sm-0 align-self-start align-self-sm-center">
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
      type: Array,
      default: () => [],
    },
  },
  methods: {
    togglePermission(id, checked) {
      this.$emit(checked ? 'permission-granted' : 'permission-denied', id)
    },
  },
}
</script>

<style scoped>
.permission-list {
  margin-top: 20px;
  max-width: 100%;
  overflow-x: hidden;
}

.list-group-item {
  word-break: break-word;
}
</style>
