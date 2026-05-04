/**
 * Vista de Detalle de Póliza (Pagos / Recibos)
 */
const DetallePoliza = {
    template: '#template-detalle-poliza',
    data() {
        return {
            poliza: {},
            pagos: [],
            nuevoPago: {
                fecha: new Date().toISOString().split('T')[0],
                importe: 0
            },
            loading: true
        };
    },
    computed: {
        totalPagado() {
            return this.pagos.reduce((acc, p) => acc + parseFloat(p.importe), 0);
        },
        pendiente() {
            return this.poliza.importe - this.totalPagado;
        }
    },
    async mounted() {
        const data = sessionStorage.getItem('temp_poliza');
        if (!data) {
            this.$emit('cambiar-vista', 'Polizas');
            return;
        }
        this.poliza = JSON.parse(data);
        await this.cargarPagos();
    },
    methods: {
        async cargarPagos() {
            this.loading = true;
            this.pagos = await API.getPagos(this.poliza.id);
            this.loading = false;
        },
        async guardarPago() {
            if (this.nuevoPago.importe <= 0) return;
            
            // Validación de importe máximo
            if (this.nuevoPago.importe > this.pendiente) {
                Swal.fire('Error', 'El importe supera el total de la póliza', 'error');
                return;
            }

            const res = await API.crearPago({
                poliza_id: this.poliza.id,
                ...this.nuevoPago
            });

            if (res.success) {
                Swal.fire('¡Éxito!', 'Pago registrado', 'success');
                this.nuevoPago.importe = 0;
                await this.cargarPagos();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }
    }
};
