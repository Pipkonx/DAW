// Ejercicio C
let valorCapturado = null;

// Capturar valor al hacer click en dígitos
document.querySelectorAll('.digitos td').forEach(celda => {
    celda.addEventListener('click', () => {
        valorCapturado = celda.textContent;
    });
});

// Asignar valor capturado al hacer click en celdas de valoración
document.querySelectorAll('.valoracion td').forEach(celda => {
    celda.addEventListener('click', () => {
        if (valorCapturado !== null) {
            celda.textContent = valorCapturado;
        }
    });
});