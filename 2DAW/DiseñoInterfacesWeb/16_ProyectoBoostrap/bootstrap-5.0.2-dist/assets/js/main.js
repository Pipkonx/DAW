// ARCHIVO JAVASCRIPT PRINCIPAL PARA ECOCONNECT

document.addEventListener('DOMContentLoaded', () => {
    // 1. Poner el año actual automáticamente en el footer
    const yearSpan = document.getElementById('currentYear');
    if (yearSpan) {
        yearSpan.textContent = new Date().getFullYear();
    }

    // 2. Lógica para el Modo Oscuro / Claro
    const botonTema = document.getElementById('theme-toggle');
    const iconoTema = document.getElementById('theme-icon');
    const cuerpo = document.body;

    if (botonTema && iconoTema) {
        botonTema.addEventListener('click', () => {
            // Alternamos la clase dark-mode
            cuerpo.classList.toggle('dark-mode');
            
            // Cambiamos el icono y el color según el modo activo
            if (cuerpo.classList.contains('dark-mode')) {
                iconoTema.classList.replace('bi-moon-fill', 'bi-sun-fill');
                iconoTema.classList.replace('text-primary', 'text-warning');
            } else {
                iconoTema.classList.replace('bi-sun-fill', 'bi-moon-fill');
                iconoTema.classList.replace('text-warning', 'text-primary');
            }
        });
    }

    // 3. Inicializar Tooltips de Bootstrap (mensajes flotantes)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // 4. Lógica para el Formulario de Contacto (Mailto)
    const formulario = document.getElementById('formularioContacto');
    
    if (formulario) {
        formulario.addEventListener('submit', (e) => {
            e.preventDefault(); // Evitamos que la página se recargue
            
            // Capturamos los datos del formulario
            const nombre = document.getElementById('nombreForm').value;
            const email = document.getElementById('emailForm').value;
            const mensaje = document.getElementById('mensajeForm').value;
            
            // Configuramos los parámetros del email
            const destinatario = 'corderorafa0@gmail.com';
            const asunto = `Nuevo mensaje de EcoConnect de ${nombre}`;
            const cuerpoEmail = `Nombre: ${nombre}%0DEmail: ${email}%0DMensaje: ${mensaje}`;
            
            // Abrimos el cliente de correo predeterminado
            window.location.href = `mailto:${destinatario}?subject=${asunto}&body=${cuerpoEmail}`;
        });
    }
});