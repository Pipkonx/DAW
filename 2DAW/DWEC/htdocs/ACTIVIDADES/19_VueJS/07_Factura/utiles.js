var app = new Vue({
    el: '#div',
    data: {
        items: [],
        cod: "",
        descripcion: "",
        cantidad: 0,
        precio: 0,
    },
    methods: {
        annadir: function (event) {
            this.items.push({
                "cod": this.cod,
                "descripcion": this.descripcion,
                "cantidad": this.cantidad,
                "precio": this.precio,
                "subtotal": this.cantidad * this.precio
            });
        }

    }
})