<template>
  <main>
    <div class="container p-2 border rounded mt-3">
      <InboxC :Tickets="Tickets" />
    </div>
  </main>
</template>

<script>
import { makeQuery } from '@/services/api'
import InboxC from '@/components/Tickets/InboxC.vue'
export default {
  name: 'TicketAllView',
  components: {
    InboxC,
  },
  data() {
    return {
      Tickets: [],
    }
  },
  created() {
    this.fetchTickets()
  },
  methods: {
    async fetchTickets() {
      try {
        const response = await makeQuery('/tickets/', 'GET')
        this.Tickets = response.data[0]
        console.log(this.Tickets)
      } catch (error) {
        console.error('Error fetching tickets:', error)
      }
    },
  },
}
</script>
