<template>
    <div class="py-5 bg-light min-vh-100">
        <div class="container overflow-hidden">
            <!-- Header -->
            <div class="mb-5">
                <h2 class="h3 fw-black text-dark uppercase tracking-tight italic mb-1">
                    Seguridad y Privacidad
                </h2>
                <p class="text-muted small fw-bold">Gestiona el acceso a tu cuenta y protege tu capital.</p>
            </div>

            <div class="row g-4">
                <!-- Card: Autenticación de Doble Factor -->
                <div class="col-12">
                    <div class="card border-0 shadow-lg rounded-5 overflow-hidden position-relative">
                        <!-- Icono decorativo de fondo -->
                        <div class="position-absolute top-0 end-0 p-4 opacity-5 pointer-events-none">
                            <i class="bi bi-shield-check" style="font-size: 8rem;"></i>
                        </div>

                        <div class="card-body p-4 p-md-5 d-flex flex-column flex-md-row align-items-center gap-4 gap-md-5 position-relative">
                            <div class="rounded-4 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary shadow-inner shrink-0" style="width: 80px; height: 80px;">
                                <i class="bi bi-phone-vibrate" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1 text-center text-md-start">
                                <div class="d-flex align-items-center gap-3 justify-content-center justify-content-md-start mb-2">
                                    <h3 class="h4 fw-black text-dark uppercase tracking-tight mb-0 italic">
                                        Doble Factor (TOTP)
                                    </h3>
                                    <span v-if="twoFactorEnabledLocal" class="badge bg-success rounded-pill px-3 py-1 text-uppercase fw-black animate-pulse shadow-sm" style="font-size: 0.65rem;">
                                        Activo
                                    </span>
                                </div>
                                <p class="text-muted small fw-bold mb-0 max-w-xl mx-auto mx-md-0">
                                    Protege tu capital con el estándar de la industria. Al iniciar sesión, solicita un código dinámico desde apps como Google Authenticator o Authy.
                                </p>
                            </div>
                            <div class="shrink-0 pt-3 pt-md-0">
                                <button v-if="!twoFactorEnabledLocal" @click="showSetupModal = true" 
                                    class="btn btn-primary px-4 py-3 rounded-4 fw-black text-uppercase tracking-widest shadow shadow-primary-50">
                                    Configurar Protección
                                </button>
                                <button v-else @click="showDisableModal = true" 
                                    class="btn btn-outline-success px-4 py-3 rounded-4 fw-black text-uppercase tracking-widest d-flex align-items-center gap-2 border-2 group hover-scale shadow-sm">
                                    <i class="bi bi-shield-fill-check group-hover-rotate transition-all"></i>
                                    Gestionar Protección
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Historial -->
                <div class="col-12">
                    <connection-history-table 
                        :activities="activities" 
                        :current-session-id="currentSessionId" 
                    />
                </div>

                <!-- Card: Recordatorio Contraseña -->
                <div class="col-12">
                    <div class="card bg-primary border-0 rounded-5 p-4 text-white shadow-lg overflow-hidden position-relative">
                        <div class="card-body p-2 d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="rounded-4 bg-white bg-opacity-20 d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                                    <i class="bi bi-key-fill" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <h4 class="h5 fw-black uppercase tracking-tight italic mb-1">¿Seguridad comprometida?</h4>
                                    <p class="small fw-bold mb-0 text-white-50">Si detectas sesiones sospechosas, cambia tu contraseña inmediatamente.</p>
                                </div>
                            </div>
                            <a href="/profile/edit" class="btn btn-light px-4 py-3 rounded-4 fw-black text-primary text-uppercase tracking-widest shadow-sm whitespace-nowrap">
                                CAMBIAR CONTRASEÑA
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modales -->
        <two-factor-setup-modal 
            :show="showSetupModal" 
            @close="showSetupModal = false" 
            @success="handle2faSuccess"
        />

        <!-- Modal de Desactivación -->
        <div v-if="showDisableModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg p-4 p-md-5 rounded-5 text-center">
                    <div class="rounded-4 bg-danger bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-4 text-danger" style="width: 80px; height: 80px;">
                        <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="h4 fw-black text-dark text-uppercase mb-3 italic">¿Desactivar Protección?</h3>
                    <p class="text-muted small mb-4 px-3 fw-bold leading-relaxed">
                        Tu cuenta quedará expuesta. Te recomendamos mantener el 2FA activo para proteger tus datos financieros.
                    </p>
                    <div class="d-flex flex-column gap-3 pt-2">
                        <button @click="executeDisable" class="btn btn-danger py-3 rounded-4 fw-black text-uppercase tracking-widest shadow" :disabled="loading">
                             <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                            SÍ, DESACTIVAR PROTECCIÓN
                        </button>
                        <button @click="showDisableModal = false" class="btn btn-light py-3 rounded-4 fw-black text-muted text-uppercase tracking-widest shadow-none">
                            MANTENER PROTEGIDO
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import ConnectionHistoryTable from './ConnectionHistoryTable.vue';
import TwoFactorSetupModal from './TwoFactorSetupModal.vue';

export default {
    name: 'ProfileSecurity',
    components: {
        ConnectionHistoryTable,
        TwoFactorSetupModal
    },
    props: {
        activities: Array,
        twoFactorEnabled: Boolean,
        currentSessionId: String,
    },
    data() {
        return {
            showSetupModal: false,
            showDisableModal: false,
            twoFactorEnabledLocal: this.twoFactorEnabled,
            loading: false
        }
    },
    methods: {
        handle2faSuccess() {
            this.twoFactorEnabledLocal = true;
            this.showSetupModal = false;
            // Opcional: Recargar página o actualizar lista de actividades
            window.location.reload();
        },
        async executeDisable() {
            this.loading = true;
            try {
                await axios.post('/profile/security/disable2fa');
                this.twoFactorEnabledLocal = false;
                this.showDisableModal = false;
                window.location.reload();
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-5 { border-radius: 2rem !important; }
.rounded-4 { border-radius: 1.25rem !important; }
.italic { font-style: italic; }
.tracking-widest { letter-spacing: 0.15em; }
.uppercase { text-transform: uppercase; }
.shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06) !important; }
.max-w-xl { max-width: 36rem; }
.hover-scale { transition: transform 0.2s ease; }
.hover-scale:hover { transform: scale(1.02); }
.group:hover .group-hover-rotate { transform: rotate(15deg); }
.animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .7; }
}
</style>
