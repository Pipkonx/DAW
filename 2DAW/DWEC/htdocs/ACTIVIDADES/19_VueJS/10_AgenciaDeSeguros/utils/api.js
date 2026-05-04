/**
 * Abstracción de llamadas a API (Full Stack PHP + MySQL)
 */
const API = {
    url: 'api.php',

    async login(username, password) {
        try {
            const response = await fetch(`${this.url}?action=login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });
            return await response.json();
        } catch (error) {
            this.handleError(error);
            return { success: false };
        }
    },

    async getClientes() {
        return this.fetchWrapper(`${this.url}?action=clientes`);
    },

    async getPolizas(clienteId = null) {
        const query = clienteId ? `&cliente_id=${clienteId}` : '';
        return this.fetchWrapper(`${this.url}?action=polizas${query}`);
    },

    async getPagos(polizaId) {
        return this.fetchWrapper(`${this.url}?action=pagos&poliza_id=${polizaId}`);
    },

    async crearCliente(data) {
        return this.postWrapper(`${this.url}?action=crear_cliente`, data);
    },

    async crearPoliza(data) {
        return this.postWrapper(`${this.url}?action=crear_poliza`, data);
    },

    async editarCliente(data) {
        return this.postWrapper(`${this.url}?action=editar_cliente`, data);
    },

    async crearPago(data) {
        return this.postWrapper(`${this.url}?action=crear_pago`, data);
    },

    async getProvincias() {
        return this.fetchWrapper(`${this.url}?action=provincias`);
    },

    async getMunicipios(provinciaId) {
        return this.fetchWrapper(`${this.url}?action=municipios&id_provincia=${provinciaId}`);
    },

    async eliminarCliente(id) {
        try {
            const response = await fetch(`${this.url}?action=cliente&id=${id}`, { method: 'DELETE' });
            return await response.json();
        } catch (error) {
            this.handleError(error);
            return { success: false };
        }
    },

    // Helpers internos
    async fetchWrapper(url) {
        try {
            const r = await fetch(url);
            const data = await r.json();
            if (data.error) throw new Error(data.error);
            return data;
        } catch (e) {
            this.handleError(e);
            return [];
        }
    },

    async postWrapper(url, data) {
        try {
            const r = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            return await r.json();
        } catch (e) {
            this.handleError(e);
            return { success: false };
        }
    },

    handleError(error) {
        console.error('API Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: error.message
        });
    }
};
