/**
 * Vista General de Pólizas (Orden Descendente por Fecha)
 */
const Polizas = {
    template: '#template-polizas',
    data() {
        return {
            items: [],
            loading: true
        };
    },
    async mounted() {
        this.items = await API.getPolizas();
        this.loading = false;
    },
    methods: {
        getStatusClass(estado) {
            switch (estado) {
                case 'Cobrada': return 'table-success';
                case 'A cuenta': return 'table-warning';
                case 'Liquidada': return 'table-primary';
                case 'Anulada': return 'table-danger';
                case 'Pre-anulada': return 'table-secondary';
                default: return '';
            }
        },
        getStatusBadge(estado) {
            switch (estado) {
                case 'Cobrada': return 'bg-success';
                case 'A cuenta': return 'bg-warning text-dark';
                case 'Liquidada': return 'bg-primary';
                case 'Anulada': return 'bg-danger';
                case 'Pre-anulada': return 'bg-secondary';
                default: return 'bg-dark';
            }
        },
        verCliente(id) {
            // Guardar cliente_id para posible filtrado/expansión en la vista de clientes
            sessionStorage.setItem('temp_cliente_id', id);
            this.$emit('cambiar-vista', 'Clientes');
        }
    }
};
