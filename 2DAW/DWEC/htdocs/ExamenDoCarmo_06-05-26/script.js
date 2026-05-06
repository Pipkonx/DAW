new Vue({
    // el (element): Le dice a Vue qué parte del HTML va a "vigilar" y controlar (el <div> con id="app")
    el: "#app",
    
    // data: El almacén de estado. Aquí declaramos TODAS las variables que usaremos en el HTML (con v-model, {{ }}, v-if)
    data: {
        categorias: [], // Array que rellenaremos con las categorías que mande PHP
        productos: [],  // Array que rellenaremos con los productos que mande PHP
        catSel: null,   // Guardará la categoría seleccionada por el usuario al hacer clic en la tabla
        // formP: Objeto que almacena lo que el usuario escribe en los inputs. Su 'id' será null si estamos creando algo nuevo
        formP: { id: null, nombre: "", precio: 0 }, 
        editandoP: false // Bandera para saber en qué modo estamos: false = Creando, true = Editando
    },
    
    // mounted(): Función de ciclo de vida. Se ejecuta AUTOMÁTICAMENTE una vez cuando se carga la página por primera vez
    mounted() {
        this.listarCats(); // Arrancamos pidiendo las categorías a la BD nada más entrar
    },
    
    // methods: El cajón de las funciones. Todo lo que pongas en los @click del HTML debe existir aquí dentro.
    methods: {
        async listarCats() {
            // axios.get: Pide datos al servidor (api.php) de fondo, sin recargar la página web.
            // await: Le dice a JS "párate y espera a que el servidor conteste" antes de seguir a la línea de abajo.
            const r = await axios.get("api.php?accion=categorias");
            this.categorias = r.data; // Guardamos el JSON que nos manda PHP directamente en nuestra variable de Vue
        },
        
        async seleccionarCat(c) {
            this.catSel = c; // Guardamos en memoria la categoría (c) que el usuario acaba de hacer clic
            this.cancelarP(); // Limpiamos el formulario (por si estaba editando algo de otra categoría)
            this.listarProds(); // Pedimos a PHP los productos pertenecientes a esta categoría
        },
        
        async listarProds() {
            // Hacemos una petición GET a api.php y le mandamos pegado en la URL el ID de la categoría (catSel.id)
            const r = await axios.get("api.php?accion=productos&cat_id=" + this.catSel.id);
            this.productos = r.data; // Actualizamos la tabla de productos
        },
        
        prepararP(p) {
            // El {...p} (Spread Operator) clona el producto seleccionado. Si no lo clonáramos, 
            // como Vue es reactivo, al escribir en el input se cambiaría el nombre en la tabla ¡incluso antes de guardar!
            this.formP = { ...p }; 
            this.editandoP = true; // Cambiamos el semáforo a "modo edición", así el botón dice "Actualizar" y sale el "Cancelar"
        },
        
        cancelarP() {
            // Limpia el formulario poniéndolo en blanco y quita el modo edición
            this.formP = { id: null, nombre: "", precio: 0 };
            this.editandoP = false;
        },
        
        async guardarP() {
            // Comprobamos la bandera para saber qué operación hacer en la Base de Datos
            if (this.editandoP) {
                // PUT: Método HTTP usado por convención para ACTUALIZAR. Mandamos el ID por la URL, y el objeto (this.formP) va escondido en el cuerpo (body) de la petición.
                await axios.put("api.php?accion=productos&id=" + this.formP.id, this.formP);
            } else {
                // POST: Método HTTP usado por convención para CREAR. Mandamos el cat_id en la URL y los datos en el body.
                await axios.post("api.php?accion=productos&cat_id=" + this.catSel.id, this.formP);
            }
            this.cancelarP(); // Tras mandar los datos al servidor, limpiamos los campos de texto
            this.listarProds(); // y recargamos la lista haciendo otra petición al servidor para ver los cambios actualizados
        },
        
        async borrarP(id) {
            // confirm() saca una ventanita de alerta nativa del navegador (Aceptar / Cancelar)
            if (confirm('¿Borrar producto?')) {
                // DELETE: Método HTTP para BORRAR. Le decimos a PHP qué ID concreto destruir.
                await axios.delete("api.php?accion=productos&id=" + id);
                this.listarProds(); // Refrescamos la lista para que desaparezca visualmente
            }
        },
    },
});
