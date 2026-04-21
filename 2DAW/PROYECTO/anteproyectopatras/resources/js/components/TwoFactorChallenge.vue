<template>
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light py-5">
        <div class="container overflow-hidden">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card border-0 shadow-lg rounded-5 overflow-hidden p-4 p-md-5 bg-white position-relative">
                        <!-- Icono decorativo -->
                        <div class="text-center mb-5">
                            <div class="rounded-4 bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center text-primary mb-4 p-4 shadow-inner">
                                <i class="bi bi-shield-lock" style="font-size: 2.5rem;"></i>
                            </div>
                            <h1 class="h3 fw-black text-dark text-uppercase tracking-tighter italic mb-2">
                                Doble Factor
                            </h1>
                            <p class="small fw-bold text-muted text-uppercase tracking-widest px-4">
                                Introduce el código de 6 dígitos de tu aplicación de autenticación
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="px-2">
                            <div class="mb-4">
                                <label class="extra-small fw-black text-muted text-uppercase tracking-widest d-block text-center mb-4">
                                    Código de Seguridad
                                </label>
                                
                                <div class="position-relative">
                                    <input
                                        ref="codeInput"
                                        v-model="code"
                                        type="text"
                                        class="form-control form-control-lg text-center fw-black py-4 border-0 bg-light rounded-4 shadow-inner"
                                        style="letter-spacing: 0.6rem; font-size: 2.5rem;"
                                        required
                                        autofocus
                                        autocomplete="one-time-code"
                                        maxlength="6"
                                        placeholder="000000"
                                    />
                                </div>

                                <div v-if="error" class="text-danger text-center extra-small fw-black text-uppercase tracking-widest mt-4">
                                    {{ error }}
                                </div>
                            </div>

                            <div class="pt-2 mb-5">
                                <button 
                                    type="submit"
                                    class="btn btn-primary w-100 py-3 rounded-4 fw-black text-uppercase tracking-widest shadow shadow-primary-50 active-scale transition-all d-flex align-items-center justify-content-center gap-2" 
                                    :disabled="processing"
                                >
                                    <span v-if="!processing">Verificar Mi Identidad</span>
                                    <span v-else class="spinner-border spinner-border-sm"></span>
                                    <i v-if="!processing" class="bi bi-rocket-takeoff"></i>
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="extra-small text-muted fw-bold text-uppercase tracking-widest mb-3 px-3">
                                    Abre tu aplicación de autenticación para obtener el código dinámico.
                                </p>
                                <a href="/login" class="extra-small text-primary fw-black text-uppercase tracking-widest text-decoration-none">
                                    Volver al inicio de sesión
                                </a>
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
    name: 'TwoFactorChallenge',
    data() {
        return {
            code: '',
            processing: false,
            error: null
        }
    },
    mounted() {
        if (this.$refs.codeInput) {
            this.$refs.codeInput.focus();
        }
    },
    methods: {
        async submit() {
            this.processing = true;
            this.error = null;
            try {
                // Enviamos vía POST. Como es una MPA, si el controlador devuelve redirección, axios lo seguirá.
                // Sin embargo, para redirigir la ventana completa, a veces es mejor que el controlador devuelva JSON con la URL.
                // En nuestro controlador, devuelve redirect().intented().
                // Axios lo seguirá y obtendrá el HTML del Dashboard.
                // Para login, a veces es más limpio usar un submit de formulario tradicional.
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/login/2fa';
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
                
                const codeInput = document.createElement('input');
                codeInput.type = 'hidden';
                codeInput.name = 'code';
                codeInput.value = this.code;
                form.appendChild(codeInput);
                
                document.body.appendChild(form);
                form.submit();
                
            } catch (e) {
                this.error = e.response?.data?.errors?.code?.[0] || 'Error de validación';
                this.processing = false;
            }
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-5 { border-radius: 2.5rem !important; }
.rounded-4 { border-radius: 1.5rem !important; }
.italic { font-style: italic; }
.tracking-widest { letter-spacing: 0.2em; }
.tracking-tighter { letter-spacing: -0.05em; }
.extra-small { font-size: 0.65rem; }
.shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.08) !important; }
.shadow-primary-50 { box-shadow: 0 10px 25px -5px rgba(13, 110, 253, 0.4) !important; }
.active-scale:active { transform: scale(0.97); }
</style>
