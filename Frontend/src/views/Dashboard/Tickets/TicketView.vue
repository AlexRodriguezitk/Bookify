<template>
  <div class="ticket-view">
    <div v-if="ticket">
      <div class="d-flex justify-content-start align-items-center mb-3">
        <RouterLink class="btn btn-link text-decoration-none" to="/tickets">
          <i class="fas fa-arrow-left fa-lg"></i>
        </RouterLink>
        <h1 class="mb-0">{{ ticket.title }}</h1>
      </div>

      <div class="row mt-4">
        <div class="col-12 col-md-4">
          <div class="container">
            <div class="card">
              <div class="card-header">
                <h3>Información del ticket</h3>
              </div>
              <div class="card-body">
                <div class="alert alert-light">
                  <span>{{ ticket.description }}</span>
                </div>

                <p>
                  <strong>Estado:</strong>
                  <span
                    class="badge"
                    :class="
                      (() => {
                        switch (ticket.status) {
                          case 'NEW':
                            return 'bg-danger'
                          case 'IN_PROGRESS':
                            return 'bg-warning'
                          case 'CLOSED':
                            return 'bg-primary'
                          default:
                            return 'bg-secondary'
                        }
                      })()
                    "
                  >
                    {{ ticket.status }}
                  </span>
                </p>
                <p><strong>Prioridad:</strong> {{ ticket.priority }}</p>
                <p><strong>Fecha de creación:</strong> {{ ticket.creation_date }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-8">
          <div class="container">
            <div class="card">
              <div class="card-header">
                <h3>Interacciones</h3>
              </div>
              <div class="card-body">
                <div v-for="interaction in interactions" :key="interaction.id">
                  <div class="card mt-2">
                    <div class="card-header d-flex justify-content-between">
                      <span>{{ interaction.user.name }}</span>
                      <span>{{
                        new Intl.DateTimeFormat('es-AR', {
                          year: '2-digit',
                          month: '2-digit',
                          day: '2-digit',
                          hour: '2-digit',
                          minute: '2-digit',
                        }).format(new Date(interaction.interaction_date))
                      }}</span>
                    </div>
                    <div class="card-body">
                      <p>{{ interaction.message }}</p>
                    </div>
                  </div>
                </div>
                <form @submit.prevent="handleAddInteraction" class="mt-3">
                  <div class="form-group">
                    <textarea
                      class="form-control"
                      rows="3"
                      placeholder="Ingrese su respuesta"
                    ></textarea>
                  </div>
                  <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-sm btn-primary mt-2">Enviar</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { makeQuery } from '@/services/api'
export default {
  name: 'TicketView',
  data() {
    return {
      ticket: null,
      interactions: [],
    }
  },
  created() {
    const ticketId = this.$route.params.id
    this.fetchTicketById(ticketId)
    this.fetchinteractions(ticketId)
  },
  methods: {
    goBack() {
      this.$router.push('/tickets')
    },
    async fetchTicketById(ticketId) {
      try {
        const response = await makeQuery(`/tickets/${ticketId}`, 'GET')
        this.ticket = response.data[0]
      } catch (error) {
        console.error('Error fetching ticket:', error)
      }
    },

    async fetchinteractions(ticketId) {
      try {
        const response = await makeQuery(`/tickets/${ticketId}/interactions`, 'GET')
        this.interactions = response.data
      } catch (error) {
        console.error('Error fetching interactions:', error)
      }
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
