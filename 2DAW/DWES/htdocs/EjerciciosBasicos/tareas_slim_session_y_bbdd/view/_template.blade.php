<!-- 
LAYOUT DE LA APLICACIÓN 
ESTA PÁGINA DISPONE DONDE IRÁN LOS DIFERENTES BLOQUES QUE CONFORMAN LA APLICACIÓN

Se centra solamente en la presentación.
Deberemos indicarle:
    - titulo
    - menu
    - cuerpo

Podría tener tantos parámetros como quisiesemos
Igualmente nuestra aplicación podría tener tantos layouts como deseasemos
-->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gestor Tareas</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>    
<body>
    <header>
        <div style="background: #ccffff; text-align: center; font-size: 2em">
            Encabezado
        </div>
    </header>
    <nav id="menu">
        @include('menu')
    </nav>
    <main id="cuerpo">
        @yield('cuerpo')
    </main>
    <footer style="background: #ccffcc; clear: both;">
        Pie de página
    </footer>
</body>
</html>