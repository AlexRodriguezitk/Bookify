<template>
  <div class="categories-list container">
    <ul class="list-group">
      <li
        v-for="category in categories"
        :key="category.id"
        class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center"
      >
        <span class="mb-2 mb-md-0">{{ category.name }}</span>
        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
          <button class="btn btn-sm btn-warning" @click="editCategory(category)">
            <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Edit</span>
          </button>

          <button class="btn btn-sm btn-danger" @click="deleteCategory(category.id)">
            <i class="fas fa-trash"></i> <span class="d-none d-sm-inline">Delete</span>
          </button>
          <router-link :to="`/categories/${category.id}`" class="btn btn-sm btn-info">
            <i class="fas fa-cogs"></i>
          </router-link>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'CategoriesC',
  props: {
    categories: {
      type: Array,
      required: true,
    },
  },
  methods: {
    editCategory(category) {
      this.$emit('edit-category', category)
    },
    deleteCategory(id) {
      this.$emit('delete-category', id)
    },
  },
}
</script>
