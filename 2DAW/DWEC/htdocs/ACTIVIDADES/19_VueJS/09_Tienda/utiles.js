var app = new Vue({
    el: '#div',
    data: {
        items: [],
    },
    // recordar que el mounted es para que cargue antes de cargar la vista
    mounted: function () {
        fetch('https://fakestoreapi.com/products')
            .then(response => response.json())
            .then(json => {
                this.items = json;
            });
    },
    methods: {
        eliminar: function(id) {
            //! usamos filter para que devuleva todos menos el que tenga el id
            // this.items = this.items.filter(item => item.id !==id);
            //! hacerlo sin filter seria con un for
            // for (let i = 0; i < this.items.length; i++) {
            //     if (this.items[i].id == id) {
            //         //splice es para eliminar 
            //         this.items.splice(i, 1);
            //     }
            // }

            //! ahora eliminando de la api
            fetch(`https://fakestoreapi.com/products/${id}`, {
                method: 'DELETE',
            })
            .then(response => response.json())
            .then(data => { console.log(data);
            });
        }
    }
});
