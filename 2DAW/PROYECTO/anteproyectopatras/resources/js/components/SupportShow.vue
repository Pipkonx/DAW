<template>
    <div class="py-5 bg-light min-vh-100">
        <div class="container container-narrow">
            <!-- Header -->
            <div class="d-flex align-items-center justify-content-between mb-5">
                <div class="d-flex align-items-center">
                    <a href="/support" class="btn btn-link text-muted p-0 me-4 shadow-none">
                        <i class="bi bi-arrow-left fs-3"></i>
                    </a>
                    <div>
                        <h1 class="h3 fw-black text-dark mb-1 uppercase tracking-tighter">{{ ticket.subject }}</h1>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge bg-light rounded-pill text-muted fw-bold px-2 py-1 shadow-sm border" style="font-size: 9px;">
                                ID: #{{ ticket.id.toString().padStart(4, '0') }}
                            </span>
                            <span class="badge rounded-pill px-2 py-1 text-uppercase fw-black" :class="getStatusClass(ticket.status)" style="font-size: 9px;">
                                {{ ticket.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Timeline -->
            <div class="chat-container mb-5">
                <div v-for="msg in ticket.messages" :key="msg.id" 
                     class="d-flex flex-column mb-4"
                     :class="[msg.user_id === authUserId ? 'align-items-end' : 'align-items-start']">
                    
                    <div class="mb-1 d-flex align-items-center gap-2 px-2" :class="[msg.user_id === authUserId ? 'flex-row-reverse' : 'flex-row']">
                        <span class="small fw-black text-muted text-uppercase tracking-widest" style="font-size: 10px;">{{ msg.user.name }}</span>
                        <span class="text-muted small opacity-50" style="font-size: 9px;">{{ formatDateTime(msg.created_at) }}</span>
                    </div>

                    <div class="p-4 rounded-4 shadow-sm max-w-85"
                         :class="[
                             msg.user_id === authUserId 
                                 ? 'bg-primary text-white rounded-bottom-end-0' 
                                 : 'bg-white text-dark rounded-bottom-start-0 border'
                         ]" style="max-width: 80%;">
                        {{ msg.message }}
                    </div>
                </div>
            </div>

            <!-- Reply Form -->
            <div v-if="ticket.status !== 'closed'" class="card border-0 shadow-lg rounded-4 p-3 sticky-bottom mb-5">
                <form @submit.prevent="submitReply" class="d-flex align-items-end gap-3">
                    <div class="flex-grow-1">
                        <textarea v-model="message" rows="2" 
                                  class="form-control border-0 bg-light rounded-4 px-4 py-3 shadow-inner" 
                                  placeholder="Escribe tu respuesta aquí..." 
                                  required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary p-3 rounded-4 shadow-sm" :disabled="submitting || !message.trim()">
                        <i v-if="!submitting" class="bi bi-send-fill fs-4 px-1"></i>
                        <span v-else class="spinner-border spinner-border-sm"></span>
                    </button>
                </form>
            </div>

            <div v-else class="alert alert-secondary rounded-4 border-0 shadow-sm text-center p-4">
                <div class="fw-black text-muted small text-uppercase tracking-widest">Este ticket ha sido marcado como cerrado</div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'SupportShow',
    props: {
        ticket: Object,
        authUserId: Number
    },
    data() {
        return {
            message: '',
            submitting: false
        }
    },
    methods: {
        getStatusClass(status) {
            if (status === 'open') return 'bg-warning-subtle text-warning-emphasis';
            if (status === 'answered') return 'bg-primary-subtle text-primary-emphasis';
            if (status === 'closed') return 'bg-success-subtle text-success-emphasis';
            return 'bg-secondary-subtle text-secondary-emphasis';
        },
        formatDateTime(dateStr) {
            return new Date(dateStr).toLocaleString('es-ES', {
                day: '2-digit',
                month: 'short',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        async submitReply() {
            this.submitting = true;
            try {
                const response = await axios.post(`/support/tickets/${this.ticket.id}/reply`, {
                    message: this.message
                });
                window.location.reload();
            } catch (e) {
                alert('Error al enviar la respuesta.');
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1.25rem !important; }
.rounded-bottom-end-0 { border-bottom-right-radius: 0 !important; }
.rounded-bottom-start-0 { border-bottom-left-radius: 0 !important; }
.container-narrow { max-width: 800px; }
.shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
.bg-warning-subtle { background-color: #fff3cd !important; }
.bg-primary-subtle { background-color: #cfe2ff !important; }
.bg-success-subtle { background-color: #d1e7dd !important; }
.text-warning-emphasis { color: #664d03 !important; }
.text-primary-emphasis { color: #052c65 !important; }
.text-success-emphasis { color: #0a3622 !important; }
</style>
