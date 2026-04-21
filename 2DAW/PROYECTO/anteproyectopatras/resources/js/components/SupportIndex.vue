<template>
    <div class="py-5 bg-light min-vh-100">
        <div class="container overflow-hidden">
            <header class="mb-5 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-5 fw-black text-dark mb-2">Centro de Ayuda y Soporte</h1>
                    <p class="text-muted fw-bold small text-uppercase tracking-widest">Resolución de incidencias y consultas</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 fw-bold py-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                    <i class="bi bi-plus-lg me-2"></i> NUEVA CONSULTA
                </button>
            </header>

            <!-- State: No Tickets -->
            <div v-if="tickets.length === 0" class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white border-2 border-dashed">
                <div class="display-1 text-muted opacity-25 mb-4"><i class="bi bi-chat-dots"></i></div>
                <h3 class="fw-black text-dark mb-3">¿En qué podemos ayudarte?</h3>
                <p class="text-muted mb-4 px-lg-5">Nuestro equipo de soporte está listo para resolver tus dudas técnicas o financieras. Abre un ticket para comenzar.</p>
                <button class="btn btn-outline-primary rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                    Crear mi primer ticket
                </button>
            </div>

            <!-- Tickets List -->
            <div v-else class="row g-4">
                <div v-for="ticket in tickets" :key="ticket.id" class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white transition-hover-shadow" @click="goToTicket(ticket.id)" style="cursor: pointer;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="badge-icon me-4 rounded-4 d-flex align-items-center justify-content-center" 
                                     :class="getStatusBg(ticket.status)" style="width: 60px; height: 60px;">
                                    <i class="bi fs-3" :class="getStatusIcon(ticket.status)"></i>
                                </div>
                                <div>
                                    <h4 class="fw-black mb-1 h5 text-dark">{{ ticket.subject }}</h4>
                                    <div class="d-flex gap-3 align-items-center">
                                        <span class="badge rounded-pill px-3 py-2 text-uppercase fw-bold" :class="getStatusClass(ticket.status)" style="font-size: 10px;">
                                            {{ getStatusLabel(ticket.status) }}
                                        </span>
                                        <span class="text-muted small fw-bold">Abierto el {{ formatDate(ticket.created_at) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-none d-md-block text-end">
                                <div v-if="ticket.messages && ticket.messages.length > 0">
                                    <div class="text-muted small fw-bold text-uppercase tracking-tighter mb-1">Último Mensaje</div>
                                    <div class="text-muted small font-italic truncate" style="max-width: 300px;">"{{ ticket.messages[0].message }}"</div>
                                </div>
                            </div>
                            <div class="ms-4">
                                <div class="bg-light p-3 rounded-circle text-muted transition-theme shadow-hover">
                                    <i class="bi bi-chevron-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW TICKET MODAL -->
        <div class="modal fade" id="newTicketModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow rounded-4 p-4">
                    <div class="modal-header border-0 pb-0">
                        <h3 class="fw-black text-dark mb-0">NUEVA CONSULTA</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-4">
                        <p class="text-muted small mb-4">Cuéntanos detalladamente tu problema o sugerencia para que nuestro equipo pueda ayudarte.</p>
                        
                        <form @submit.prevent="submitForm">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase tracking-widest">Asunto de la consulta</label>
                                <input type="text" class="form-control rounded-3 p-3 bg-light border-0" v-model="form.subject" placeholder="Ej: Error al subir PDF de BBVA" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase tracking-widest">Prioridad</label>
                                <select class="form-select rounded-3 p-3 bg-light border-0 fw-bold" v-model="form.priority">
                                    <option value="low">Baja (General)</option>
                                    <option value="medium">Media (Funcionalidad)</option>
                                    <option value="high">Alta (Financiero/Cuenta)</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase tracking-widest">Mensaje Detallado</label>
                                <textarea class="form-control rounded-3 p-3 bg-light border-0" rows="5" v-model="form.message" placeholder="Describe aquí tu problema..." required></textarea>
                            </div>
                            
                            <div class="d-flex gap-3 pt-3">
                                <button type="button" class="btn btn-link text-decoration-none fw-bold text-muted flex-grow-1" data-bs-dismiss="modal">CANCELAR</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold py-3 shadow-sm flex-grow-1" :disabled="submitting">
                                    <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
                                    {{ submitting ? 'ENVIANDO...' : 'ENVIAR TICKET' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'SupportIndex',
    props: {
        tickets: Array
    },
    data() {
        return {
            submitting: false,
            form: {
                subject: '',
                message: '',
                priority: 'medium'
            }
        }
    },
    methods: {
        goToTicket(id) {
            window.location.href = `/support/tickets/${id}`;
        },
        getStatusClass(status) {
            if (status === 'open') return 'bg-warning-subtle text-warning-emphasis border-warning-subtle';
            if (status === 'answered') return 'bg-primary-subtle text-primary-emphasis border-primary-subtle';
            if (status === 'closed') return 'bg-success-subtle text-success-emphasis border-success-subtle';
            return 'bg-secondary-subtle';
        },
        getStatusBg(status) {
            if (status === 'open') return 'bg-warning text-white bg-opacity-10 text-warning';
            if (status === 'answered') return 'bg-primary text-white bg-opacity-10 text-primary';
            if (status === 'closed') return 'bg-success text-white bg-opacity-10 text-success';
            return 'bg-secondary';
        },
        getStatusIcon(status) {
            if (status === 'open') return 'bi-clock';
            if (status === 'answered') return 'bi-chat-left-dots';
            if (status === 'closed') return 'bi-check-circle';
            return 'bi-question-circle';
        },
        getStatusLabel(status) {
            const labels = { open: 'Abierto', answered: 'Respondido', closed: 'Cerrado' };
            return labels[status] || status;
        },
        formatDate(dateStr) {
            return new Date(dateStr).toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
        },
        async submitForm() {
            this.submitting = true;
            try {
                // We use standard form submission if we don't want complex AJAX handling in this pass
                const formData = new FormData();
                formData.append('subject', this.form.subject);
                formData.append('message', this.form.message);
                formData.append('priority', this.form.priority);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const response = await axios.post('/support/tickets', this.form);
                window.location.reload();
            } catch (e) {
                alert('Error al crear el ticket. ' + (e.response?.data?.message || ''));
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
.transition-hover-shadow:hover {
    box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
    transform: translateY(-2px);
}
.transition-hover-shadow { transition: all 0.3s ease; }
.bg-warning-subtle { background-color: #fff3cd !important; }
.bg-primary-subtle { background-color: #cfe2ff !important; }
.bg-success-subtle { background-color: #d1e7dd !important; }
.text-warning-emphasis { color: #664d03 !important; }
.text-primary-emphasis { color: #052c65 !important; }
.text-success-emphasis { color: #0a3622 !important; }
</style>
