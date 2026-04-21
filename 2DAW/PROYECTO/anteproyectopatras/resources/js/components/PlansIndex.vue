<template>
    <div class="py-5 bg-light min-vh-100">
        <div class="container overflow-hidden">
            <!-- Header -->
            <div class="text-center mb-5 pb-4">
                <h1 class="display-4 fw-black text-dark mb-3">Potencia tus Inversiones</h1>
                <p class="lead text-muted max-w-2xl mx-auto px-4">
                    Suscríbete a uno de nuestros planes para eliminar los anuncios y desbloquear herramientas avanzadas de análisis financiero con IA.
                </p>
                <div class="mt-4">
                    <span class="badge rounded-pill bg-white text-primary border px-3 py-2 fw-bold shadow-sm">
                        <i class="bi bi-shield-check me-1"></i> Pagos seguros con Stripe
                    </span>
                </div>
            </div>

            <!-- Rejilla de Planes -->
            <div class="row g-4 justify-content-center">
                <div v-for="plan in plans" :key="plan.id" class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-lg rounded-5 p-4 transition-hover position-relative"
                         :class="{'border border-primary border-4': plan.popular}">
                        
                        <!-- Etiqueta de Popular -->
                        <div v-if="plan.popular" class="position-absolute top-0 start-50 translate-middle">
                            <span class="badge bg-primary rounded-pill px-4 py-2 shadow-lg fw-black text-uppercase tracking-widest" style="font-size: 0.7rem;">
                                Más Popular
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column pt-4">
                            <div class="mb-4">
                                <h3 class="h2 fw-black text-dark mb-2">{{ plan.name }}</h3>
                                <p class="text-muted small fw-bold">{{ plan.description }}</p>
                            </div>

                            <div class="mb-4 py-3 border-top border-bottom border-light">
                                <div class="d-flex align-items-baseline gap-1">
                                    <span class="display-5 fw-black text-dark">{{ plan.price }}€</span>
                                    <span class="text-muted fw-bold">/ mes</span>
                                </div>
                            </div>

                            <!-- Lista de Características -->
                            <ul class="list-unstyled space-y-3 mb-5 flex-grow-1">
                                <li v-for="feature in plan.features" :key="feature" class="d-flex align-items-center gap-3 mb-3">
                                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center text-success" style="width: 24px; height: 24px; flex-shrink: 0;">
                                        <i class="bi bi-check-lg" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <span class="small fw-bold text-dark">{{ feature }}</span>
                                </li>
                            </ul>

                            <button 
                                @click="handleSubscription(plan.id)"
                                :disabled="processing && selectedPlan === plan.id"
                                class="btn w-100 py-3 rounded-4 fw-black text-uppercase tracking-widest shadow"
                                :class="plan.popular ? 'btn-primary' : 'btn-dark bg-opacity-75'"
                            >
                                <span v-if="processing && selectedPlan === plan.id" class="spinner-border spinner-border-sm me-2"></span>
                                {{ processing && selectedPlan === plan.id ? 'Cargando...' : 'Elegir Plan' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <p class="small text-muted fw-bold italic">
                    <i class="bi bi-info-circle me-1"></i> Puedes cancelar o cambiar tu plan en cualquier momento desde tu perfil.
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'PlansIndex',
    props: {
        plans: Array,
        intent: Object
    },
    data() {
        return {
            selectedPlan: null,
            processing: false
        }
    },
    methods: {
        async handleSubscription(planId) {
            this.selectedPlan = planId;
            this.processing = true;
            
            try {
                // Hacemos una petición POST al controlador
                // Como es una MPA, el controlador nos devolverá un redirect (que axios maneja pero nosotros queremos que sea la ventana principal)
                // O mejor, el controlador devuelve la URL y nosotros redirigimos.
                
                // Pero según el controlador que acabamos de modificar:
                // return redirect($checkout->url);
                // Si lo llamamos vía AJAX, axios recibirá el HTML de la página de Stripe o seguirá el redirect.
                // Lo ideal para Checkout es redirección de ventana completa.
                
                // Simulamos un envío de formulario estándar para Checkout:
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/subscription/subscribe';
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
                
                const planInput = document.createElement('input');
                planInput.type = 'hidden';
                planInput.name = 'plan_id';
                planInput.value = planId;
                form.appendChild(planInput);
                
                document.body.appendChild(form);
                form.submit();
                
            } catch (e) {
                console.error(e);
                this.processing = false;
            }
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-5 { border-radius: 2rem !important; }
.rounded-4 { border-radius: 1rem !important; }
.max-w-2xl { max-width: 42rem; }
.tracking-widest { letter-spacing: 0.15em; }
.italic { font-style: italic; }
.transition-hover {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.transition-hover:hover {
    transform: translateY(-10px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,0.175) !important;
}
</style>
