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
            // .toggle() añade la clase si no existe o la quita si ya existe
            cuerpo.classList.toggle('dark-mode');
            
            // Cambiamos el icono y el color según el modo activo
            // .contains() comprueba si el elemento tiene una clase específica (devuelve true/false)
            if (cuerpo.classList.contains('dark-mode')) {
                // .replace() intercambia una clase por otra de forma atómica
                iconoTema.classList.replace('bi-moon-fill', 'bi-sun-fill');
                iconoTema.classList.replace('text-primary', 'text-warning');
            } else {
                iconoTema.classList.replace('bi-sun-fill', 'bi-moon-fill');
                iconoTema.classList.replace('text-warning', 'text-primary');
            }
        });
    }

    // 3. Inicializar Tooltips de Bootstrap (mensajes flotantes) 
    // .call() permite invocar un método (slice) sobre un objeto que no es un array (un NodeList)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    // .map() recorre el array y aplica una función a cada elemento (en este caso, inicializa el Tooltip)
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        // Tooltip es el componente de Bootstrap para mostrar mensajes emergentes
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
            // window representa la ventana del navegador; .location.href redirige a una nueva URL
            window.location.href = `mailto:${destinatario}?subject=${asunto}&body=${cuerpoEmail}`;
        });
    }

    // 5. Gráfico Canvas Decorativo (Sistema de partículas conectadas)
    const canvas = document.getElementById('canvasDecorativo');
    if (canvas) {
        // ctx (context) es la herramienta que usamos para dibujar en el canvas (el "pincel")
        const ctx = canvas.getContext('2d');
        let particles = [];
        const particleCount = 12;
        const maxDistance = 40;
        
        // Inicialización de partículas
        for(let i = 0; i < particleCount; i++) {
            // .push() añade un nuevo elemento (objeto partícula) al final de un array
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                radius: 2,
                vx: (Math.random() - 0.5) * 0.4,
                vy: (Math.random() - 0.5) * 0.4
            });
        }

        function animateCanvas() {
            // ctx.clearRect() borra los píxeles en un área rectangular (limpia el lienzo antes de redibujar)
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Obtener el color primario dinámicamente o usar el de la marca
            // getComputedStyle() obtiene los estilos CSS finales aplicados al elemento
            // .getPropertyValue() extrae el valor de una variable CSS específica
            const color = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim() || '#198754';
            
            for(let i = 0; i < particleCount; i++) {
                let p = particles[i];
                
                // Dibujar partícula
                // .beginPath() inicia un nuevo trazo o limpia el actual para empezar a dibujar
                ctx.beginPath();
                // .arc() dibuja un arco circular (x, y, radio, ángulo inicial, ángulo final)
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                // .fillStyle define el color o patrón para rellenar las formas
                ctx.fillStyle = color;
                // .fill() rellena el trazo actual con el color definido en fillStyle
                ctx.fill();
                
                // Mover partícula
                p.x += p.vx;
                p.y += p.vy;
                
                // Rebotes
                if(p.x < 0 || p.x > canvas.width) p.vx *= -1;
                if(p.y < 0 || p.y > canvas.height) p.vy *= -1;

                // Dibujar líneas de conexión
                for(let j = i + 1; j < particleCount; j++) {
                    let p2 = particles[j];
                    let dx = p.x - p2.x;
                    let dy = p.y - p2.y;
                    let dist = Math.sqrt(dx*dx + dy*dy);

                    if(dist < maxDistance) {
                        ctx.beginPath();
                        // .strokeStyle define el color para el contorno de las líneas
                        ctx.strokeStyle = color;
                        // .globalAlpha establece la transparencia de los dibujos (0 a 1)
                        ctx.globalAlpha = 1 - (dist / maxDistance);
                        // .lineWidth define el grosor de las líneas en píxeles
                        ctx.lineWidth = 0.5;
                        // .moveTo() mueve el "pincel" a unas coordenadas sin dibujar nada
                        ctx.moveTo(p.x, p.y);
                        // .lineTo() añade una línea recta desde la posición actual hasta las coordenadas indicadas
                        ctx.lineTo(p2.x, p2.y);
                        // .stroke() dibuja el contorno del trazo definido con moveTo y lineTo
                        ctx.stroke();
                        ctx.globalAlpha = 1;
                    }
                }
            }
            requestAnimationFrame(animateCanvas);
        }
        animateCanvas();
    }
});