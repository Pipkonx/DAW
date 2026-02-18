<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Anteproyecto Fin de Grado - FintechPro</title>
    <style>
        @page { margin: 0px; }
        body { margin: 0px; font-family: 'Helvetica', 'Arial', sans-serif; color: #334155; line-height: 1.6; }
        .page-break { page-break-after: always; }
        
        /* Cover */
        .cover { 
            height: 100vh; 
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); 
            color: white; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            text-align: center; 
            padding: 60px; 
            position: relative; 
        }
        .logo-text { font-size: 64px; font-weight: 800; margin-bottom: 10px; letter-spacing: -1px; }
        .logo-sub { font-size: 24px; color: #94a3b8; font-weight: 300; margin-bottom: 60px; letter-spacing: 2px; text-transform: uppercase; }
        
        .cover h1 { font-size: 36px; margin-bottom: 20px; font-weight: bold; }
        .cover h2 { font-size: 20px; font-weight: normal; margin-bottom: 50px; opacity: 0.8; }
        
        .meta-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 12px;
            max-width: 500px;
            margin: 0 auto;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .meta p { margin: 8px 0; font-size: 16px; }
        
        /* Content Pages */
        .content { padding: 50px 60px; }
        
        .header { 
            border-bottom: 2px solid #e2e8f0; 
            padding-bottom: 15px; 
            margin-bottom: 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .header h3 { color: #64748b; margin: 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .header span { color: #cbd5e1; font-size: 12px; }
        
        h1.section-title { 
            color: #0f172a; 
            font-size: 32px; 
            font-weight: 800;
            margin-bottom: 30px; 
            position: relative;
        }
        h1.section-title:after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: #3b82f6;
            margin-top: 10px;
            border-radius: 2px;
        }

        h2 { color: #1e293b; font-size: 22px; margin-top: 30px; margin-bottom: 15px; font-weight: 700; border-left: 4px solid #3b82f6; padding-left: 10px; }
        h3 { color: #334155; font-size: 18px; margin-top: 20px; margin-bottom: 10px; font-weight: 600; }
        
        p { margin-bottom: 15px; text-align: justify; color: #475569; font-size: 14px; }
        ul { margin-bottom: 20px; padding-left: 20px; }
        li { margin-bottom: 8px; color: #475569; font-size: 14px; }
        
        /* Tech Stack Cards */
        .tech-grid { display: table; width: 100%; border-spacing: 10px; margin-top: 20px; }
        .tech-card { 
            display: table-cell; 
            width: 33%; 
            background: #f8fafc; 
            border: 1px solid #e2e8f0; 
            padding: 15px; 
            border-radius: 8px; 
            vertical-align: top; 
        }
        .tech-title { font-weight: bold; color: #0f172a; margin-bottom: 5px; display: block; font-size: 14px; }
        .tech-desc { font-size: 11px; color: #64748b; }

        /* Tables */
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin: 20px 0; font-size: 13px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
        th { background-color: #f1f5f9; color: #334155; padding: 12px; text-align: left; font-weight: 600; border-bottom: 1px solid #e2e8f0; }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; color: #475569; vertical-align: top; }
        tr:last-child td { border-bottom: none; }
        
        .footer { 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            right: 0; 
            height: 40px; 
            background-color: #fff; 
            text-align: center; 
            line-height: 40px; 
            font-size: 10px; 
            color: #cbd5e1; 
            border-top: 1px solid #f1f5f9; 
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        .bg-green { background-color: #10b981; }
        .bg-blue { background-color: #3b82f6; }
        .bg-amber { background-color: #f59e0b; }
    </style>
</head>
<body>

    <!-- Cover Page -->
    <div class="cover">
        <div class="logo-text">FintechPro</div>
        <div class="logo-sub">Gestión Financiera Inteligente</div>
        
        <h1>Anteproyecto Fin de Grado</h1>
        <h2>Desarrollo de Aplicación SPA para Gestión de Patrimonio</h2>
        
        <div class="meta-box">
            <div class="meta">
                <p><strong>Autor:</strong> {{ $author }}</p>
                <p><strong>Fecha:</strong> {{ $date }}</p>
                <p><strong>Ciclo:</strong> Desarrollo de Aplicaciones Web</p>
                <p><strong>Curso Académico:</strong> 2025/2026</p>
            </div>
        </div>
    </div>
    
    <div class="page-break"></div>

    <!-- Page 1: Introducción y Justificación -->
    <div class="content">
        <div class="header">
            <h3>1. Introducción y Contexto</h3>
            <span>01</span>
        </div>
        
        <h1 class="section-title">1. Resumen del Proyecto</h1>
        <p>
            <strong>FintechPro</strong> es una aplicación web de tipo Single Page Application (SPA) diseñada para centralizar y simplificar la gestión de las finanzas personales. El proyecto nace de la necesidad de unificar el control de ingresos, gastos, ahorros e inversiones complejas (acciones, criptomonedas, fondos) en una sola interfaz intuitiva y moderna.
        </p>
        <p>
            En un entorno económico cada vez más digitalizado, los usuarios se enfrentan a la fragmentación de su información financiera. FintechPro ofrece una solución integral para visualizar el patrimonio neto ("Net Worth") en tiempo real, facilitando la toma de decisiones informadas.
        </p>
        
        <h2>Justificación</h2>
        <p>
            La gestión financiera personal suele realizarse mediante hojas de cálculo complejas y propensas a errores, o a través de aplicaciones bancarias que no ofrecen una visión global de activos externos. FintechPro resuelve esta problemática mediante:
        </p>
        <ul>
            <li><strong>Centralización de Datos:</strong> Agregación de múltiples fuentes de información financiera.</li>
            <li><strong>Automatización de Cálculos:</strong> Eliminación de errores manuales en el cálculo de rendimientos y balances.</li>
            <li><strong>Accesibilidad:</strong> Interfaz web adaptable a cualquier dispositivo, disponible 24/7.</li>
            <li><strong>Privacidad:</strong> Al ser una solución auto-hospedable o privada, los datos sensibles no se comparten con terceros con fines publicitarios.</li>
        </ul>

        <h2>Ámbito del Proyecto</h2>
        <p>
            El sistema está dirigido a usuarios particulares con conocimientos medios de tecnología que deseen tener un control exhaustivo sobre sus finanzas, así como a pequeños inversores que necesiten un seguimiento detallado de su cartera de activos.
        </p>
    </div>

    <div class="page-break"></div>

    <!-- Page 2: Objetivos -->
    <div class="content">
        <div class="header">
            <h3>2. Objetivos</h3>
            <span>02</span>
        </div>
        
        <h1 class="section-title">2. Objetivos del Proyecto</h1>

        <h2>Objetivo General</h2>
        <p>
            Desarrollar una aplicación web completa, segura y escalable que permita a los usuarios gestionar su economía personal de manera integral, proporcionando herramientas de análisis financiero profesional adaptadas al uso doméstico.
        </p>

        <h2>Objetivos Específicos</h2>
        <ul>
            <li><strong>Gestión de Transacciones:</strong> Implementar un sistema CRUD (Create, Read, Update, Delete) eficiente para registrar ingresos y gastos categorizados.</li>
            <li><strong>Control de Inversiones:</strong> Desarrollar un módulo para el seguimiento de activos financieros que calcule automáticamente el precio medio de compra y la rentabilidad actual.</li>
            <li><strong>Visualización de Datos:</strong> Integrar gráficos interactivos que muestren la evolución del patrimonio y la distribución de gastos por categoría.</li>
            <li><strong>Experiencia de Usuario (UX):</strong> Diseñar una interfaz moderna y reactiva utilizando tecnologías SPA para minimizar los tiempos de carga.</li>
            <li><strong>Seguridad:</strong> Garantizar la protección de datos sensibles mediante autenticación robusta y cifrado.</li>
        </ul>

        <h2>Resultados Esperados</h2>
        <p>
            Al finalizar el proyecto, se espera disponer de una aplicación funcional desplegada en un entorno de producción, con documentación completa de uso y mantenimiento, y un código fuente modular que permita futuras ampliaciones.
        </p>
    </div>

    <div class="page-break"></div>

    <!-- Page 3: Planificación -->
    <div class="content">
        <div class="header">
            <h3>3. Metodología y Planificación</h3>
            <span>03</span>
        </div>
        
        <h1 class="section-title">3. Planificación</h1>

        <h2>Metodología de Desarrollo</h2>
        <p>
            Se empleará una metodología ágil basada en <strong>Scrum</strong>, con iteraciones cortas (Sprints) de 2 semanas. Esto permitirá una adaptación flexible a nuevos requisitos y una entrega continua de valor.
        </p>

        <h3>Diseño de Interfaz: Wireframes</h3>
        <p>
            Antes de comenzar la programación, se diseñaron <strong>Wireframes</strong>. Estos son esquemas visuales de baja fidelidad que representan la estructura esquelética de la interfaz. Su uso permitió definir la disposición de elementos clave (menús, gráficos, formularios) y validar la Experiencia de Usuario (UX) sin distraerse con detalles estéticos como colores o tipografías.
        </p>

        <h2>Fases del Proyecto</h2>
        <table>
            <tr>
                <th>Fase</th>
                <th>Duración</th>
                <th>Actividades Principales</th>
            </tr>
            <tr>
                <td><strong>1. Análisis</strong></td>
                <td>2 Semanas</td>
                <td>Definición de requisitos, diseño de base de datos, maquetación (Wireframes).</td>
            </tr>
            <tr>
                <td><strong>2. Desarrollo Backend</strong></td>
                <td>3 Semanas</td>
                <td>Configuración Laravel, Migraciones, Modelos, API RESTful, Autenticación.</td>
            </tr>
            <tr>
                <td><strong>3. Desarrollo Frontend</strong></td>
                <td>4 Semanas</td>
                <td>Componentes Vue, Integración con Inertia, Gráficos, Estilado Tailwind.</td>
            </tr>
            <tr>
                <td><strong>4. Pruebas y Despliegue</strong></td>
                <td>2 Semanas</td>
                <td>Tests unitarios, corrección de bugs, optimización, documentación final.</td>
            </tr>
        </table>

        <h2>Recursos Necesarios</h2>
        <ul>
            <li><strong>Hardware:</strong> Equipo de desarrollo estándar.</li>
            <li><strong>Software:</strong> Trae IDE, VS Code, Docker, Git, Composer, NPM.</li>
            <li><strong>Servicios:</strong> GitHub (Control de versiones), Servidor VPS (Despliegue).</li>
        </ul>

        <h3>Desarrollo Aumentado por IA (Trae IDE + Gemini)</h3>
        <p>
            Un pilar fundamental en la ejecución de este proyecto ha sido la utilización del IDE de nueva generación <strong>Trae</strong>, impulsado por el modelo de inteligencia artificial <strong>Gemini 3-Flash-Preview</strong>. Esta herramienta no solo ha acelerado la escritura de código, sino que ha redefinido el flujo de trabajo:
        </p>
        <ul>
            <li><strong>De "Picador" a Arquitecto:</strong> La capacidad de la IA para generar estructuras de código complejas permitió que el rol del desarrollador evolucionara. En lugar de centrarse únicamente en la sintaxis ("picar código"), el esfuerzo se redirigió hacia la <strong>comprensión profunda de la arquitectura</strong>, la depuración lógica y la revisión de calidad, garantizando un acabado profesional.</li>
            <li><strong>Pruebas Automatizadas en Navegador:</strong> Se integraron herramientas avanzadas del ecosistema Google (incluyendo funciones experimentales como "Google Antigravity") para realizar pruebas automáticas de comportamiento sobre la interfaz, permitiendo detectar y corregir errores visuales de forma ágil.</li>
        </ul>
    </div>

    <div class="page-break"></div>

    <!-- Page 4: Requisitos -->
    <div class="content">
        <div class="header">
            <h3>4. Análisis de Requisitos</h3>
            <span>04</span>
        </div>
        
        <h1 class="section-title">4. Requisitos del Sistema</h1>

        <h2>Requisitos Funcionales</h2>
        <table>
            <tr>
                <th width="20%">ID</th>
                <th>Requisito</th>
                <th>Prioridad</th>
            </tr>
            <tr>
                <td><strong>RF-01</strong></td>
                <td>El sistema debe permitir el registro, inicio de sesión y recuperación de contraseña (Auth).</td>
                <td><span class="status-badge bg-green">Alta</span></td>
            </tr>
            <tr>
                <td><strong>RF-02</strong></td>
                <td>El usuario podrá crear, editar y eliminar transacciones financieras.</td>
                <td><span class="status-badge bg-green">Alta</span></td>
            </tr>
            <tr>
                <td><strong>RF-03</strong></td>
                <td>El sistema mostrará un Dashboard con resumen financiero y gráficos.</td>
                <td><span class="status-badge bg-blue">Media</span></td>
            </tr>
            <tr>
                <td><strong>RF-04</strong></td>
                <td>El sistema permitirá gestionar una cartera de inversiones (Activos).</td>
                <td><span class="status-badge bg-blue">Media</span></td>
            </tr>
             <tr>
                <td><strong>RF-05</strong></td>
                <td>Generación de reportes PDF exportables del estado financiero.</td>
                <td><span class="status-badge bg-amber">Baja</span></td>
            </tr>
        </table>

        <h2>Requisitos No Funcionales</h2>
        <ul>
            <li><strong>Rendimiento:</strong> La carga inicial de la aplicación no debe superar los 2 segundos.</li>
            <li><strong>Seguridad:</strong> Las contraseñas deben almacenarse hashadas (Bcrypt). Uso de tokens CSRF.</li>
            <li><strong>Usabilidad:</strong> Diseño Responsive adaptado a Móvil, Tablet y Desktop.</li>
            <li><strong>Mantenibilidad:</strong> Código comentado y estructurado según el patrón MVC.</li>
        </ul>
    </div>

    <div class="page-break"></div>

    <!-- Page 5: Arquitectura Técnica -->
    <div class="content">
        <div class="header">
            <h3>5. Arquitectura Técnica</h3>
            <span>05</span>
        </div>
        
        <h1 class="section-title">5. Stack Tecnológico</h1>
        <p>
            La elección tecnológica prioriza el rendimiento, la seguridad y la velocidad de desarrollo, utilizando un stack moderno y ampliamente soportado por la comunidad.
        </p>
        
        <div class="tech-grid">
            <div class="tech-card">
                <span class="tech-title">Backend: Laravel 11</span>
                <span class="tech-desc">Framework PHP robusto. Provee Eloquent ORM, Routing avanzado, Queues y seguridad integrada.</span>
            </div>
            <div class="tech-card">
                <span class="tech-title">Frontend: Vue 3</span>
                <span class="tech-desc">Composition API para lógica reactiva modular. Single File Components (SFC) para mantenibilidad.</span>
            </div>
            <div class="tech-card">
                <span class="tech-title">Glue: Inertia.js</span>
                <span class="tech-desc">Conecta Laravel y Vue sin necesidad de una API compleja, permitiendo el desarrollo de SPA monolítica.</span>
            </div>
        </div>

        <h3>API RESTful</h3>
        <p>
            Aunque Inertia.js simplifica la comunicación, se ha diseñado una arquitectura basada en los principios de <strong>API RESTful</strong> (Representational State Transfer). Esto significa que los recursos del servidor (Transacciones, Activos) se exponen a través de verbos HTTP estándar (GET para leer, POST para crear, PUT para actualizar, DELETE para borrar). Esta estructura facilita la escalabilidad y permite que en el futuro otros clientes (como una App Móvil) consuman los mismos datos.
        </p>

        <div class="tech-grid">
            <div class="tech-card">
                <span class="tech-title">BD: MySQL / SQLite</span>
                <span class="tech-desc">Almacenamiento relacional de datos. SQLite para desarrollo y MySQL para producción.</span>
            </div>
            <div class="tech-card">
                <span class="tech-title">Estilos: Tailwind CSS</span>
                <span class="tech-desc">Framework utility-first para diseño rápido y consistente.</span>
            </div>
            <div class="tech-card">
                <span class="tech-title">Tools: Vite</span>
                <span class="tech-desc">Build tool de próxima generación para un entorno de desarrollo ultrarrápido.</span>
            </div>
        </div>

        <h2>Modelo de Datos</h2>
        <p>El esquema de base de datos relacional incluye las siguientes entidades clave:</p>
        <table>
            <tr>
                <th>Entidad</th>
                <th>Descripción</th>
            </tr>
            <tr>
                <td><strong>Users</strong></td>
                <td>Almacena credenciales y configuración de perfil.</td>
            </tr>
            <tr>
                <td><strong>Transactions</strong></td>
                <td>Registro central de movimientos (amount, type, category, date).</td>
            </tr>
            <tr>
                <td><strong>Assets</strong></td>
                <td>Instrumentos de inversión (ticker, name, quantity, avg_price).</td>
            </tr>
            <tr>
                <td><strong>Categories</strong></td>
                <td>Clasificación de gastos e ingresos para reportes.</td>
            </tr>
        </table>

        <h2>Librerías y Dependencias Clave</h2>
        <p>El proyecto integra diversas librerías externas que potencian su funcionalidad sin reinventar la rueda.</p>

        <h3>Backend (PHP / Composer)</h3>
        <ul>
            <li><strong>laravel/socialite:</strong> Gestiona la autenticación OAuth con Google, simplificando el flujo de login seguro.</li>
            <li><strong>guzzlehttp/guzzle:</strong> Cliente HTTP utilizado para consumir las APIs financieras externas.</li>
            <li><strong>symfony/dom-crawler:</strong> Componente esencial para realizar Web Scraping (parseo de HTML) de sitios como Morningstar.</li>
            <li><strong>smalot/pdfparser:</strong> Librería independiente para extraer texto plano de archivos PDF (facturas/extractos).</li>
            <li><strong>barryvdh/laravel-dompdf:</strong> Wrapper de DomPDF para generar reportes PDF desde vistas Blade.</li>
        </ul>

        <h3>Frontend (JS / NPM)</h3>
        <ul>
            <li><strong>chart.js:</strong> Motor de renderizado de gráficos en Canvas HTML5.</li>
            <li><strong>vue-chartjs:</strong> Wrapper para integrar Chart.js en Vue 3.</li>
            <li><strong>axios:</strong> Cliente HTTP basado en promesas para realizar peticiones asíncronas al servidor.</li>
            <li><strong>tailwindcss:</strong> Framework CSS de bajo nivel para diseño rápido y responsive.</li>
        </ul>

        <div style="background-color: #f8fafc; padding: 15px; border-left: 4px solid #3b82f6; margin-top: 20px;">
            <strong>Nota Técnica: ¿Qué es un Wrapper?</strong>
            <p style="margin: 5px 0 0 0; font-size: 13px;">
                Un <strong>Wrapper</strong> (o envoltorio) es un patrón de diseño que adapta una librería o API existente para que sea más fácil de usar en un entorno específico. 
                Por ejemplo, <em>vue-chartjs</em> "envuelve" la librería <em>Chart.js</em> (que es agnóstica al framework) y expone sus funcionalidades como Componentes Vue reactivos. 
                Esto nos permite usar <code>&lt;BarChart :data="misDatos" /&gt;</code> en lugar de escribir código imperativo complejo para manipular el DOM directamente.
            </p>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- Page 6: Desafíos Técnicos -->
    <div class="content">
        <div class="header">
            <h3>6. Implementación Técnica Detallada</h3>
            <span>06</span>
        </div>
        
        <h1 class="section-title">6. Desafíos Técnicos</h1>
        <p>
            El desarrollo de FintechPro ha conllevado la resolución de retos técnicos complejos, especialmente en lo referente a la integración de servicios externos y el procesamiento de datos no estructurados.
        </p>

        <h3>6.1. Digitalización de Activos vía OCR</h3>
        <p>
            Una de las funcionalidades más costosas a nivel de implementación ha sido el sistema de reconocimiento óptico de caracteres (OCR) para importar carteras de inversión a partir de capturas de pantalla o documentos PDF.
        </p>
        <ul>
            <li><strong>Tecnología:</strong> Se ha integrado la <strong>API de OCR Space</strong> para el procesamiento de imágenes (PNG/JPG) y la librería <strong>Smalot PdfParser</strong> para archivos PDF nativos.</li>
            <li><strong>Desafío:</strong> La API devuelve texto "crudo" sin estructura. El reto principal fue desarrollar un algoritmo de <strong>Parsing Heurístico</strong> en PHP que utiliza Expresiones Regulares (Regex) complejas para identificar patrones de Tickers (ej. "AAPL", "BTC"), cantidades numéricas y precios en contextos multilínea.</li>
            <li><strong>Optimización:</strong> Se implementó un sistema de colas para no bloquear la interfaz de usuario durante el procesamiento de archivos pesados.</li>
        </ul>

        <h3>6.2. Arquitectura de Datos de Mercado en Tiempo Real</h3>
        <p>
            Para valorar el patrimonio del usuario en tiempo real ("Mark-to-Market"), el sistema consulta múltiples fuentes de datos externas.
        </p>
        <ul>
            <li><strong>APIs Utilizadas:</strong>
                <ul>
                    <li><strong>CoinGecko API:</strong> Para cotizaciones de criptomonedas.</li>
                    <li><strong>Alpha Vantage / Yahoo Finance:</strong> Para acciones y ETFs.</li>
                    <li><strong>Web Scraping Específico:</strong> Para fondos de inversión y ETFs europeos que no disponen de API pública gratuita, se han desarrollado scrapers a medida para:
                        <ul>
                            <li><strong>Morningstar (.es):</strong> Extracción de Valor Liquidativo (NAV) para fondos mutuos.</li>
                            <li><strong>Financial Times (markets.ft.com):</strong> Fuente secundaria fiable para fondos internacionales.</li>
                            <li><strong>JustETF:</strong> Para datos específicos de ETFs europeos (UCITS).</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li><strong>Estrategia de Actualización (Caché vs Cron):</strong>
                <p>
                    Se ha optado por una estrategia híbrida "Lazy Loading" con Caché en lugar de Cron Jobs masivos, debido a las limitaciones de las APIs gratuitas y para optimizar recursos del servidor:
                </p>
                <ul>
                    <li><strong>Caché bajo demanda:</strong> Los precios solo se actualizan cuando un usuario visita su Dashboard. El sistema verifica si existe un precio en caché (Redis/File) con menos de 15-60 minutos de antigüedad (TTL). Si es válido, se sirve instantáneamente; si ha expirado, se lanza la consulta a la API/Scraper en tiempo real y se renueva el caché.</li>
                    <li><strong>Ventaja:</strong> Esto evita realizar miles de consultas innecesarias para activos de usuarios que no están activos en ese momento, reduciendo drásticamente el riesgo de bloqueo por IP o "Rate Limiting".</li>
                </ul>
            </li>
        </ul>

        <h3>6.3. Visualización de Datos y Reportes</h3>
        <p>
            La renderización de gráficos financieros requiere transformar grandes volúmenes de datos transaccionales en estructuras JSON optimizadas.
        </p>
        <ul>
            <li><strong>Frontend:</strong> Uso de <strong>Chart.js</strong> con wrappers de Vue 3 para reactividad instantánea. Los gráficos de "Evolución de Patrimonio" calculan acumulados diarios en el cliente para reducir carga del servidor.</li>
            <li><strong>Generación PDF:</strong> Implementación de <strong>DomPDF</strong> para convertir vistas HTML/Blade en documentos descargables. Se tuvo que solucionar la compatibilidad de estilos CSS modernos (Flexbox/Grid) que no son soportados nativamente por el motor de renderizado PDF.</li>
        </ul>
    </div>

    <div class="page-break"></div>

    <div class="content">
        <div class="header">
            <h3>7. Conclusiones y Futuro</h3>
            <span>07</span>
        </div>
        
        <h1 class="section-title">7. Conclusiones</h1>
        
        <h3>Logros del Proyecto</h3>
        <p>
            FintechPro logra unificar la complejidad de la gestión financiera en una herramienta accesible. La arquitectura elegida (Laravel + Inertia + Vue) ha demostrado ser altamente eficiente, reduciendo el tiempo de desarrollo sin comprometer la calidad del producto final.
        </p>

        <h3>Líneas Futuras</h3>
        <p>
            El proyecto sienta las bases para futuras expansiones, entre las que se contemplan:
        </p>
        <ul>
            <li><strong>Integración Bancaria (PSD2):</strong> Conexión automática con bancos para importación de movimientos.</li>
            <li><strong>IA Predictiva:</strong> Análisis de patrones de gasto para sugerir presupuestos inteligentes.</li>
            <li><strong>App Móvil Nativa:</strong> Desarrollo de versión móvil utilizando tecnologías como Capacitor o React Native.</li>
            <li><strong>Gamificación:</strong> Sistema de logros para incentivar el ahorro.</li>
        </ul>

        <h3>Bibliografía</h3>
        <ul>
            <li>Laravel Documentation. (2024). laravel.com</li>
            <li>Vue.js Documentation. (2024). vuejs.org</li>
            <li>Inertia.js - The Modern Monolith. (2024). inertiajs.com</li>
            <li>Tailwind CSS - Rapidly build modern websites. (2024). tailwindcss.com</li>
        </ul>
    </div>

    <div class="footer">
        FintechPro - Proyecto de Desarrollo de Aplicaciones Web - {{ date('Y') }}
    </div>

</body>
</html>
