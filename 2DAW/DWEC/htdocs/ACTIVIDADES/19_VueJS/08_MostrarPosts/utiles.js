var app = new Vue({
    el: '#div',
    data: {
        items: [],
    },
    // recordar que el mounted es para que cargue antes de cargar la vista
    mounted: function () {
        fetch('https://jsonplaceholder.typicode.com/posts')
            .then(response => response.json())
            .then(json => {
                this.items = json;
            });
    },
});
