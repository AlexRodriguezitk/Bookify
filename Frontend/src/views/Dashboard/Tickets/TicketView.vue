<template>
  <main class="h-100">
    <div class="ticket" v-if="ticket">
      <div class="d-flex mt-3 align-items-center" style="align-items: center">
        <button class="btn align-self-center" @click="goBack">
          <i class="fas fa-arrow-left"></i>
        </button>
        <h2 class="ms-1">
          <span class="badge bg-primary">Ticket #{{ ticket.id }}</span>
        </h2>
      </div>
      <div class="mx-5">
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
              @take-ticket="takeTicket(ticket.id, $event)"
            />
          </div>
          <div class="col-lg-8 mt-3 mt-lg-0">
            <!-- Ticket tool bar -->
            <div class="btn-group w-100 flex-wrap" role="group" aria-label="Ticket actions">
              <button
                type="button"
                :disabled="!Permissions.hasPermission(PermissionsList, 'TICKET.TRANSFER')"
                class="btn btn-light flex-fill"
                @click="transferTicket(ticket.id)"
              >
                <i class="fas fa-random"></i>
                <span class="d-none d-lg-inline ms-1">Transferir</span>
              </button>

              <button
                type="button"
                class="btn btn-light flex-fill"
                :disabled="!Permissions.hasPermission(PermissionsList, 'TICKET.WORKLOG') || 1 == 1"
                @click="openWorklog(ticket.id)"
              >
                <i class="fas fa-book"></i>
                <span class="d-none d-lg-inline ms-1">Worklog</span>
              </button>

              <button type="button" class="btn btn-light flex-fill" @click="TogglePublic">
                <i :class="publicT ? 'fas fa-lock' : 'fas fa-globe'"></i>
                <span class="d-none d-lg-inline ms-1">
                  {{ publicT ? 'Modo interno' : 'Modo público' }}
                </span>
              </button>

              <button
                type="button"
                :disabled="!Permissions.hasPermission(PermissionsList, 'TICKET.RESOLVE')"
                class="btn btn-primary flex-fill"
                @click="resolveTicket(ticket.id)"
              >
                <i class="fas fa-check"></i>
                <span class="d-none d-lg-inline ms-1">Resolver</span>
              </button>
            </div>

            <div>
              <InteractionsC
                class="mt-3 h-100"
                :interactions="interactions"
                :publict="publicT"
                :ticket-id="ticket.id"
                @send="sendMessage"
              />
            </div>
          </div>
        </div>
      </div>
      <!--Modales-->
      <TransferModal ref="transferModal" :ticketId="ticket.id" @confirm="onTransferConfirmed" />
    </div>
  </main>
</template>
<script>
import TicketInfoC from '@/components/Tickets/TicketInfoC.vue'
import InteractionsC from '@/components/Tickets/InteractionsC.vue'
import TransferModal from '@/components/Tickets/TransferModal.vue'
import Permissions from '@/services/permissions'
import { useUserStore } from '@/stores/user'
import { makeQuery } from '@/services/api'

export default {
  name: 'TicketView',
  components: {
    TicketInfoC,
    InteractionsC,
    TransferModal,
  },
  data() {
    return {
      ticket: null,
      interactions: [],
      PermissionsList: [],
      publicT: false,
      intervalId: null,
      canWorklog: false,
      userStore: useUserStore(),
    }
  },
  created() {
    const ticketId = this.$route.params.id
    this.fetchTicketById(ticketId)
    this.fetchinteractions(ticketId)
    this.intervalId = setInterval(() => {
      this.fetchinteractions(ticketId)
    }, 30000) // 10000 ms = 10 segundos

    this.fetchPermissions()
    console.log()
    //this.canWorklog =
  },
  computed: {
    Permissions() {
      return Permissions
    },
  },
  beforeUnmount() {
    // Detiene el intervalo cuando el componente está a punto de ser destruido
    if (this.intervalId) {
      clearInterval(this.intervalId)
    }
  },
  methods: {
    goBack() {
      this.$router.push('/tickets')
    },

    async fetchPermissions() {
      const userPermissions = await Permissions.checkPermissions([
        'TICKET.TRANSFER',
        'TICKET.INTERN',
        'TICKET.WORKLOG',
        'TICKET.RESOLVE',
      ])
      this.PermissionsList = userPermissions
    },

    async fetchCategories() {
      try {
        const category_id = this.ticket.category
        const response = await makeQuery(`/categories/${category_id}`, 'GET')
        this.ticket.category = response.data[0]
        console.log(this.ticket)
      } catch (error) {
        console.error(error)
      }
    },

    async sendMessage({ content, type }) {
      try {
        // Lógica fetch en el padre
        const data = { message: content, type }
        await makeQuery(`/tickets/${this.ticket.id}/interactions`, 'POST', data)
        this.fetchinteractions(this.ticket.id)
      } catch (err) {
        console.error(err)
      }
    },
    async fetchTicketById(ticketId) {
      try {
        const response = await makeQuery(`/tickets/${ticketId}`, 'GET')
        this.ticket = response.data[0]
        this.fetchCategories()
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

    async takeTicket(ticketId) {
      try {
        const user_id = this.userStore.id
        await makeQuery(`/tickets/${ticketId}`, 'PUT', { asesor: user_id })
        this.fetchTicketById(ticketId)
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

    transferTicket() {
      this.$refs.transferModal.openModal()
    },
    async onTransferConfirmed(payload) {
      try {
        if (payload.mode === 'queue') {
          await makeQuery(`/tickets/${this.ticket.id}/transfer`, 'PUT')
          this.fetchTicketById(this.ticket.id)
          return
        }
        await makeQuery(`/tickets/${this.ticket.id}/transfer/${payload.user}`, 'PUT')
        this.fetchTicketById(this.ticket.id)
      } catch (error) {
        console.error('Error updating ticket title:', error)
      }
    },
    async resolveTicket(ticket_id) {
      await this.uptadeStatus(ticket_id, 'CLOSED')
      this.fetchTicketById(this.ticket.id)
    },
    TogglePublic() {
      if (Permissions.hasPermission(this.PermissionsList, 'TICKET.INTERN')) {
        this.publicT = !this.publicT
      } else {
        this.publicT = false
      }
    },
  },
}
</script>
