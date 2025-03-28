2
<template>
  <div class="ticket-view">
    <h1>Ticket Details</h1>
    <div class="ticket-card">
      <h2>{{ ticket.title }}</h2>
      <p><strong>Description:</strong> {{ ticket.description }}</p>
      <p><strong>Status:</strong> {{ ticket.status }}</p>
      <p><strong>Created At:</strong> {{ ticket.createdAt }}</p>
      <button @click="goBack">Back</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TicketView',
  data() {
    return {
      ticket: {
        title: 'Sample Ticket',
        description: 'This is a sample ticket description.',
        status: 'Open',
        createdAt: '2023-10-01',
      },
    }
  },
  methods: {
    goBack() {
      this.$router.push('/')
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
