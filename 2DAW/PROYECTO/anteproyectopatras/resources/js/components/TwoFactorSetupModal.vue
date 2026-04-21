<template>
    <div v-if="show" class="modal fade show d-block" style="background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 1060;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg p-4 p-md-5 rounded-5 overflow-hidden">
                <button @click="close" class="btn-close position-absolute top-0 end-0 m-4 shadow-none"></button>

                <!-- Paso 1: Introducción -->
                <div v-if="step === 1" class="text-center py-3">
                    <div class="rounded-4 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-4 text-primary shadow-inner" style="width: 80px; height: 80px;">
                        <i class="bi bi-shield-check" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="h4 fw-black text-dark text-uppercase mb-3 italic">Refuerza tu cuenta</h3>
                    <p class="text-muted small mb-4 px-4 fw-bold">
                        Necesitarás una aplicación como Google Authenticator instalada en tu smartphone para escanear un código de seguridad.
                    </p>
                    <button @click="startSetup" class="btn btn-primary w-100 py-3 rounded-4 fw-black text-uppercase tracking-widest shadow" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                        Comenzar Configuración
                    </button>
                </div>

                <!-- Paso 2: Configuración (QR -> Verificación) -->
                <div v-if="step === 2" class="text-center">
                    
                    <!-- Sub-paso 2.1: Escaneo de QR -->
                    <div v-if="!hasScannedQR">
                        <h3 class="h5 fw-black text-dark text-uppercase mb-4 italic">1. Escanea el Código</h3>
                        
                        <div class="bg-white p-4 rounded-4 border shadow-sm d-inline-block mb-4 overflow-hidden">
                            <div v-html="qrCodeUrl" class="qr-container"></div>
                        </div>

                        <p class="text-muted extra-small mb-4 px-4 fw-bold">
                            Abre tu aplicación de autenticación y escanea este código para vincular tu cuenta con FintechPro.
                        </p>

                        <button @click="confirmQRScanned" class="btn btn-dark w-100 py-3 rounded-4 fw-black text-uppercase tracking-widest shadow-sm d-flex align-items-center justify-content-center gap-2">
                            Ya he escaneado el código 
                            <i class="bi bi-check-circle-fill"></i>
                        </button>
                    </div>

                    <!-- Sub-paso 2.2: Verificación con Código -->
                    <div v-else>
                        <h3 class="h5 fw-black text-dark text-uppercase mb-4 italic">2. Verifica la Conexión</h3>
                        
                        <!-- Temporizador -->
                        <div class="bg-light p-3 rounded-4 border d-inline-flex flex-column align-items-center gap-1 mb-4">
                            <div class="position-relative d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <svg class="w-100 h-100" viewBox="0 0 50 50">
                                    <circle cx="25" cy="25" r="22" stroke="#e9ecef" stroke-width="4" fill="none" />
                                    <circle cx="25" cy="25" r="22" stroke="#0d6efd" stroke-width="4" fill="none" 
                                        style="transition: stroke-dashoffset 1s linear; transform: rotate(-90deg); transform-origin: center;"
                                        :stroke-dasharray="138" 
                                        :stroke-dashoffset="138 * (1 - secondsRemaining / 30)" 
                                    />
                                </svg>
                                <span class="position-absolute small fw-black text-dark">{{ secondsRemaining }}</span>
                            </div>
                            <span class="extra-small fw-black text-uppercase text-muted tracking-widest">Sinc</span>
                        </div>

                        <form @submit.prevent="confirmSetup" class="px-2">
                            <div class="mb-4">
                                <label class="extra-small fw-black text-muted text-uppercase tracking-widest d-block mb-3">Introduce el código generado</label>
                                <input 
                                    v-model="code"
                                    type="text" 
                                    maxlength="6"
                                    placeholder="000000"
                                    class="form-control form-control-lg text-center fw-black py-3 border-0 bg-light rounded-4 shadow-inner"
                                    style="letter-spacing: 0.5rem; font-size: 1.5rem;"
                                    required
                                    autofocus
                                />
                                <div v-if="error" class="text-danger extra-small fw-black text-uppercase tracking-widest mt-2">{{ error }}</div>
                            </div>
                            
                            <button 
                                type="submit" 
                                :disabled="loading"
                                class="btn btn-success w-100 py-3 rounded-4 fw-black text-uppercase tracking-widest shadow active-scale"
                            >
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                ACTIVAR PROTECCIÓN
                            </button>

                            <button @click="hasScannedQR = false" type="button" class="btn btn-link btn-sm text-muted text-uppercase fw-black tracking-widest mt-3 text-decoration-none" style="font-size: 0.6rem;">
                                Volver a ver el QR
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Paso 3: Éxito -->
                <div v-if="step === 3" class="text-center py-3">
                    <div class="rounded-4 bg-success bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-4 text-success" style="width: 80px; height: 80px;">
                        <i class="bi bi-shield-lock-fill" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="h4 fw-black text-dark text-uppercase mb-3 italic">¡Protegido con éxito!</h3>
                    <p class="text-muted small mb-4 px-4 fw-bold leading-relaxed">
                        Tu cuenta ahora requiere autenticación de doble factor para cada inicio de sesión. Conserva tu app a buen recaudo.
                    </p>
                    <button @click="close" class="btn btn-dark w-100 py-3 rounded-4 fw-black text-uppercase tracking-widest shadow">
                        Regresar a Seguridad
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'TwoFactorSetupModal',
    props: {
        show: Boolean
    },
    data() {
        return {
            step: 1,
            loading: false,
            hasScannedQR: false,
            qrCodeUrl: '',
            secret: '',
            code: '',
            error: null,
            secondsRemaining: 30,
            timerInterval: null
        }
    },
    watch: {
        show(newVal) {
            if (!newVal) {
                this.stopTimer();
                this.step = 1;
                this.code = '';
                this.error = null;
            }
        }
    },
    beforeDestroy() {
        this.stopTimer();
    },
    methods: {
        async startSetup() {
            this.loading = true;
            try {
                const response = await axios.post('/profile/security/setup2fa');
                this.qrCodeUrl = response.data.qrCodeUrl;
                this.secret = response.data.secret;
                this.step = 2;
                this.hasScannedQR = false;
                await this.fetchCurrentOtpInfo();
                this.startTimer();
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        async fetchCurrentOtpInfo() {
            try {
                const response = await axios.post('/profile/security/current-code', { secret: this.secret });
                this.secondsRemaining = response.data.secondsRemaining;
            } catch (e) {
                console.error(e);
            }
        },
        startTimer() {
            this.stopTimer();
            this.timerInterval = setInterval(() => {
                if (this.secondsRemaining > 0) {
                    this.secondsRemaining--;
                } else {
                    this.fetchCurrentOtpInfo();
                }
            }, 1000);
        },
        stopTimer() {
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
                this.timerInterval = null;
            }
        },
        confirmQRScanned() {
            this.hasScannedQR = true;
        },
        async confirmSetup() {
            this.loading = true;
            this.error = null;
            try {
                await axios.post('/profile/security/activate2fa', {
                    code: this.code,
                    secret: this.secret
                });
                this.step = 3;
                this.stopTimer();
                this.$emit('success');
            } catch (e) {
                this.error = e.response?.data?.errors?.code?.[0] || 'Código inválido';
            } finally {
                this.loading = false;
            }
        },
        close() {
            this.stopTimer();
            this.$emit('close');
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
.extra-small { font-size: 0.65rem; }
.shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06) !important; }
.active-scale:active { transform: scale(0.98); }
.qr-container >>> svg {
    width: 180px;
    height: 180px;
    display: block;
    margin: 0 auto;
}
</style>
