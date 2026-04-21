<template>
    <div class="py-5 bg-light min-vh-100">
        <div class="container overflow-hidden">
            <header class="mb-5 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                <div>
                    <h1 class="display-5 fw-black text-dark mb-2">Centro de Moderación</h1>
                    <p class="text-muted fw-bold small text-uppercase tracking-widest text-danger">Seguridad y Protección a la Comunidad</p>
                </div>
                <div class="px-4 py-3 bg-white border shadow-sm rounded-4 d-flex align-items-center gap-3">
                    <div class="text-muted small fw-bold uppercase tracking-tighter">Reportes Pendientes</div>
                    <div class="h4 fw-black text-danger mb-0">{{ reportsData.total }}</div>
                </div>
            </header>

            <!-- Reports List -->
            <div v-if="reportsData.data.length > 0" class="row g-4">
                <div v-for="report in reportsData.data" :key="report.id" class="col-12">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                        <div class="row g-0">
                            <!-- Left: Denouncer Info -->
                            <div class="col-md-4 p-4 border-end bg-light bg-opacity-50">
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <span class="badge rounded-pill bg-warning text-dark fw-black uppercase px-2 py-1" style="font-size: 8px;">PENDIENTE</span>
                                    <span class="text-muted small fw-bold">{{ formatDate(report.created_at) }}</span>
                                </div>
                                <div class="mb-4">
                                    <h6 class="text-muted small fw-black uppercase tracking-widest mb-2">Denunciante</h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px; font-size: 10px;">
                                            {{ report.user.name.charAt(0) }}
                                        </div>
                                        <span class="fw-bold text-dark small">{{ report.user.name }}</span>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="text-muted small fw-black uppercase tracking-widest mb-2">Motivo</h6>
                                    <div class="p-3 bg-white border border-dashed rounded-3 italic small text-muted">
                                        "{{ report.reason }}"
                                    </div>
                                </div>
                            </div>

                            <!-- Center: Content Preview -->
                            <div class="col-md-5 p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-muted small fw-black uppercase tracking-widest mb-0">
                                        {{ getReportableTypeLabel(report.reportable_type) }}
                                    </h6>
                                    <span v-if="report.reportable" class="text-muted italic small" style="font-size: 10px;">#{{ report.reportable.id }}</span>
                                </div>

                                <div v-if="report.reportable" class="space-y-3">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="avatar-md bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 12px;">
                                            {{ report.reportable.user.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">{{ report.reportable.user.name }}</div>
                                            <div class="text-muted italic" style="font-size: 10px;">Autor del Contenido</div>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-light rounded-4 text-dark small border shadow-inner mb-3">
                                        {{ report.reportable.content }}
                                    </div>
                                    <div v-if="report.reportable.image_path" class="rounded-4 overflow-hidden border">
                                        <img :src="'/storage/' + report.reportable.image_path" class="w-100 object-fit-cover" style="height: 120px;">
                                    </div>
                                </div>
                                <div v-else class="alert alert-danger rounded-4 py-4 text-center border-0 shadow-inner">
                                    <i class="bi bi-trash3 me-2"></i> Contenido ya eliminado
                                </div>
                            </div>

                            <!-- Right: Actions -->
                            <div class="col-md-3 p-4 d-flex flex-column justify-content-center gap-2 bg-light bg-opacity-25 border-start">
                                <button @click="dismiss(report.id)" class="btn btn-outline-secondary rounded-pill py-3 fw-black small text-uppercase tracking-widest w-100 shadow-sm" :disabled="submitting">
                                    <i class="bi bi-x-lg me-2"></i> Ignorar
                                </button>
                                <button @click="confirmDelete(report.id)" class="btn btn-danger rounded-pill py-3 fw-black small text-uppercase tracking-widest w-100 shadow-sm" :disabled="submitting">
                                    <i class="bi bi-trash-fill me-2"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white border-2 border-dashed">
                <div class="display-1 text-success opacity-25 mb-4"><i class="bi bi-shield-check"></i></div>
                <h3 class="fw-black text-dark mb-2 uppercase">Comunidad Protegida</h3>
                <p class="text-muted italic mb-0">No hay reportes pendientes para revisar en este momento.</p>
            </div>

            <!-- Pagination -->
            <div v-if="reportsData.links && reportsData.links.length > 3" class="mt-5 d-flex justify-content-center">
                <nav>
                    <ul class="pagination pagination-sm">
                        <li v-for="link in reportsData.links" :key="link.label" class="page-item" :class="{ active: link.active, disabled: !link.url }">
                            <a class="page-link border-0 shadow-sm mx-1 rounded-3 fw-black" :href="link.url || '#'" v-html="link.label"></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'AdminReports',
    props: {
        reportsData: Object
    },
    data() {
        return {
            submitting: false
        }
    },
    methods: {
        formatDate(dateStr) {
            return new Date(dateStr).toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
        },
        getReportableTypeLabel(type) {
            if (type.includes('Post')) return 'PUBLICACIÓN';
            if (type.includes('Comment')) return 'COMENTARIO';
            return 'CONTENIDO';
        },
        async dismiss(id) {
            if (confirm('¿Ignorar este reporte? El contenido se mantendrá visible.')) {
                this.submitting = true;
                try {
                    await axios.delete(`/admin/reports/${id}/dismiss`);
                    window.location.reload();
                } catch (e) { alert('Error.'); }
                this.submitting = false;
            }
        },
        async confirmDelete(id) {
            if (confirm('¿Eliminar contenido reportado permanentemente? Esta acción es irreversible.')) {
                this.submitting = true;
                try {
                    await axios.delete(`/admin/reports/${id}/action`);
                    window.location.reload();
                } catch (e) { alert('Error.'); }
                this.submitting = false;
            }
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1.25rem !important; }
.shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
.italic { font-style: italic; }
.space-y-3 > *:not(:last-child) { margin-bottom: 1rem; }
</style>
