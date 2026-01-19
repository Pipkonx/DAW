var app = new Vue ({
    el: '#div',
    data: {
        num1: 0,
        num2:0,
    },
    methods: {
        sumar:function(){
            return this.num1 + this.num2
        },

        restar:function() {
            return this.num1 - this.num2
        }
    }
})