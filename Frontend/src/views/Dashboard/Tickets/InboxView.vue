<template>
  <div class="ticket-view">
    <h1>tickets</h1>
    <div v-if="ticket" class="ticket-card">
      <h2>{{ ticket.title }}</h2>
      <p><strong>Description:</strong> {{ ticket.description }}</p>
      <p><strong>Status:</strong> {{ ticket.status }}</p>
      <p><strong>Created At:</strong> {{ ticket.createdAt }}</p>
      <button @click="goBack">Go Back</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TicketView',
  data() {
    return {
      ticket: null,
    }
  },
  created() {
    const ticketId = this.$route.params.id
    this.fetchTicketById(ticketId)
  },
  methods: {
    goBack() {
      this.$router.push('/')
    },
    fetchTicketById(id) {
      // Simulación de datos, reemplázalo con una llamada real a la API
      const mockTickets = {
        1: {
          title: 'Bug Report',
          description: 'Fix login issue',
          status: 'Open',
          createdAt: '2025-03-20',
        },
        2: {
          title: 'Feature Request',
          description: 'Add dark mode',
          status: 'In Progress',
          createdAt: '2025-03-21',
        },
      }

      this.ticket = mockTickets[id] || null
    },
  },
}
</script>

<style scoped>
.ticket-view {
  padding: 20px;
}

.ticket-card {
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 20px;
  max-width: 400px;
  background-color: #f9f9f9;
}

button {
  margin-top: 10px;
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #0056b3;
}
</style>
