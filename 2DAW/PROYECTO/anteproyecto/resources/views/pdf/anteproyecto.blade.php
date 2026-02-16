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

        h2 { color: #1e293b; font-size: 22px; margin-top: 30px; margin-bottom: 15px; font-weight: 700; }
        h3 { color: #334155; font-size: 18px; margin-top: 20px; margin-bottom: 10px; font-weight: 600; }
        
        p { margin-bottom: 15px; text-align: justify; color: #475569; }
        ul { margin-bottom: 20px; padding-left: 20px; }
        li { margin-bottom: 8px; color: #475569; }
        
        /* Tech Stack Cards */
        .tech-grid { display: table; width: 100%; border-spacing: 10px; margin-top: 20px; }
        .tech-card { 
            display: table-cell; 
            width: 33%; 
            background: #f8fafc; 
            border: 1px solid #e2e8f0; 
            padding: 20px; 
            border-radius: 8px; 
            vertical-align: top; 
        }
        .tech-title { font-weight: bold; color: #0f172a; margin-bottom: 10px; display: block; }
        .tech-desc { font-size: 12px; color: #64748b; }

        /* Tables */
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin: 25px 0; font-size: 14px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
        th { background-color: #f1f5f9; color: #334155; padding: 15px; text-align: left; font-weight: 600; border-bottom: 1px solid #e2e8f0; }
        td { padding: 15px; border-bottom: 1px solid #e2e8f0; color: #475569; }
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
            </div>
        </div>
    </div>
    
    <div class="page-break"></div>

    <!-- Page 1: Introducción -->
    <div class="content">
        <div class="header">
            <h3>FintechPro Documentación</h3>
            <span>01</span>
        </div>
        
        <h1 class="section-title">1. Resumen del Proyecto</h1>
        <p>
            <strong>FintechPro</strong> es una aplicación web de tipo Single Page Application (SPA) diseñada para centralizar y simplificar la gestión de las finanzas personales. El proyecto nace de la necesidad de unificar el control de ingresos, gastos, ahorros e inversiones complejas (acciones, criptomonedas, fondos) en una sola interfaz intuitiva y moderna.
        </p>
        <p>
            A diferencia de las hojas de cálculo tradicionales o aplicaciones bancarias fragmentadas, FintechPro ofrece una visión holística del patrimonio neto ("Net Worth"), permitiendo al usuario entender su salud financiera real mediante gráficos interactivos, cálculos automáticos de rendimiento y proyecciones a futuro.
        </p>
        
        <h2>Objetivos Específicos</h2>
        <ul>
            <li><strong>Centralización:</strong> Unificar cuentas de efectivo y portafolios de inversión en un solo dashboard.</li>
            <li><strong>Automatización:</strong> Calcular automáticamente rendimientos, precios promedio de compra y distribución de activos.</li>
            <li><strong>Educación Financiera:</strong> Proveer una interfaz explicativa que ayude a usuarios no expertos a entender conceptos como "Mark-to-Market", "Cash Flow" o "Asset Allocation".</li>
            <li><strong>Experiencia de Usuario (UX):</strong> Ofrecer una navegación fluida e instantánea gracias a la arquitectura SPA.</li>
        </ul>
    </div>

    <div class="page-break"></div>

    <!-- Page 2: Arquitectura Técnica -->
    <div class="content">
        <div class="header">
            <h3>FintechPro Documentación</h3>
            <span>02</span>
        </div>
        
        <h1 class="section-title">2. Arquitectura Técnica</h1>
        <p>
            El proyecto se ha desarrollado siguiendo una arquitectura moderna de separación de responsabilidades, pero aprovechando la integración monolítica que ofrece <strong>Inertia.js</strong> para simplificar el desarrollo sin sacrificar la experiencia de usuario de una SPA.
        </p>
        
        <div class="tech-grid">
            <div class="tech-card">
                <span class="tech-title">Backend: Laravel 11</span>
                <span class="tech-desc">API robusta, Eloquent ORM para manejo complejo de datos, Autenticación (Breeze/Socialite) y validaciones de seguridad.</span>
            </div>
            <div class="tech-card">
                <span class="tech-title">Frontend: Vue 3</span>
                <span class="tech-desc">Composition API para lógica reactiva, componentes modulares y reutilizables. Chart.js para visualización de datos.</span>
            </div>
            <div class="tech-card">
                <span class="tech-title">Estilos: Tailwind CSS</span>
                <span class="tech-desc">Diseño "Utility-first" para una interfaz limpia, consistente y responsive. Paleta de colores profesional (Slate/Blue).</span>
            </div>
        </div>

        <h2>Base de Datos</h2>
        <p>El modelo de datos relacional se estructura en torno a tres entidades principales:</p>
        <table>
            <tr>
                <th>Entidad</th>
                <th>Descripción</th>
            </tr>
            <tr>
                <td><strong>Users</strong></td>
                <td>Gestión de identidad, preferencias y autenticación segura.</td>
            </tr>
            <tr>
                <td><strong>Assets</strong></td>
                <td>Representa los instrumentos de inversión (Acciones, Crypto, Fondos). Almacena cantidad, precio medio y cotización actual.</td>
            </tr>
            <tr>
                <td><strong>Transactions</strong></td>
                <td>Registro inmutable de todos los movimientos (Ingresos, Gastos, Compra/Venta). Vincula usuarios con activos.</td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Page 3: Funcionalidades -->
    <div class="content">
        <div class="header">
            <h3>FintechPro Documentación</h3>
            <span>03</span>
        </div>
        
        <h1 class="section-title">3. Funcionalidades Clave</h1>
        
        <h3>Dashboard Interactivo</h3>
        <p>
            El panel principal actúa como centro de mando. Muestra KPIs en tiempo real (Patrimonio Neto, Ahorro Mensual) y gráficas de evolución histórica generadas dinámicamente.
        </p>

        <h3>Gestión de Inversiones</h3>
        <p>
            El sistema soporta operaciones complejas como compras parciales, ventas y recepción de dividendos. Calcula automáticamente el precio promedio ponderado de compra para estimar ganancias reales.
        </p>

        <h3>Seguridad</h3>
        <p>
            Implementación de autenticación OAuth 2.0 con Google, protección CSRF, sanitización de entradas y transacciones de base de datos (ACID) para asegurar la integridad financiera.
        </p>

        <h2>Conclusiones</h2>
        <p>
            FintechPro demuestra cómo las tecnologías web modernas pueden transformar la gestión financiera personal, ofreciendo herramientas de nivel profesional a usuarios domésticos en un entorno seguro y educativo.
        </p>
    </div>

    <div class="footer">
        FintechPro - Proyecto de Desarrollo de Aplicaciones Web - {{ date('Y') }}
    </div>

</body>
</html>