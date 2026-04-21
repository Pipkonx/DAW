<template>
    <div class="py-5 bg-light min-vh-100">
        <div class="container overflow-hidden">
            <h1 class="fw-black text-dark mb-5 display-5">Ajustes de Cuenta</h1>

            <div class="row g-5">
                <!-- Sidebar Navigation -->
                <aside class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="nav flex-column nav-pills" role="tablist">
                            <button v-for="tab in tabs" :key="tab.id"
                                    class="nav-link text-start rounded-pill px-4 py-3 mb-2 fw-bold transition-all border-0"
                                    :class="{ active: activeTab === tab.id, 'text-danger': tab.danger }"
                                    @click="activeTab = tab.id">
                                <i :class="tab.icon" class="me-2"></i> {{ tab.label }}
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- Content Area -->
                <main class="col-lg-9">
                    <div class="card border-0 shadow-sm rounded-4 p-5 bg-white">
                        
                        <!-- TAB: Profile -->
                        <div v-if="activeTab === 'profile'">
                            <h4 class="fw-bold mb-4">Información de Perfil</h4>
                            <p class="text-muted small mb-5">Actualiza la información pública de tu cuenta y los elementos visuales de tu perfil social.</p>
                            
                            <form @submit.prevent="updateProfile">
                                <div class="row g-4 mb-5">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Nombre completo</label>
                                        <input type="text" class="form-control rounded-3 p-2 bg-light border-0" v-model="form.name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Nombre de usuario</label>
                                        <div class="input-group">
                                            <span class="input-group-text rounded-start-3 border-0 bg-light text-muted fw-bold">@</span>
                                            <input type="text" class="form-control rounded-end-3 p-2 bg-light border-0" v-model="form.username">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-muted">Biografía</label>
                                        <textarea class="form-control rounded-3 p-3 bg-light border-0" rows="4" v-model="form.bio"></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Guardar cambios</button>
                            </form>
                        </div>

                        <!-- TAB: Password -->
                        <div v-else-if="activeTab === 'password'">
                            <h4 class="fw-bold mb-4">Cambiar Contraseña</h4>
                            <p class="text-muted small mb-5">Asegúrate de que tu cuenta esté protegida con una contraseña larga y compleja.</p>
                            
                            <form @submit.prevent="updatePassword">
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-muted">Contraseña actual</label>
                                    <input type="password" class="form-control rounded-3 p-2 bg-light border-0">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-muted">Nueva contraseña</label>
                                    <input type="password" class="form-control rounded-3 p-2 bg-light border-0">
                                </div>
                                <div class="mb-5">
                                    <label class="form-label fw-bold small text-muted">Confirmar contraseña</label>
                                    <input type="password" class="form-control rounded-3 p-2 bg-light border-0">
                                </div>
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Actualizar contraseña</button>
                            </form>
                        </div>

                        <!-- TAB: Membresía -->
                        <div v-else-if="activeTab === 'subscription'">
                            <h4 class="fw-bold mb-4">Gestión de Membresía</h4>
                            <p class="text-muted small mb-5">Administra tu suscripción a Pipkonx Alpha y accede a herramientas avanzadas.</p>
                            
                            <div v-if="subscription" class="p-4 rounded-4 bg-primary text-white mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <div class="small opacity-75 mb-1">Nivel actual</div>
                                        <div class="h3 fw-black text-capitalize mb-0">{{ subscription.tier }}</div>
                                    </div>
                                    <div class="badge bg-white bg-opacity-25 rounded-pill px-3">{{ subscription.status }}</div>
                                </div>
                                <div class="border-top border-white border-opacity-10 pt-4 d-flex justify-content-between align-items-center">
                                    <div class="small fw-bold">Días restantes: {{ subscription.days_left }}</div>
                                    <div v-if="subscription.ends_at" class="small opacity-75">Expira: {{ subscription.ends_at }}</div>
                                </div>
                            </div>
                            <div v-else class="alert alert-light rounded-4 border-0 p-4 mb-4">
                                <p class="mb-0 fw-bold">No tienes ninguna membresía activa.</p>
                            </div>
                            
                            <button class="btn btn-outline-primary rounded-pill px-4 fw-bold">Ampliar membresía</button>
                        </div>

                        <!-- TAB: Danger -->
                        <div v-else-if="activeTab === 'danger'">
                            <h4 class="fw-bold text-danger mb-4">Zona de Peligro</h4>
                            <p class="text-muted small mb-5">Si eliminas tu cuenta, se perderán todos tus datos de forma permanente. Esta acción no se puede deshacer.</p>
                            
                            <div class="p-4 rounded-4 border border-danger border-opacity-25 bg-danger bg-opacity-10">
                                <p class="fw-bold text-danger mb-4">Confirma tu identidad para proceder con la eliminación.</p>
                                <button class="btn btn-danger rounded-pill px-4 fw-bold">Eliminar mi cuenta definitivamente</button>
                            </div>
                        </div>

                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ProfileEdit',
    props: {
        mustVerifyEmail: Boolean,
        status: String,
        blockedUsers: Array,
        subscription: Object
    },
    data() {
        return {
            activeTab: 'profile',
            tabs: [
                { id: 'profile', label: 'Información de Perfil', icon: 'bi bi-person', danger: false },
                { id: 'password', label: 'Contraseña', icon: 'bi bi-lock', danger: false },
                { id: 'privacy', label: 'Privacidad', icon: 'bi bi-shield-lock', danger: false },
                { id: 'subscription', label: 'Membresía', icon: 'bi bi-star', danger: false },
                { id: 'danger', label: 'Zona de Peligro', icon: 'bi bi-exclamation-triangle', danger: true }
            ],
            form: {
                name: '',
                username: '',
                bio: ''
            }
        }
    },
    methods: {
        updateProfile() {
            // TODO: Post to /profile/update-social or /profile
            console.log('Actualizar perfil', this.form);
        },
        updatePassword() {
            console.log('Actualizar contraseña');
        }
    }
}
</script>

<style scoped>
.fw-black { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
.nav-pills .nav-link.active {
    background-color: #6366f1 !important;
    color: white !important;
    box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3) !important;
}
.nav-pills .nav-link.active.text-danger {
    background-color: #ef4444 !important;
    box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3) !important;
}
.nav-link:not(.active):hover {
    background-color: #f8fafc;
}
.transition-all { transition: all 0.2s ease; }
</style>
