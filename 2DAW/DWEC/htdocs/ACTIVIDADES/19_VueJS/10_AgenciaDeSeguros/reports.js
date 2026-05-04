import { createApp, ref, onMounted } from 'https://unpkg.com/vue@3/dist/vue.global.prod.js';
import ReportsComponent from './components/Reports.js';
import { fetchClients, fetchPolicies } from './services/api.js';
import { registerPrimeVueComponents } from './utils/primevue-config.js';
import { formatCurrency, formatDate } from './utils/formatters.js';

// Hacer los formatters disponibles globalmente para los componentes
window.formatters = { formatCurrency, formatDate };

const ReportsApp = {
    components: {
        'reports': ReportsComponent
    },
    setup() {
        const clients = ref([]);
        const policies = ref([]);
        
        const menuItems = ref([
            { label: 'Dashboard', icon: 'pi pi-home', command: () => { window.location.href = 'dashboard.html'; } },
            { label: 'Reportes', icon: 'pi pi-file-pdf', command: () => { window.location.href = 'reports.html'; } },
            { label: 'Salir', icon: 'pi pi-sign-out', command: () => { logout(); } }
        ]);

        const fetchAllData = async () => {
            try {
                const [clientsRes, policiesRes] = await Promise.all([
                    fetchClients(),
                    fetchPolicies()
                ]);
                clients.value = clientsRes;
                policies.value = policiesRes;
            } catch(e) { console.error("Error fetching data", e); }
        };

        const logout = () => {
            // En una MPA, el logout simplemente redirige a la página de login
            window.location.href = 'login.html';
        };

        onMounted(() => {
            fetchAllData();
        });

        return {
            clients,
            policies,
            menuItems,
            logout,
            fetchAllData
        };
    }
};

const app = createApp(ReportsApp);

// Registrar componentes de PrimeVue
registerPrimeVueComponents(app);

app.mount('#app');
