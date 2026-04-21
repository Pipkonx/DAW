<template>
    <div class="py-5 bg-light min-vh-100">
        <div class="container pb-5">
            <header class="mb-5 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-5 fw-black text-dark mb-2">Administración</h1>
                    <p class="text-muted fw-bold small text-uppercase tracking-widest">Panel de Control Ejecutivo y Mantenimiento</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary rounded-pill px-4 fw-bold" @click="clearCache">
                        <i class="bi bi-eraser me-2"></i> LIMPIAR CACHÉ
                    </button>
                    <button class="btn btn-dark rounded-pill px-4 fw-bold" @click="generateBackup">
                        <i class="bi bi-save me-2"></i> NUEVA COPIA
                    </button>
                </div>
            </header>

            <!-- Stats Grid -->
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <h6 class="text-muted fw-bold small text-uppercase tracking-widest mb-3">Usuarios</h6>
                        <div class="h2 fw-black text-dark mb-1">{{ stats.total_users || 0 }}</div>
                        <div class="text-success small fw-bold">+{{ stats.new_users_month || 0 }} este mes</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <h6 class="text-muted fw-bold small text-uppercase tracking-widest mb-3">Suscripciones Pro</h6>
                        <div class="h2 fw-black text-primary mb-1">{{ stats.pro_users || 0 }}</div>
                        <div class="text-muted small fw-bold">{{ ((stats.pro_users / stats.total_users) * 100).toFixed(1) }}% conversión</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100">
                        <h6 class="text-muted fw-bold small text-uppercase tracking-widest mb-3">Salud del Sistema</h6>
                        <div class="h2 fw-black text-success mb-1">ÓPTIMA</div>
                        <div class="text-muted small fw-bold">DB: {{ stats.db_size || '0 MB' }}</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white h-100 border-start border-4 border-warning">
                        <h6 class="text-muted fw-bold small text-uppercase tracking-widest mb-3">Tickets Abiertos</h6>
                        <div class="h2 fw-black text-warning mb-1">{{ supportTicketsCount }}</div>
                        <a href="/admin/tickets" class="text-warning small fw-bold text-decoration-none">Ver Centro de Soporte</a>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <!-- User Management Table -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100">
                        <div class="card-header bg-white border-0 p-4">
                            <h5 class="fw-black text-dark mb-0 text-uppercase small tracking-widest">Gestión de Usuarios</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light fs-xs text-muted fw-bold">
                                    <tr>
                                        <th class="border-0 px-4">Usuario</th>
                                        <th class="border-0">Tier</th>
                                        <th class="border-0">Registro</th>
                                        <th class="border-0 text-end px-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users.slice(0, 5)" :key="user.id">
                                        <td class="px-4">
                                            <div class="d-flex align-items-center">
                                                <img :src="'https://ui-avatars.com/api/?name=' + user.name" class="rounded-circle me-3" width="32">
                                                <div>
                                                    <div class="fw-bold text-dark mb-0">{{ user.name }}</div>
                                                    <div class="text-muted small" style="font-size: 10px;">{{ user.email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill fw-bold text-uppercase" 
                                                  :class="user.tier === 'premium' ? 'bg-indigo' : (user.tier === 'pro' ? 'bg-primary' : 'bg-secondary')" style="font-size: 9px;">
                                                {{ user.tier }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">{{ formatDate(user.created_at) }}</td>
                                        <td class="text-end px-4">
                                            <button class="btn btn-light btn-sm rounded-circle" @click="editUser(user)"><i class="bi bi-gear-fill"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white border-0 text-center py-3">
                            <button class="btn btn-link text-decoration-none fw-bold small text-muted">VER TODOS LOS USUARIOS</button>
                        </div>
                    </div>
                </div>

                <!-- Backups & Activity -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white h-100">
                        <h5 class="fw-black text-dark mb-4 text-uppercase small tracking-widest">Backups Recientes</h5>
                        <div class="space-y-4">
                            <div v-for="backup in backups.slice(0, 4)" :key="backup.filename" class="d-flex justify-content-between align-items-center p-3 bg-light rounded-4 mb-3">
                                <div class="min-width-0">
                                    <div class="fw-bold small text-truncate">{{ backup.filename }}</div>
                                    <div class="text-muted" style="font-size: 10px;">{{ backup.size }} • {{ backup.date }}</div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                    <ul class="dropdown-menu shadow border-0 rounded-3">
                                        <li><a class="dropdown-item fw-bold small" @click="restoreBackup(backup.filename)">Restaurar</a></li>
                                        <li><a class="dropdown-item fw-bold small text-danger" @click="deleteBackup(backup.filename)">Eliminar</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div v-if="backups.length === 0" class="text-center py-4 text-muted small italic">No hay copias de seguridad</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'AdminDashboard',
    props: {
        backups: Array,
        users: Array,
        stats: Object,
        apiConsumption: Object,
        globalActivity: Array,
        reports: Array,
        supportTicketsCount: Number
    },
    methods: {
        formatDate(dateStr) {
            return new Date(dateStr).toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
        },
        async clearCache() {
            if (confirm('¿Limpiar toda la caché de la aplicación?')) {
                try {
                    await axios.post('/admin/system/clear-cache');
                    alert('Caché limpiada.');
                } catch (e) { alert('Error.'); }
            }
        },
        async generateBackup() {
            try {
                await axios.post('/admin/backup');
                window.location.reload();
            } catch (e) { alert('Error al generar copia.'); }
        },
        async deleteBackup(filename) {
            if (confirm('¿Eliminar copia?')) {
                try {
                    await axios.delete(`/admin/backup/${filename}`);
                    window.location.reload();
                } catch (e) { alert('Error.'); }
            }
        },
        async restoreBackup(filename) {
            if (confirm(`¿Restaurar sistema al estado de ${filename}? Esta acción no se puede deshacer.`)) {
                try {
                    await axios.post(`/admin/backup/restore/${filename}`);
                    alert('Sistema restaurado.');
                    window.location.reload();
                } catch (e) { alert('Error.'); }
            }
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
.bg-indigo { background-color: #6366f1 !important; color: white; }
.fs-xs { font-size: 0.65rem; }
.space-y-4 > *:not(:last-child) { margin-bottom: 2rem; }
</style>
