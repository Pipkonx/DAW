<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Mi Anteproyecto: FintechPro - Memoria Completa del Proyecto</title>
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
        code { background: #f1f5f9; padding: 2px 4px; border-radius: 4px; font-family: monospace; font-size: 12px; color: #e11d48; }
        
        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 12px; }
        th, td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; }
        th { background: #f8fafc; color: #0f172a; }

        .note { 
            background: #fff7ed; 
            border: 1px solid #fdba74; 
            padding: 15px; 
            border-radius: 8px; 
            margin: 20px 0; 
            font-size: 13px; 
            color: #9a3412; 
        }
        .note strong { color: #c2410c; }

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
    </style>
</head>
<body>

    <!-- Portada -->
    <div class="cover">
        <div class="logo-text">FintechPro</div>
        <div class="logo-sub">Memoria Completa del Proyecto</div>
        
        <h1>Anteproyecto Fin de Grado</h1>
        <h2>De la idea inicial a la arquitectura avanzada: Mi diario de desarrollo</h2>
        
        <div class="meta-box">
            <div class="meta">
                <p><strong>Autor:</strong> {{ $author }}</p>
                <p><strong>Fecha:</strong> {{ $date }}</p>
                <p><strong>Ciclo:</strong> Desarrollo de Aplicaciones Web</p>
                <p><strong>Proyecto:</strong> FintechPro</p>
            </div>
        </div>
    </div>
    
    <div class="page-break"></div>

    <!-- 1. Introducción -->
    <div class="content">
        <div class="header">
            <h3>1. Introducción y Motivación</h3>
            <span>01</span>
        </div>
        
        <h1 class="section-title">1. ¿Por qué FintechPro?</h1>
        <p>
            Como desarrollador y entusiasta de las finanzas personales, me di cuenta de que gestionar el patrimonio hoy en día es una tarea fragmentada. Tenemos cuentas bancarias, brokers de bolsa, exchanges de criptomonedas y gestoras de fondos. Consultar el estado real de mis finanzas implicaba abrir cinco aplicaciones distintas y consolidar los datos manualmente en un Excel.
        </p>
        <p>
            <strong>FintechPro</strong> nace para resolver este problema: centralizar en una sola aplicación web reactiva toda la salud financiera del usuario, automatizando los cálculos de rentabilidad y proporcionando una visión clara y estética de los datos.
        </p>
        
        <h2>El problema que quería resolver</h2>
        <p>
            Quería eliminar la fricción de la actualización manual. Las hojas de cálculo son potentes pero visualmente pobres y difíciles de mantener en dispositivos móviles. Quería una SPA (Single Page Application) que me diera respuestas instantáneas: "¿Cuánto he ahorrado este mes?", "¿Cuál es el valor actual de mis activos?".
        </p>

        <div class="note">
            <strong>Nota técnica:</strong> He diseñado el sistema bajo una arquitectura SPA usando <strong>Inertia.js</strong>. Esto permite que la navegación sea instantánea, como en una app nativa, pero manteniendo la robustez del backend en Laravel.
        </div>
    </div>

    <div class="page-break"></div>

    <!-- 2. Objetivos -->
    <div class="content">
        <div class="header">
            <h3>2. Objetivos del Proyecto</h3>
            <span>02</span>
        </div>
        
        <h1 class="section-title">2. Metas y Desafíos</h1>

        <h2>Objetivo General</h2>
        <p>
            Desarrollar una plataforma integral de gestión financiera que unifique el seguimiento de inversiones y el control de gastos, automatizando la obtención de datos de mercado.
        </p>

        <h2>Objetivos específicos</h2>
        <ul>
            <li><strong>Autenticación Segura:</strong> Implementar login social con Google (<code>app/Http/Controllers/Auth/GoogleAuthController.php</code>).</li>
            <li><strong>Gestión Multi-activo:</strong> Soportar acciones, ETFs, criptomonedas y fondos de inversión.</li>
            <li><strong>Automatización OCR/PDF:</strong> Extraer transacciones automáticamente de extractos bancarios (<code>app/Http/Controllers/PortfolioController.php</code>).</li>
            <li><strong>Análisis Visual:</strong> Crear dashboards interactivos con <code>Chart.js</code> (<code>resources/js/Components/Charts/</code>).</li>
            <li><strong>Exportación:</strong> Generar reportes financieros profesionales en PDF (<code>app/Http/Controllers/PdfController.php</code>).</li>
        </ul>
    </div>

    <div class="page-break"></div>

    <!-- 3. Tecnologías y Dependencias -->
    <div class="content">
        <div class="header">
            <h3>3. Stack Tecnológico</h3>
            <span>03</span>
        </div>
        
        <h1 class="section-title">3. Herramientas y Librerías</h1>
        <p>
            Para este proyecto elegí lo que yo llamo el "Dream Team" del desarrollo web moderno. Aquí detallo las piezas clave:
        </p>
        
        <h3>Backend (PHP - Laravel 11)</h3>
        <ul>
            <li><strong>Inertia.js:</strong> El puente que une Laravel con Vue sin necesidad de una API REST separada.</li>
            <li><strong>Smalot PdfParser:</strong> Para extraer texto de extractos bancarios en PDF.</li>
            <li><strong>Symfony DomCrawler:</strong> El motor de mi sistema de <strong>Web Scraping</strong> en <code>app/Services/MarketData/FundService.php</code>.</li>
            <li><strong>Laravel Socialite:</strong> Para el flujo OAuth con Google.</li>
        </ul>

        <h3>Frontend (JS - Vue 3)</h3>
        <ul>
            <li><strong>Vue 3 (Composition API):</strong> Por su reactividad y modularidad.</li>
            <li><strong>Tailwind CSS:</strong> Para un diseño moderno con soporte nativo de Modo Oscuro.</li>
            <li><strong>Chart.js:</strong> Para las gráficas financieras dinámicas.</li>
            <li><strong>Ziggy:</strong> Para usar las rutas de Laravel directamente en el frontend.</li>
        </ul>

        <div class="note">
            <strong>Nota técnica sobre dependencias:</strong> He minimizado el uso de librerías externas para evitar el "dependency hell", eligiendo solo aquellas que aportan un valor crítico como el parseo de PDF o la renderización de gráficas.
        </div>
    </div>

    <div class="page-break"></div>

    <!-- 4 & 5. Arquitectura y DB -->
    <div class="content">
        <div class="header">
            <h3>4-5. Arquitectura y Base de Datos</h3>
            <span>04</span>
        </div>
        
        <h1 class="section-title">4. Diseño del Sistema</h1>
        <p>
            He seguido el patrón <strong>MVC</strong> vitaminado con servicios y componentes reactivos.
        </p>
        
        <h2>Estructura de Carpetas</h2>
        <ul>
            <li><code>app/Http/Controllers/</code>: Lógica de respuesta a peticiones.</li>
            <li><code>app/Services/</code>: Lógica de negocio extraída (Scraping, APIs de mercado).</li>
            <li><code>resources/js/Pages/</code>: Pantallas de la aplicación (SPA).</li>
            <li><code>database/migrations/</code>: El ADN de la base de datos.</li>
        </ul>

        <h2>Base de Datos y Relaciones</h2>
        <p>
            Diseñé una base de datos relacional robusta. Un usuario (<code>User</code>) posee múltiples carteras (<code>Portfolio</code>), las cuales contienen activos (<code>Asset</code>). Cada activo o categoría de gasto está vinculado a transacciones (<code>Transaction</code>).
        </p>
        <p>
            Un hito importante fue la creación de <code>MarketAsset</code> (<code>database/migrations/2026_02_16_121308_create_assets_table.php</code>), que actúa como el catálogo maestro de activos con precios actualizados.
        </p>
        <p>
            <strong>Seeders:</strong> El <code>CategorySeeder.php</code> es vital para que la aplicación nazca con un sistema de categorías listo para usar.
        </p>
    </div>

    <div class="page-break"></div>

    <!-- 6 & 7. APIs y Scraping -->
    <div class="content">
        <div class="header">
            <h3>6-7. Integraciones y Scraping</h3>
            <span>05</span>
        </div>
        
        <h1 class="section-title">5. Obtención de Datos</h1>
        <p>
            FintechPro no sería útil sin datos en tiempo real. He implementado un sistema híbrido:
        </p>

        <h2>APIs Utilizadas</h2>
        <ul>
            <li><strong>CoinGecko:</strong> Precios de cripto (<code>app/Services/MarketData/CryptoService.php</code>).</li>
            <li><strong>FMP API:</strong> Cotizaciones bursátiles (<code>app/Services/MarketData/StockService.php</code>).</li>
            <li><strong>OCR.space:</strong> Para leer imágenes de tickets y extractos.</li>
        </ul>

        <h2>Sistema de Web Scraping</h2>
        <p>
            Dado el alto coste de las APIs de fondos de inversión, desarrollé mis propios scrapers en <code>app/Services/MarketData/FundService.php</code> usando <code>DomCrawler</code>.
        </p>
        <ul>
            <li><strong>Morningstar:</strong> Obtención del NAV de fondos españoles por ISIN.</li>
            <li><strong>JustETF:</strong> Datos detallados de fondos cotizados europeos.</li>
            <li><strong>Financial Times:</strong> Buscador global y fuente de respaldo.</li>
        </ul>

        <div class="note">
            <strong>Nota técnica sobre Scraping:</strong> Para cumplir con las políticas de uso y mejorar el rendimiento, implementé un sistema de <strong>Caché de 24 horas</strong>. Si un precio ya se ha obtenido hoy, el sistema no vuelve a consultar la web externa.
        </div>
    </div>

    <div class="page-break"></div>

    <!-- 8. Backend y Flujo -->
    <div class="content">
        <div class="header">
            <h3>8. Backend y Flujo de Datos</h3>
            <span>06</span>
        </div>
        
        <h1 class="section-title">6. El Camino de la Información</h1>
        <p>
            Cuando entras al Dashboard, ocurre lo siguiente:
        </p>
        <ol>
            <li>El <code>DashboardController.php</code> recibe la petición.</li>
            <li>Se calculan los totales de cada cartera y el valor actual de los activos.</li>
            <li>Si los precios son antiguos, se lanzan <strong>Jobs</strong> en segundo plano (<code>UpdatePricesJob.php</code>).</li>
            <li>Los datos se inyectan en Vue a través de Inertia.</li>
            <li>El frontend renderiza las gráficas y la lista de transacciones con <strong>scroll infinito</strong>.</li>
        </ol>

        <h2>Endpoints Principales</h2>
        <table>
            <tr><th>Ruta</th><th>Método</th><th>Acción</th></tr>
            <tr><td><code>/dashboard</code></td><td>GET</td><td>Carga de KPIs y resumen.</td></tr>
            <tr><td><code>/api/dashboard/transactions</code></td><td>GET</td><td>Scroll infinito de movimientos.</td></tr>
            <tr><td><code>/portfolios/preview-import</code></td><td>POST</td><td>Procesado de PDF/Imagen con OCR.</td></tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- 9. Roadmap y Problemas -->
    <div class="content">
        <div class="header">
            <h3>9. Roadmap y Desafíos</h3>
            <span>07</span>
        </div>
        
        <h1 class="section-title">7. Evolución del Proyecto</h1>
        <p>
            El desarrollo fue incremental: desde un simple login hasta un sistema de automatización con OCR.
        </p>

        <h2>Problemas y Soluciones</h2>
        <ul>
            <li><strong>Gestión de Fechas:</strong> La discrepancia entre PHP y JS se solucionó normalizando todo a ISO-8601.</li>
            <li><strong>Rendimiento:</strong> Las consultas pesadas en el historial se optimizaron con <code>selectRaw</code> y paginación asíncrona.</li>
            <li><strong>Persistencia:</strong> Usé <code>localStorage</code> para que el rango de fechas seleccionado en el Análisis de Gastos se mantenga al navegar entre secciones.</li>
        </ul>

        <h2>Funcionalidades Avanzadas</h2>
        <p>
            Implementé comandos personalizados de Artisan (<code>app/Console/Commands/</code>) para tareas de mantenimiento y actualización de precios sin intervención manual.
        </p>
    </div>

    <div class="page-break"></div>

    <!-- 10. Cierre -->
    <div class="content">
        <div class="header">
            <h3>10-13. Futuro e Instalación</h3>
            <span>08</span>
        </div>
        
        <h1 class="section-title">8. Conclusiones y Futuro</h1>
        
        <h2>Instalación</h2>
        <p>
            El proyecto requiere PHP 8.2+, Node.js y una base de datos SQL. Tras clonar, ejecutar <code>composer install</code>, <code>npm install</code> y <code>php artisan migrate --seed</code>. Es vital configurar las claves de las APIs en el <code>.env</code>.
        </p>

        <h2>Mejoras Futuras</h2>
        <ul>
            <li>Integración con APIs de Open Banking (PSD2).</li>
            <li>Sistema de presupuestos y alertas inteligentes.</li>
            <li>App móvil nativa usando Capacitor o React Native.</li>
        </ul>

        <h2>Reflexión final</h2>
        <p>
            FintechPro ha sido un viaje de aprendizaje profundo en arquitectura de software, seguridad y UX. He logrado transformar una necesidad personal en una herramienta robusta y escalable que demuestra el poder del stack Laravel + Vue.
        </p>
    </div>

    <div class="footer">
        FintechPro - Memoria de Proyecto Final - {{ date('Y') }}
    </div>

</body>
</html>
