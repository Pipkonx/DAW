<template>
    <div>
        <!-- Hero Section -->
        <section class="bg-white py-5">
            <div class="container py-5">
                <div class="row align-items-center">
                    <div class="col-lg-6 text-center text-lg-start">
                        <div class="mb-4">
                            <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2 fw-bold">+15 B $ Seguidos</span>
                            <span class="ms-3 text-muted small fw-medium">+500.000 usuarios</span>
                        </div>
                        <h1 class="display-3 fw-black text-dark lh-sm mb-4">
                            Todas sus inversiones. <br/>
                            <span class="text-primary">Una aplicación.</span> <br/>
                            Control total.
                        </h1>
                        <p class="lead text-muted mb-5">
                            Reúna todas sus inversiones en una sola aplicación y obtenga información valiosa mediante un análisis detallado. Diseñado para el inversor moderno.
                        </p>
                        <div class="d-grid d-sm-flex gap-3 justify-content-center justify-content-lg-start">
                            <a v-if="canRegister" href="/register" class="btn btn-primary btn-lg px-5 py-3 rounded-4 shadow-sm fw-bold">
                                Empezar ahora
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 mt-5 mt-lg-0">
                        <div class="position-relative">
                            <img src="/images/mockups/dashboard.png" class="img-fluid rounded-4 shadow-lg border" alt="Dashboard">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="bg-light py-5">
            <div class="container py-5">
                <div class="row g-4">
                    <div v-for="(feature, idx) in features" :key="idx" class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm p-4 rounded-4 transition-hover">
                            <div class="display-6 mb-4">{{ feature.icon }}</div>
                            <h3 class="fw-bold h4">{{ feature.title }}</h3>
                            <p class="text-muted small">{{ feature.desc }}</p>
                            <a href="/register" class="mt-auto text-decoration-none fw-bold small">
                                Empezar ahora <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="py-5">
            <div class="container py-5">
                <h2 class="text-center fw-black mb-5">Preguntas frecuentes</h2>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="accordion accordion-flush" id="faqAccordion">
                            <div v-for="(faq, i) in faqs" :key="i" class="accordion-item border-bottom mb-2 rounded-3 overflow-hidden shadow-sm">
                                <h2 class="accordion-header">
                                    <button 
                                        class="accordion-button collapsed fw-bold p-4" 
                                        type="button" 
                                        @click="toggleFaq(i)"
                                    >
                                        {{ faq.q }}
                                    </button>
                                </h2>
                                <div class="accordion-collapse collapse" :class="{ show: faq.open }">
                                    <div class="accordion-body text-muted p-4 pt-0">
                                        {{ faq.a }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    name: 'Welcome',
    props: {
        canLogin: Boolean,
        canRegister: Boolean
    },
    data() {
        return {
            features: [
                { icon: '📥', title: 'Importar', desc: 'Añada y realice el seguimiento de cualquier activo, acciones, ETFs, fondos, DeFi y más.' },
                { icon: '🌐', title: 'Agregación', desc: 'Siga su patrimonio neto agregado en tiempo real vinculando todas sus cuentas.' },
                { icon: '📈', title: 'Rendimiento', desc: 'La tasa TTWROR le ofrece la visión más precisa de sus beneficios reales.' }
            ],
            faqs: [
                {
                    q: '¿Es seguro vincular mi banco con FintechPro?',
                    a: 'Absolutamente. Utilizamos encriptación de grado bancario (AES-256) y nunca almacenamos tus credenciales.',
                    open: false
                },
                {
                    q: '¿Cómo funciona el analista de IA?',
                    a: 'Nuestro Analista IA procesa de forma anónima tus transacciones para detectar patrones de ahorro.',
                    open: false
                }
            ]
        }
    },
    methods: {
        toggleFaq(index) {
            this.faqs[index].open = !this.faqs[index].open;
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
.bg-primary-subtle { background-color: #e7f1ff; }
.transition-hover {
    transition: transform 0.3s ease;
}
.transition-hover:hover {
    transform: translateY(-5px);
}
</style>
