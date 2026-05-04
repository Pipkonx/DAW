/**
 * Aplicación Principal Vue 3
 */
const { createApp } = Vue;

const app = createApp({
    data() {
        return {
            vistaActual: 'Login',    // Iniciamos en Login
            usuarioLogueado: null,    // Datos del usuario autenticado
            esAdmin: false           // Permisos de administrador
        };
    },
    mounted() {
        // Verificar si hay sesión guardada (opcional para el examen)
        const savedUser = localStorage.getItem('seguros_user');
        if (savedUser) {
            this.usuarioLogueado = JSON.parse(savedUser);
            this.esAdmin = this.usuarioLogueado.username === 'admin';
            this.vistaActual = 'Inicio';
        }
    },
    methods: {
        setVista(vista) {
            this.vistaActual = vista;
        },
        handleLogin(user) {
            this.usuarioLogueado = user;
            this.esAdmin = user.username === 'admin';
            this.vistaActual = 'Inicio';
            localStorage.setItem('seguros_user', JSON.stringify(user));
        },
        handleLogout() {
            this.usuarioLogueado = null;
            this.esAdmin = false;
            this.vistaActual = 'Login';
            localStorage.removeItem('seguros_user');
            Swal.fire('Sesión cerrada', 'Hasta pronto', 'info');
        }
    },
    // Registro de todos los componentes
    components: {
        'Login': Login,
        'Inicio': Inicio,
        'Clientes': Clientes,
        'Polizas': Polizas,
        'DetallePoliza': DetallePoliza
    }
});

// Configuración de PrimeVue (Global)
app.use(primevue.config.default);
app.component('p-datatable', primevue.datatable);
app.component('p-column', primevue.column);
app.component('p-button', primevue.button);

// Exponemos los estilos globales
app.config.globalProperties.UI = UI;

// Montar la aplicación
app.mount('#app');
