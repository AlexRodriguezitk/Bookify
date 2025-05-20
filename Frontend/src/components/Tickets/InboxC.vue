<template>
  <main>
    <div class="container ticket-list">
      <div class="table-responsive">
        <table class="table table-sm align-middle" style="table-layout: fixed">
          <thead class="table-light">
            <tr>
              <th class="col-1">ID</th>
              <th class="col-3">Título</th>
              <th class="col-2">Asesor</th>
              <th class="col-3">Cliente</th>
              <th class="d-none d-sm-table-cell col-2">Prioridad</th>
              <th class="col-2">Estado</th>
              <th class="d-none d-sm-table-cell col-2">Fecha</th>
              <th class="col-2">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ticket in Tickets" :key="ticket.id">
              <td>{{ ticket.id }}</td>
              <td>
                <div class="ellipsis">{{ ticket.title }}</div>
              </td>

              <td>
                <div class="ellipsis">
                  <RouterLink
                    v-if="ticket.asesor"
                    class="text-decoration-none text-black"
                    :to="`/users/${ticket.asesor.id}`"
                  >
                    {{ ticket.asesor.name }} <i class="fas fa-user fa-sm ms-1"></i>
                  </RouterLink>
                  <span v-else>Sin asignar</span>
                </div>
              </td>

              <td>
                <div class="ellipsis">
                  <RouterLink
                    class="text-decoration-none text-black"
                    :to="`/users/${ticket.client.id}`"
                  >
                    {{ ticket.client.name }} <i class="fas fa-user fa-sm ms-1"></i>
                  </RouterLink>
                </div>
              </td>

              <td class="d-none d-md-table-cell">
                <div class="ellipsis">
                  {{
                    (() => {
                      switch (ticket.priority) {
                        case 'HIGH':
                          return 'Alta'
                        case 'MEDIUM':
                          return 'Media'
                        case 'LOW':
                          return 'Baja'
                        default:
                          return 'Desconocida'
                      }
                    })()
                  }}
                </div>
              </td>

              <td>
                <div class="ellipsis">{{ ticket.status }}</div>
              </td>

              <td class="d-none d-md-table-cell">
                <div class="ellipsis">
                  {{
                    new Intl.DateTimeFormat('es-AR', {
                      year: '2-digit',
                      month: '2-digit',
                      day: '2-digit',
                    }).format(new Date(ticket.creation_date))
                  }}
                </div>
              </td>

              <td>
                <span class="d-inline-flex gap-2 align-items-center">
                  <RouterLink class="btn btn-sm btn-primary" :to="`/tickets/${ticket.id}`">
                    <i class="fas fa-eye"></i> <span class="d-none d-sm-inline">Ver</span>
                  </RouterLink>
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</template>

<script>
export default {
  name: 'InboxC',
  props: {
    Tickets: {
      type: Array,
      required: true,
    },
  },
}
</script>

<style scoped>
.ticket-list {
  margin-top: 20px;
}

.ellipsis {
  max-width: 90%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  position: relative;
  display: block;
  padding-right: 0.2rem;
}

.ellipsis::after {
  content: '';
  position: absolute;
  right: 0;
  top: 0;
  height: 100%;
  width: 2em;
  background: linear-gradient(to right, transparent, var(--bs-table-bg, white));
  pointer-events: none;
}
</style>
