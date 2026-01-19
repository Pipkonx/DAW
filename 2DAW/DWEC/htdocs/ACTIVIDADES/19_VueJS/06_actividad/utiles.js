var app = new Vue({
    el: '#div',
    data: {
        items: ["Naruto", "Dragon ball", "Pokemon"],
        seleccionada: "",
        palabraSeleccionada: "",
    },
    methods: {
        annadir: function (event) {
            this.items.push(this.seleccionada);
        }
    }
})