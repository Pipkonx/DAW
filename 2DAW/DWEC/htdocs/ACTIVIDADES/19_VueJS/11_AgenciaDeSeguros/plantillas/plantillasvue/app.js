// Extraemos la función createApp de la librería Vue global
const { createApp } = Vue;

createApp({
    // 'data' contiene todas las variables reactivas. Si estas cambian, el HTML se actualiza solo.
    data() {
        return {
            listaDatos: [],         // Array para guardar lo que devuelve la base de datos (SELECT)
            verFormulario: false,   // Controla si se muestra u oculta el HTML del formulario (v-if)
            formulario: {           // Objeto enlazado a los inputs del formulario (v-model)
                id: null,
                nombre: '',
                email: ''
            }
        }
    },
    // 'mounted' se ejecuta automáticamente cuando la página termina de cargar
    mounted() {
        this.leerDatos(); // Cargamos los datos iniciales de la tabla
    },
    // 'methods' contiene todas las funciones (métodos) de nuestra aplicación
    methods: {
        // --- READ: Leer (SELECT) ---
        async leerDatos() {
            try {
                // Axios hace un GET al archivo PHP. 'await' espera a que PHP responda.
                const respuesta = await axios.get('../plantillaphp/leer.php');
                // Guardamos el JSON devuelto en la variable 'listaDatos'
                this.listaDatos = respuesta.data;
            } catch (error) {
                console.error("Error al cargar los datos:", error);
            }
        },

        // --- Utilidad para abrir la tarjeta de creación/edición ---
        abrirFormulario(item = null) {
            this.verFormulario = true; // Mostramos el div del formulario
            
            if (item) {
                // Si recibe un 'item' (clic en Editar), copiamos sus datos al formulario
                // Usamos {...item} para hacer una copia y no editar la fila de la tabla en vivo
                this.formulario = { ...item };
            } else {
                // Si NO recibe item (clic en Nuevo), vaciamos el formulario
                this.formulario = { id: null, nombre: '', email: '' };
            }
        },

        // --- Utilidad para ocultar la tarjeta de creación/edición ---
        cerrarFormulario() {
            this.verFormulario = false;
        },

        // --- CREATE y UPDATE: Guardar datos ---
        async guardarRegistro() {
            try {
                if (this.formulario.id) {
                    // Si el objeto tiene un 'id', significa que ya existe en la BD -> ACTUALIZAR
                    await axios.post('../plantillaphp/actualizar.php', this.formulario);
                } else {
                    // Si el objeto NO tiene 'id', es un registro nuevo -> CREAR
                    await axios.post('../plantillaphp/crear.php', this.formulario);
                }
                
                // Después de guardar, cerramos la tarjeta y volvemos a pedir los datos a la BD
                this.cerrarFormulario();
                this.leerDatos(); 
                
            } catch (error) {
                console.error("Error al guardar:", error);
            }
        },

        // --- DELETE: Eliminar un registro ---
        async eliminarRegistro(id) {
            // Confirmación básica nativa del navegador
            if (confirm("¿Estás seguro de que quieres eliminar este registro?")) {
                try {
                    // Enviamos por POST el ID del registro que queremos borrar
                    await axios.post('../plantillaphp/borrar.php', { id: id });
                    
                    // Recargamos la tabla para ver los cambios
                    this.leerDatos(); 
                } catch (error) {
                    console.error("Error al eliminar:", error);
                }
            }
        }
    }
}).mount('#app'); // Montamos la app en el div con id="app"
