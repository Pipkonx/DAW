import { createApp, ref } from 'https://unpkg.com/vue@3/dist/vue.global.prod.js';
import { loginUser } from './services/api.js';
import { registerPrimeVueComponents } from './utils/primevue-config.js';

const LoginApp = {
    setup() {
        const username = ref('');
        const password = ref('');
        const loginError = ref('');

        const login = async () => {
            loginError.value = ''; // Limpiar errores anteriores
            try {
                const res = await loginUser({ username: username.value, password: password.value });
                if (res.success) {
                    // Redirigir a la página de dashboard
                    window.location.href = 'dashboard.html';
                } else {
                    loginError.value = 'Usuario o contraseña incorrectos';
                }
            } catch (e) {
                console.error("Error durante el login:", e);
                loginError.value = 'Error conectando con la API';
            }
        };

        return {
            username,
            password,
            loginError,
            login
        };
    }
};

const app = createApp(LoginApp);

// Registrar componentes de PrimeVue necesarios para el login
registerPrimeVueComponents(app);

app.mount('#app');
