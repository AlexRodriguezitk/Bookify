<template>
  <div class="user-list container">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th style="width: 20%" class="d-none d-md-table-cell">Nombre</th>
            <th style="width: 25%">Usuario</th>
            <th style="width: 20%" class="d-none d-md-table-cell">Teléfono</th>
            <th style="width: 20%" class="d-none d-md-table-cell">Rol</th>
            <th style="width: 15%" v-if="mode === 'lista'">Activo</th>
            <th style="width: 15%" v-else>Seleccionar</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in users"
            :key="user.id"
            @click="onRowClick(user)"
            :class="{ 'table-active': isSelected(user.id) }"
            style="cursor: pointer"
          >
            <td class="d-none d-md-table-cell">
              <div class="d-flex align-items-center ellipsis">
                <img
                  :src="
                    user.profile_image
                      ? user.profile_image
                      : `https://ui-avatars.com/api/?name=${user.name}&background=random`
                  "
                  @error="onImageError(user.username, $event)"
                  alt="Avatar"
                  class="rounded-circle me-2 d-none d-sm-inline-block"
                  style="width: 30px; height: 30px; object-fit: cover"
                />
                <div>{{ user.name }}</div>
              </div>
            </td>

            <td>
              <RouterLink
                v-if="mode === 'lista'"
                class="text-decoration-none text-black ellipsis"
                :to="`/users/${user.id}`"
                @click.stop
              >
                <span>{{ user.username }}</span>
                <i class="fas fa-user fa-sm ms-1"></i>
              </RouterLink>
              <span v-else class="ellipsis">{{ user.username }}</span>
            </td>

            <td class="d-none d-md-table-cell">
              <div class="ellipsis">{{ user.phone }}</div>
            </td>

            <td class="d-none d-md-table-cell">
              <div class="ellipsis">{{ user.rol ? user.rol.name : 'Sin rol' }}</div>
            </td>

            <td>
              <div
                v-if="mode === 'lista'"
                class="form-check form-switch mt-2 mt-sm-0 align-self-start align-self-sm-center"
              >
                <input
                  class="form-check-input"
                  type="checkbox"
                  :id="`user-${user.id}`"
                  :checked="userStates[user.id]"
                  @change.stop="handleToggle(user)"
                />
              </div>

              <div v-else class="form-check mt-2 mt-sm-0 align-self-start align-self-sm-center">
                <input
                  class="form-check-input"
                  type="checkbox"
                  :id="`select-user-${user.id}`"
                  :checked="isSelected(user.id)"
                  @change="handleSelect(user, $event)"
                  :name="mode === 'seleccion-unica' ? 'single-select' : ''"
                  :value="user.id"
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
    mode: {
      type: String,
      default: 'lista', // 'lista' | 'seleccion-unica' | 'seleccion-multiple'
      validator(value) {
        return ['lista', 'seleccion-unica', 'seleccion-multiple'].includes(value)
      },
    },
  },
  data() {
    return {
      userStates: {},
      selectedIds: [], // Array para selección múltiple o única (pos 0)
    }
  },
  watch: {
    users: {
      immediate: true,
      handler(newUsers) {
        this.userStates = newUsers.reduce((acc, user) => {
          acc[user.id] = user.is_active
          return acc
        }, {})
        this.selectedIds = []
      },
    },
  },
  methods: {
    handleToggle(user) {
      const prev = this.userStates[user.id]
      this.userStates[user.id] = !prev
      this.$emit(prev ? 'user-deactivated' : 'user-activated', user.id, (success) => {
        if (!success) {
          this.userStates[user.id] = prev
        }
      })
    },
    onImageError(username, e) {
      e.target.src = `https://ui-avatars.com/api/?name=${username}&background=random`
    },
    isSelected(userId) {
      if (this.mode === 'seleccion-unica') {
        return this.selectedIds.length > 0 && this.selectedIds[0] === userId
      } else if (this.mode === 'seleccion-multiple') {
        return this.selectedIds.includes(userId)
      }
      return false
    },
    handleSelect(user, event) {
      const checked = event.target.checked
      if (this.mode === 'seleccion-unica') {
        if (checked) {
          this.selectedIds = [user.id]
          this.$emit('selection-changed', user.id)
        } else {
          this.selectedIds = []
          this.$emit('selection-changed', null)
        }
      } else if (this.mode === 'seleccion-multiple') {
        if (checked) {
          if (!this.selectedIds.includes(user.id)) {
            this.selectedIds.push(user.id)
          }
        } else {
          this.selectedIds = this.selectedIds.filter((id) => id !== user.id)
        }
        this.$emit('selection-changed', [...this.selectedIds])
      }
    },
    onRowClick(user) {
      if (this.mode === 'seleccion-unica' || this.mode === 'seleccion-multiple') {
        // Al hacer click en fila simulamos toggle checkbox
        const currentlySelected = this.isSelected(user.id)
        if (this.mode === 'seleccion-unica') {
          if (currentlySelected) {
            this.selectedIds = []
            this.$emit('selection-changed', null)
          } else {
            this.selectedIds = [user.id]
            this.$emit('selection-changed', user.id)
          }
        } else if (this.mode === 'seleccion-multiple') {
          if (currentlySelected) {
            this.selectedIds = this.selectedIds.filter((id) => id !== user.id)
          } else {
            this.selectedIds.push(user.id)
          }
          this.$emit('selection-changed', [...this.selectedIds])
        }
      }
    },
  },
}
</script>
