import ApiService from './ApiService';

/**
 * Servicio de Autenticación.
 * Gestiona el inicio de sesión, registro y obtención de usuario.
 * Preparado para Laravel Sanctum.
 */
export const AuthService = {
    /**
     * Inicia sesión con credenciales.
     * @param {Object} credentials - { email, password }
     */
    async login(credentials) {
        const response = await ApiService.post('/login', credentials);
        if (response.data.token) {
            localStorage.setItem('auth_token', response.data.token);
        }
        return response.data;
    },

    /**
     * Cierra la sesión actual.
     */
    async logout() {
        await ApiService.post('/logout');
        localStorage.removeItem('auth_token');
    },

    /**
     * Obtiene el usuario autenticado actual.
     */
    async getUser() {
        if (!localStorage.getItem('auth_token')) return null;
        const response = await ApiService.get('/user');
        return response.data;
    },

    /**
     * Registra un nuevo usuario.
     * @param {Object} userData - { name, email, password, password_confirmation }
     */
    async register(userData) {
        const response = await ApiService.post('/register', userData);
        if (response.data.token) {
            localStorage.setItem('auth_token', response.data.token);
        }
        return response.data;
    }
};
