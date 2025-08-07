<template>
  <div class="ticket" v-if="ticket">
    <div class="d-flex mt-3 align-items-center" style="align-items: center">
      <button class="btn align-self-center" @click="goBack">
        <i class="fas fa-arrow-left"></i>
      </button>
      <h2 class="ms-1">
        <span class="badge bg-primary">Ticket #{{ ticket.id }}</span>
      </h2>
    </div>
    <div class="container">
      <h3 class="mt-2 fw-semibold text-dark">
        {{ ticket.title || '\u00A0' }}
      </h3>
      <div class="row">
        <div class="col-lg-4">
          <!-- Left column content here -->
          <TicketInfoC
            :ticket="ticket"
            @update-status="uptadeStatus(ticket.id, $event)"
            @update-title="updateTitle(ticket.id, $event)"
          />
        </div>
        <div class="col-lg-8 mt-3 mt-lg-0">
          <!-- Ticket tool bar -->
          <div class="btn-group w-100 flex-wrap" role="group" aria-label="Ticket actions">
            <button
              type="button"
              class="btn btn-light flex-fill"
              @click="transferTicket(ticket.id)"
            >
              <i class="fas fa-random"></i>
              <span class="d-none d-lg-inline ms-1">Transferir</span>
            </button>

            <button type="button" class="btn btn-light flex-fill" @click="openWorklog(ticket.id)">
              <i class="fas fa-book"></i>
              <span class="d-none d-lg-inline ms-1">Worklog</span>
            </button>

            <button type="button" class="btn btn-light flex-fill" @click="toggleMode">
              <i :class="isInternalMode ? 'fas fa-lock' : 'fas fa-globe'"></i>
              <span class="d-none d-lg-inline ms-1">
                {{ isInternalMode ? 'Modo interno' : 'Modo público' }}
              </span>
            </button>

            <button
              type="button"
              class="btn btn-primary flex-fill"
              @click="resolveTicket(ticket.id)"
            >
              <i class="fas fa-check"></i>
              <span class="d-none d-lg-inline ms-1">Resolver</span>
            </button>
          </div>

          <div v-if="interactions.length">
            <h5>Interactions</h5>
            <ul class="list-group">
              <li v-for="interaction in interactions" :key="interaction.id" class="list-group-item">
                {{ interaction.content }}
              </li>
            </ul>
          </div>
          <div v-else>
            <p>No interactions found.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import TicketInfoC from '@/components/Tickets/TicketInfoC.vue'
import { makeQuery } from '@/services/api'

export default {
  name: 'TicketView',
  components: {
    TicketInfoC,
  },
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

    async uptadeStatus(ticketId, status) {
      try {
        await makeQuery(`/tickets/${ticketId}`, 'PUT', { status: status })
      } catch (error) {
        console.error('Error updating ticket status:', error)
      }
    },

    async updateTitle(ticketId, title) {
      try {
        await makeQuery(`/tickets/${ticketId}`, 'PUT', { title: title })
        this.fetchTicketById(ticketId) // Refresh the ticket data after updating
      } catch (error) {
        console.error('Error updating ticket title:', error)
      }
    },
  },
}
</script>
