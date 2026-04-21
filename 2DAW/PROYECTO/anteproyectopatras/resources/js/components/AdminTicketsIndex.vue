<template>
    <div class="py-5 bg-light min-vh-100">
        <div class="container overflow-hidden">
            <header class="mb-5 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                <div>
                    <h1 class="display-5 fw-black text-dark mb-2">Centro de Soporte Admin</h1>
                    <p class="text-muted fw-bold small text-uppercase tracking-widest">Gestión de consultas técnicas y atención al usuario</p>
                </div>

                <!-- Filtros -->
                <div class="d-flex gap-2">
                    <select v-model="statusFilter" @change="applyFilters" class="form-select border-0 shadow-sm rounded-pill px-4 fw-bold small text-muted bg-white">
                        <option value="all">Todos los Estados</option>
                        <option value="open">Abiertos</option>
                        <option value="answered">Respondidos</option>
                        <option value="closed">Cerrados</option>
                    </select>

                    <select v-model="priorityFilter" @change="applyFilters" class="form-select border-0 shadow-sm rounded-pill px-4 fw-bold small text-muted bg-white">
                        <option value="all">Todas las Prioridades</option>
                        <option value="low">Baja</option>
                        <option value="medium">Media</option>
                        <option value="high">Alta</option>
                    </select>
                </div>
            </header>

            <!-- Tickets Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-xs text-muted fw-bold uppercase">
                            <tr>
                                <th class="border-0 px-4 py-3">Usuario / ID</th>
                                <th class="border-0 py-3">Asunto</th>
                                <th class="border-0 py-3 text-center">Estado</th>
                                <th class="border-0 py-3 text-center">Prioridad</th>
                                <th class="border-0 py-3 text-end px-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="ticket in ticketsData.data" :key="ticket.id" class="transition-hover">
                                <td class="px-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center fw-black me-3 shadow-sm" style="width: 40px; height: 40px;">
                                            {{ ticket.user.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small mb-0">{{ ticket.user.name }}</div>
                                            <div class="text-muted fw-bold uppercase tracking-tighter" style="font-size: 9px;">#{{ ticket.id.toString().padStart(4, '0') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <div class="fw-bold text-dark small mb-1">{{ ticket.subject }}</div>
                                    <div class="text-muted small text-truncate italic" style="max-width: 300px; font-size: 11px;">
                                        "{{ ticket.messages[0]?.message || 'Sin mensajes' }}"
                                    </div>
                                </td>
                                <td class="py-4 text-center">
                                    <span class="badge rounded-pill px-3 py-2 text-uppercase fw-black" :class="getStatusClass(ticket.status)" style="font-size: 9px;">
                                        {{ ticket.status }}
                                    </span>
                                </td>
                                <td class="py-4 text-center">
                                    <span class="fw-black text-uppercase small tracking-tighter" :class="getPriorityClass(ticket.priority)" style="font-size: 9px;">
                                        {{ ticket.priority }}
                                    </span>
                                </td>
                                <td class="py-4 text-end px-4">
                                    <a :href="'/admin/tickets/' + ticket.id" class="btn btn-dark rounded-pill px-4 btn-sm fw-black shadow-sm" style="font-size: 10px;">
                                        ATENDER <i class="bi bi-chevron-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr v-if="ticketsData.data.length === 0">
                                <td colspan="5" class="text-center py-5">
                                    <div class="opacity-25 mb-3"><i class="bi bi-search display-3"></i></div>
                                    <div class="text-muted fw-bold small text-uppercase tracking-widest">No se encontraron tickets</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="ticketsData.links.length > 3" class="card-footer bg-light border-0 py-4 d-flex justify-content-center">
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li v-for="link in ticketsData.links" :key="link.label" class="page-item" :class="{ active: link.active, disabled: !link.url }">
                                <a class="page-link border-0 shadow-sm mx-1 rounded-3 fw-black" :href="link.url || '#'" v-html="link.label"></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AdminTicketsIndex',
    props: {
        ticketsData: Object,
        filters: Object
    },
    data() {
        return {
            statusFilter: this.filters.status || 'all',
            priorityFilter: this.filters.priority || 'all'
        }
    },
    methods: {
        applyFilters() {
            const url = new URL(window.location.href);
            url.searchParams.set('status', this.statusFilter);
            url.searchParams.set('priority', this.priorityFilter);
            url.searchParams.set('page', 1); // Reset page on filter
            window.location.href = url.toString();
        },
        getStatusClass(status) {
            if (status === 'open') return 'bg-warning-subtle text-warning-emphasis';
            if (status === 'answered') return 'bg-primary-subtle text-primary-emphasis';
            if (status === 'closed') return 'bg-success-subtle text-success-emphasis';
            return 'bg-secondary-subtle';
        },
        getPriorityClass(priority) {
            if (priority === 'high') return 'text-danger';
            if (priority === 'medium') return 'text-warning';
            return 'text-muted';
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
.transition-hover { transition: all 0.2s ease; }
.transition-hover:hover { background-color: #f8fafc !important; }
.fs-xs { font-size: 0.65rem; }
.bg-warning-subtle { background-color: #fff3cd !important; }
.bg-primary-subtle { background-color: #cfe2ff !important; }
.bg-success-subtle { background-color: #d1e7dd !important; }
.text-warning-emphasis { color: #664d03 !important; }
.text-primary-emphasis { color: #052c65 !important; }
.text-success-emphasis { color: #0a3622 !important; }
</style>
