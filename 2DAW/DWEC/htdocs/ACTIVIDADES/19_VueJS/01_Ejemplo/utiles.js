var app = new Vue({
    el: '#div',
    data: {
        mensaje: 'Hola amiguitos',
        muestra: true,
        frutas: [{ 'nombre': 'manzana', 'stock': 50 }, { 'nombre': 'pera', 'stock': 30 }, { 'nombre': 'fresa', 'stock': 10 }],
        // frutas : ['manzana', 'fresa', 'pera'],
        nuevaFruta: '',
        nuevoStock: 0,
        sumarKilos: 0,
        fotos: ['images/fresa.png', 'images/manzana.png', 'images/platano.png', 'images/piña.png'],
        foto: 'images/fresa.png',
    },
    // almacena en cache y es reactivo //! esto no lo explico, explico el mounted
    computed: {
        // totalKilos() {
        //     return this.frutas.reduce((acumulado, fruta) => acumulado + Number(fruta.stock), 0);
        // },
    },
    mounted() {
        this.actualizaKilos();
    },
    methods: {
        cambiaFoto() {
            // sacar numero aleatorio
            let indice = Math.floor(Math.random() * this.fotos.length);
            this.foto = this.fotos[indice];
        },

        actualizaKilos() {
            total = 0;
            for (let fruta in this.frutas) {
                total += this.frutas[fruta].stock;
                // for (let i = 0; i < this.frutas.length; i++) {
                // total += this.frutas[i].stock;
            }
            this.sumarKilos = total;
        },


        cambia() {
            this.muestra = !this.muestra;
        },
        annadeFruta(nuevaFruta, nuevoStock) {
            //parseFloat para que el stock sea un numero y no un string
            this.frutas.push({ "nombre": nuevaFruta, "stock": parseFloat(nuevoStock) });
            this.nuevaFruta = "";
            this.nuevoStock = 0;
            // actualizamos el total cuando agregamos una fruta
            this.actualizaKilos();
        },
        // eliminandolo con el indexof
        eliminar(fruta) {
            this.frutas.splice(this.frutas.indexOf(fruta), 1);
        },
    }
})