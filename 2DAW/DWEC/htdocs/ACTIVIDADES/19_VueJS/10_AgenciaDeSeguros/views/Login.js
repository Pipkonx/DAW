/**
 * Vista de Login
 */
const Login = {
    template: '#template-login',
    data() {
        return {
            username: '',
            password: '',
            loading: false
        };
    },
    methods: {
        async submitLogin() {
            this.loading = true;
            const result = await API.login(this.username, this.password);
            this.loading = false;

            if (result.success) {
                this.$emit('login-success', result.user);
                Swal.fire({
                    icon: 'success',
                    title: '¡Bienvenido!',
                    text: `Hola, ${result.user.username}`,
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', result.message, 'error');
            }
        }
    }
};
