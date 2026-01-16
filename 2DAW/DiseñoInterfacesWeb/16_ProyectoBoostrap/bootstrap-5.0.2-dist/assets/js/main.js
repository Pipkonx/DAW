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

    // 5. Gráfico Canvas Decorativo (Sistema de partículas conectadas)
    const canvas = document.getElementById('canvasDecorativo');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let particles = [];
        const particleCount = 12;
        const maxDistance = 40;
        
        // Inicialización de partículas
        for(let i = 0; i < particleCount; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                radius: 2,
                vx: (Math.random() - 0.5) * 0.4,
                vy: (Math.random() - 0.5) * 0.4
            });
        }

        function animateCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Obtener el color primario dinámicamente o usar el de la marca
            const color = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim() || '#198754';
            
            for(let i = 0; i < particleCount; i++) {
                let p = particles[i];
                
                // Dibujar partícula
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fillStyle = color;
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
                        ctx.strokeStyle = color;
                        ctx.globalAlpha = 1 - (dist / maxDistance);
                        ctx.lineWidth = 0.5;
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p2.x, p2.y);
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