<template>
  <div class="terminal-list container">
    <ul class="list-group">
      <li
        v-for="terminal in terminals"
        :key="terminal.id"
        class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center"
      >
        <span class="mb-2 mb-md-0">{{ terminal.terminal_ext }}</span>
        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
          <button class="btn btn-sm btn-warning" @click="editTerminal(terminal)">
            <i class="fas fa-edit"></i> Edit
          </button>

          <button class="btn btn-sm btn-danger" @click="deleteTerminal(terminal.id)">
            <i class="fas fa-trash"></i> Delete
          </button>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'TerminalsC',
  props: {
    terminals: {
      type: Array,
      required: true,
    },
  },
  methods: {
    editTerminal(terminal) {
      this.$emit('edit-terminal', terminal)
    },
    deleteTerminal(id) {
      this.$emit('delete-terminal', id)
    },
  },
}
</script>

<style scoped>
.terminal-list {
  margin-top: 20px;
}
</style>
