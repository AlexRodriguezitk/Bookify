import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
  state: () => ({
    id: '',
    name: '',
    username: '',
    rol: '',
    profile_image: '',
  }),
  actions: {
    setUser(userData) {
      this.id = userData.id
      this.name = userData.name
      this.username = userData.username
      this.rol = userData.rol
      this.profile_image = userData.profile_image
    },
    clearUser() {
      this.name = ''
      this.username = ''
      this.rol = ''
      this.profile_image = ''
    },
  },
  persist: true,
})
