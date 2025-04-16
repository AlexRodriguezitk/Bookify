<template>
  <div class="user-list container">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th style="width: 20%">Nombre</th>
            <th style="width: 25%">Usuario</th>
            <th style="width: 20%" class="d-none d-md-table-cell">Teléfono</th>
            <th style="width: 20%">Rol</th>
            <th style="width: 15%">Activo</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>
              <div class="ellipsis">{{ user.name }}</div>
            </td>

            <td>
              <RouterLink
                class="text-decoration-none text-black ellipsis"
                :to="`/users/${user.id}`"
              >
                <span>{{ user.username }}</span>
                <i class="fas fa-user fa-sm ms-1"></i>
              </RouterLink>
            </td>

            <td class="d-none d-md-table-cell">
              <div class="ellipsis">{{ user.phone }}</div>
            </td>

            <td>
              <div class="ellipsis">{{ user.rol ? user.rol.name : 'Sin rol' }}</div>
            </td>

            <td>
              <div
                class="form-check form-switch mt-2 mt-sm-0 align-self-start align-self-sm-center"
              >
                <input
                  class="form-check-input"
                  type="checkbox"
                  :id="`user-${user.id}`"
                  :checked="userStates[user.id]"
                  @change="handleToggle(user)"
                />
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
  name: 'UserListC',
  props: {
    users: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      userStates: {},
    }
  },
  watch: {
    users: {
      immediate: true,
      handler(newUsers) {
        // Inicializar estados locales por cada usuario
        this.userStates = newUsers.reduce((acc, user) => {
          acc[user.id] = user.is_active
          return acc
        }, {})
      },
    },
  },
  methods: {
    async handleToggle(user) {
      const prev = this.userStates[user.id]
      this.userStates[user.id] = !prev // Cambio visual inmediato

      // Emitimos con callback para controlar el éxito
      this.$emit(prev ? 'user-deactivated' : 'user-activated', user.id, (success) => {
        if (!success) {
          // Volver al estado anterior si falla
          this.userStates[user.id] = prev
        }
      })
    },
  },
}
</script>

<style scoped>
.user-list {
  margin-top: 20px;
}

.ellipsis {
  max-width: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  position: relative;
  display: block;
}

.ellipsis::after {
  content: '';
  position: absolute;
  right: 0;
  top: 0;
  height: 100%;
  width: 2em;
  background: linear-gradient(to right, transparent, white);
}
</style>
