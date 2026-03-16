import axios from 'axios';

/**
 * Servicio base para comunicación con el Backend (Laravel).
 * Configura Axios con interceptores y manejo de errores.
 */

const ApiService = axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    withCredentials: false // Forzamos modo API pura (sin cookies/CSRF)
});

// Interceptor de Solicitud (Request)
ApiService.interceptors.request.use(
    (config) => {
        // Aquí se puede inyectar el token Bearer si se usa localStorage
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Interceptor de Respuesta (Response)
ApiService.interceptors.response.use(
    (response) => response,
    (error) => {
        // Manejo global de errores (ej. 401 Unauthorized)
        if (error.response) {
            if (error.response.status === 401) {
                console.warn('Sesión expirada o no autorizada. Redirigiendo a login...');
                // window.location.href = '/login'; // Descomentar cuando exista login
            }
        } else if (error.request) {
            // Error de red o CORS
            console.error('No se pudo contactar con el servidor. ¿Está el backend (Laravel) encendido?');
        }
        return Promise.reject(error);
    }
);

export default ApiService;
