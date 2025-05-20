<template>
  <main>
    <div v-if="Profile">
      <div class="container">
        <div class="card">
          <div class="card-body">
            <h3>Nombre:</h3>
            <span>{{ Profile.name }}</span>
            <h3>Nombre de usuario:</h3>
            <span>{{ Profile.username }}</span>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
<script>
import { makeQuery } from '@/services/api'
export default {
  data() {
    return {
      Profile: {},
    }
  },
  created() {
    this.fetchProfile()
  },
  methods: {
    async fetchProfile() {
      try {
        const response = await makeQuery('/profile', 'GET')
        this.Profile = response.data[0]
        console.log(this.Profile)
      } catch (error) {
        console.error('Error fetching profile:', error)
      }
    },
  },
}
</script>
