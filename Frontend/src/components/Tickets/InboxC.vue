<template>
  <main>
    <div class="container ticket-list">
      <div class="table-responsive">
        <table class="table table-sm align-middle" style="table-layout: fixed">
          <thead class="table-light">
            <tr>
              <th class="col-1">ID</th>
              <th class="col-4">Título</th>
              <th class="d-none d-md-table-cell col-2">Asesor</th>
              <th class="d-none d-md-table-cell col-3">Cliente</th>
              <th class="d-none d-lg-table-cell col-2">Prioridad</th>
              <th class="col-2">Estado</th>
              <th class="col-2">Fecha</th>
              <th class="col-2">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ticket in Tickets" :key="ticket.id">
              <td>{{ ticket.id }}</td>

              <td>
                <div class="ellipsis">{{ ticket.title }}</div>
              </td>

              <td class="d-none d-md-table-cell">
                <div class="ellipsis">
                  <RouterLink
                    v-if="ticket.asesor"
                    class="text-decoration-none text-black"
                    :to="`/users/${ticket.asesor.id}`"
                  >
                    {{ ticket.asesor.name }} <i class="fas fa-user"></i>
                  </RouterLink>
                  <span v-else>Sin asignar</span>
                </div>
              </td>

              <td class="d-none d-md-table-cell">
                <div class="ellipsis">
                  <RouterLink
                    class="text-decoration-none text-black"
                    :to="`/users/${ticket.client.id}`"
                  >
                    {{ ticket.client.name }} <i class="fas fa-user"></i>
                  </RouterLink>
                </div>
              </td>

              <td class="d-none d-lg-table-cell">
                <small
                  class="px-2 py-1 rounded-pill border text-truncate d-inline-block"
                  :class="{
                    'border-danger text-danger bg-light': ticket.priority === 'HIGH',
                    'border-warning text-warning bg-light': ticket.priority === 'MEDIUM',
                    'border-success text-success bg-light': ticket.priority === 'LOW',
                  }"
                  style="
                    max-width: 100px;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                  "
                >
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
                </small>
              </td>

              <td>
                <small
                  class="px-2 py-1 rounded-pill border text-truncate d-inline-block"
                  :class="{
                    'border-primary text-primary bg-light': ticket.status === 'NEW',
                    'border-info text-info bg-light': ticket.status === 'IN_PROGRESS',
                    'border-secondary text-secondary bg-light': ticket.status === 'CLOSED',
                  }"
                  style="
                    max-width: 110px; /* un poco más ancho */
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                  "
                >
                  <span class="d-none d-md-inline">
                    {{
                      (() => {
                        switch (ticket.status) {
                          case 'NEW':
                            return 'Nuevo'
                          case 'IN_PROGRESS':
                            return 'En prog.' // texto más corto
                          case 'CLOSED':
                            return 'Cerrado'
                          default:
                            return 'Desconocido'
                        }
                      })()
                    }}
                  </span>
                  <span class="d-inline d-md-none">
                    <i
                      :class="{
                        'fas fa-circle text-primary': ticket.status === 'NEW',
                        'fas fa-spinner text-info': ticket.status === 'IN_PROGRESS',
                        'fas fa-check text-secondary': ticket.status === 'CLOSED',
                      }"
                    ></i>
                  </span>
                </small>
              </td>

              <td>
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
                <RouterLink class="btn btn-sm btn-primary" :to="`/tickets/${ticket.id}`">
                  <i class="fas fa-eye"></i>
                  <span class="d-none d-sm-inline">Ver</span>
                </RouterLink>
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
  max-width: 100%;
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
