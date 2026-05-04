import { createApp, ref, onMounted } from 'https://unpkg.com/vue@3/dist/vue.global.prod.js';
import DashboardComponent from './components/Dashboard.js';
import { fetchClients, fetchPolicies, fetchPayments } from './services/api.js';
import { registerPrimeVueComponents } from './utils/primevue-config.js';
import { formatCurrency, formatDate } from './utils/formatters.js';

// Hacer los formatters disponibles globalmente para los componentes
window.formatters = { formatCurrency, formatDate };

const DashboardApp = {
    components: {
        'dashboard': DashboardComponent
    },
    setup() {
        const clients = ref([]);
        const policies = ref([]);
        const payments = ref([]);
        const selectedClient = ref(null);
        
        const menuItems = ref([
            { label: 'Dashboard', icon: 'pi pi-home', command: () => { window.location.href = 'dashboard.html'; } },
            { label: 'Reportes', icon: 'pi pi-file-pdf', command: () => { window.location.href = 'reports.html'; } },
            { label: 'Salir', icon: 'pi pi-sign-out', command: () => { logout(); } }
        ]);

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

        const logout = () => {
            // En una MPA, el logout simplemente redirige a la página de login
            window.location.href = 'login.html';
        };

        const viewClient = (client) => {
            if (selectedClient.value && client && selectedClient.value.id === client.id) {
                selectedClient.value = null;
            } else {
                selectedClient.value = client;
            }
        };

        onMounted(() => {
            fetchAllData();
        });

        return {
            clients,
            policies,
            payments,
            selectedClient,
            menuItems,
            logout,
            viewClient,
            fetchAllData
        };
    }
};

const app = createApp(DashboardApp);

// Registrar componentes de PrimeVue
registerPrimeVueComponents(app);

app.mount('#app');
