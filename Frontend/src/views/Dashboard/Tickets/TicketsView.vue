<template>
  <main class="main-content">
    <ul class="nav nav-tabs">
      <li class="nav-item">
        <RouterLink
          class="nav-link d-flex align-items-center"
          :class="{ active: $route.path === '/tickets' }"
          aria-current="page"
          to="/tickets"
        >
          <i class="fas fa-inbox me-2"></i>
          Inbox
        </RouterLink>
      </li>
      <li class="nav-item">
        <RouterLink
          class="nav-link d-flex align-items-center"
          :class="{ active: $route.path === '/tickets/groups' }"
          to="/tickets/groups"
        >
          <i class="fas fa-users me-2"></i>
          Grupos</RouterLink
        >
      </li>
    </ul>

    <div v-if="Tickets.length > 0">
      <div v-if="$route.path === '/tickets'">
        <div class="container p-2 border rounded mt-3">
          <InboxC :Tickets="Tickets" />
        </div>
      </div>
    </div>
    <RouterView />
  </main>
</template>

<script>
import InboxC from '@/components/Tickets/InboxC.vue'
import { makeQuery } from '@/services/api'
export default {
  name: 'TicketView',
  components: {
    InboxC,
  },
  data() {
    return {
      Tickets: [],
    }
  },
  created() {
    if (this.$route.path === '/tickets') {
      this.fetchTickets()
    }
  },
  watch: {
    '$route.path'(newPath) {
      if (newPath === '/tickets') {
        this.fetchTickets()
      }
    },
  },
  methods: {
    async fetchTickets() {
      try {
        const response = await makeQuery('/tickets/inbox', 'GET')
        this.Tickets = response.data.tickets
        console.log(this.Tickets)
      } catch (error) {
        console.error('Error fetching tickets:', error)
      }
    },
  },
}
</script>
