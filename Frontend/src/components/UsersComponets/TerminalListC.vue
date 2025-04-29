<template>
  <div class="terminal-list">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th scope="col">Nombre</th>
            <th scope="col" class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="terminal in terminals" :key="terminal.id">
            <td>{{ terminal.terminal_ext }}</td>
            <td>
              <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button class="btn btn-sm btn-danger" @click="Unassign(terminal.id)">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TerminalListC',
  props: {
    terminals: {
      type: Array,
      required: true,
    },
  },
  methods: {
    Unassign(terminalId) {
      this.$emit('unassign-terminal', terminalId)
    },
  },
}
</script>

<style scoped>
.terminal-list {
  margin-top: 20px;
  max-width: 100%;
  overflow-x: hidden;
}

.list-group-item {
  word-break: break-word;
}
</style>
