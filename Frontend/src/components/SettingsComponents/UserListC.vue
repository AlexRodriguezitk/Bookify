<template>
  <div class="user-list container">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Nombre</th>
          <th scope="col">Usuario</th>
          <th scope="col">Tel fono</th>
          <th scope="col">Activo</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.name }}</td>
          <td>
            <RouterLink class="text-decoration-none text-black" :to="`/users/${user.id}`"
              ><span>{{ user.username }}</span> <i class="fas fa-user fa-sm"></i
            ></RouterLink>
          </td>
          <td>{{ user.phone }}</td>
          <td>
            <i class="fas fa-check-circle text-success" v-if="user.is_active"></i>
            <i class="fas fa-times-circle text-danger" v-else></i>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'UserC',
  props: {
    users: {
      type: Array,
      required: true,
    },
  },
}
</script>

<style scoped>
.user-list {
  margin-top: 20px;
}
</style>
