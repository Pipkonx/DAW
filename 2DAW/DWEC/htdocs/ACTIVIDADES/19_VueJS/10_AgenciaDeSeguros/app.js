import { createApp, ref, reactive, computed, onMounted } from 'https://unpkg.com/vue@3/dist/vue.global.prod.js';

// Importar componentes
import Dashboard from './components/Dashboard.js';
import Reports from './components/Reports.js';

// Importar servicios de API
import { fetchClients, fetchPolicies, fetchPayments, addPayment, deletePayment, loginUser } from './services/api.js';

// Importar utilidades
import { registerPrimeVueComponents } from './utils/primevue-config.js';
import { formatCurrency, formatDate } from './utils/formatters.js';

// Hacer los formatters disponibles globalmente para los componentes (alternativa a inyección de dependencias o props)
window.formatters = { formatCurrency, formatDate };

// Comprobar si PrimeVue está cargado
if (typeof primevue === 'undefined') {
    console.error('PrimeVue is not loaded. Please check your internet connection or CDN links.');
    alert('Error: PrimeVue library could not be loaded.');
}

// --- APLICACIÓN PRINCIPAL ---
const App = {
    components: {
        Dashboard,
        Reports
    },
    setup() {
        const isLoggedIn = ref(false);
        const loginForm = ref({ username: '', password: '' });
        const loginError = ref('');
        const currentView = ref('dashboard');
        const clients = ref([]);
        const policies = ref([]);
        const payments = ref([]);
        const selectedClient = ref(null);
        
        const menuItems = ref([
            { label: 'Dashboard', icon: 'pi pi-home', command: () => { currentView.value = 'dashboard'; selectedClient.value = null; } },
            { label: 'Reportes', icon: 'pi pi-file-pdf', command: () => { currentView.value = 'reports'; } }
        ]);

        // Obtiene todos los datos iniciales de la aplicación
        const fetchAllData = async () => {
            try {
                const [clientsRes, policiesRes, paymentsRes] = await Promise.all([
                    fetchClients(),
                    fetchPolicies(),
                    fetchPayments()
                ]);
                clients.value = clientsRes;
                policies.value = policiesRes;
                payments.value = paymentsRes;
            } catch(e) { console.error("Error fetching data", e); }
        };

        // Maneja el proceso de inicio de sesión
        const login = async () => {
            try {
                const res = await loginUser(loginForm.value);
                if (res.success) {
                    isLoggedIn.value = true;
                    loginError.value = '';
                    await fetchAllData();
                } else {
                    loginError.value = 'Usuario o contraseña incorrectos';
                }
            } catch (e) {
                loginError.value = 'Error conectando con la API';
            }
        };

        // Maneja el cierre de sesión
        const logout = () => {
            isLoggedIn.value = false;
            loginForm.value = { username: '', password: '' };
            currentView.value = 'dashboard';
            selectedClient.value = null;
        };

        // Visualiza los detalles de un cliente
        const viewClient = (client) => {
            // Alternar selección o simplemente seleccionar
            if (selectedClient.value && client && selectedClient.value.id === client.id) {
                selectedClient.value = null;
            } else {
                selectedClient.value = client;
            }
            // No cambiar la vista, permanecer en el panel de control
        };

        return {
            isLoggedIn,
            loginForm,
            loginError,
            currentView,
            clients,
            policies,
            payments,
            selectedClient,
            menuItems,
            login,
            logout,
            viewClient,
            fetchAllData
        };
    }
};

const app = createApp(App);

// Registrar componentes de PrimeVue
registerPrimeVueComponents(app);

app.mount('#app');
