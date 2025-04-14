<template>
  <div class="fields-list">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th scope="col">Campo</th>
            <th scope="col">Tipo</th>
            <th scope="col" class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="field in fields" :key="field.id">
            <td>{{ field.name }}</td>
            <td>{{ field.type }}</td>
            <td>
              <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button class="btn btn-sm btn-warning" @click="openEditFieldModal(field)">
                  <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Editar</span>
                </button>

                <button class="btn btn-sm btn-danger" @click="deleteField(field.id)">
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
  name: 'FieldsListC',
  props: {
    fields: {
      type: Array,
      required: true,
    },
  },
  methods: {
    openEditFieldModal(field) {
      this.$emit('edit-field', field)
    },
    deleteField(fieldId) {
      this.$emit('delete-field', fieldId)
    },
  },
}
</script>

<style scoped>
.fields-list {
  margin-top: 20px;
  max-width: 100%;
  overflow-x: hidden;
}

.list-group-item {
  word-break: break-word;
}
</style>
