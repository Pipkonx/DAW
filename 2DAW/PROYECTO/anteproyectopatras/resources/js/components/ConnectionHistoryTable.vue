<template>
    <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
        <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
            <h3 class="h5 fw-black text-dark text-uppercase tracking-widest mb-0 flex items-center gap-2 italic">
                <i class="bi bi-globe text-primary"></i>
                Historial de Conexiones
            </h3>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted fw-black small uppercase tracking-wider border-0">Evento / Dispositivo</th>
                        <th class="px-4 py-3 text-muted fw-black small uppercase tracking-wider border-0">Ubicación / IP</th>
                        <th class="px-4 py-3 text-muted fw-black small uppercase tracking-wider border-0">Navegador / SO</th>
                        <th class="pe-4 py-3 text-end text-muted fw-black small uppercase tracking-wider border-0">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="activity in activities" :key="activity.id" class="transition-hover">
                        <!-- Columna: Evento y Dispositivo -->
                        <td class="ps-4 py-4">
                            <div class="d-flex align-items-center gap-3">
                                <div :class="[
                                    'rounded-3 d-flex align-items-center justify-content-center shadow-sm',
                                    activity.type === 'login' ? 'bg-primary bg-opacity-10 text-primary' : 'bg-success bg-opacity-10 text-success'
                                ]" style="width: 40px; height: 40px;">
                                    <i :class="activity.device === 'mobile' ? 'bi bi-phone' : 'bi bi-laptop'" style="font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-dark text-capitalize text-sm">
                                            {{ activity.type.replace('_', ' ') }}
                                        </span>
                                        <span v-if="activity.session_id === currentSessionId" class="badge bg-success rounded-pill px-2 py-1 text-uppercase fw-black" style="font-size: 0.6rem;">
                                            VIVO
                                        </span>
                                    </div>
                                    <p class="small text-muted fw-bold text-uppercase mb-0 tracking-tighter" style="font-size: 0.65rem;">{{ activity.device || 'Escritorio' }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Columna: Ubicación e IP -->
                        <td class="px-4 py-4">
                            <div class="d-flex flex-column">
                                <span class="small fw-bold text-dark d-flex align-items-center gap-1">
                                    <i class="bi bi-geo-alt text-danger"></i>
                                    {{ activity.city || 'Local' }}, {{ activity.country || 'Reserved' }}
                                </span>
                                <span class="small text-muted font-monospace" style="font-size: 0.75rem;">
                                    {{ activity.ip_address || '127.0.0.1' }}
                                </span>
                            </div>
                        </td>

                        <!-- Columna: Navegador y SO -->
                        <td class="px-4 py-4">
                            <div class="d-flex flex-column">
                                <span class="small fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-browser-chrome text-muted"></i>
                                    {{ activity.browser }} {{ activity.browser_version }}
                                </span>
                                <p class="small text-muted fw-bold text-uppercase mb-0 tracking-widest ms-4" style="font-size: 0.65rem;">
                                    {{ activity.os }}
                                </p>
                            </div>
                        </td>

                        <!-- Columna: Fecha -->
                        <td class="pe-4 py-4 text-end">
                            <div class="small fw-bold text-dark">
                                {{ formatDate(activity.created_at) }}
                            </div>
                            <div class="small text-muted fw-black text-uppercase tracking-tighter" style="font-size: 0.6rem;">
                                {{ activity.session_id === currentSessionId ? 'Sesión Actual' : 'Histórico' }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ConnectionHistoryTable',
    props: {
        activities: Array,
        currentSessionId: String
    },
    methods: {
        formatDate(dateString) {
            return new Date(dateString).toLocaleString('es-ES', {
                day: '2-digit',
                month: 'short',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-5 { border-radius: 1.5rem !important; }
.tracking-widest { letter-spacing: 0.1em; }
.italic { font-style: italic; }
.uppercase { text-transform: uppercase; }
.font-monospace {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
.transition-hover:hover {
    background-color: rgba(0,0,0,0.02) !important;
}
</style>
