var app = new Vue({
    el: '#div',
    data: {
        jugador1: 0,
        jugador2: 0,
        ganador: "",
        contadorJugador1:0,
        contadorJugador2:0,
        contadorEmpates:0,
    },  
    methods: {
        jugar: function(event) {
            this.jugador1 = Math.floor(Math.random() * 100)
            this.jugador2 = Math.floor(Math.random() * 100)

            if (this.jugador1 > this.jugador2) {
                this.ganador = 'Jugador1'
                this.contadorJugador1++
            } else if (this.jugador1 < this.jugador2) {
                this.ganador = 'Jugador2'
                this.contadorJugador2++
            } else {
                this.ganador = 'Empate'
                this.contadorEmpates++
            }
        }
    }
})