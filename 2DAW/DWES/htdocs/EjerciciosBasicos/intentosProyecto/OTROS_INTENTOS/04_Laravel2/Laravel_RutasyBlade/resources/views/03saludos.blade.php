<!DOCTYPE html>
<html>
    <head>
        <title>Blade 1</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h1>Saludos</h1>
        <ul>
        @foreach($nombres as $nombre)
        <li>Hola {{ $nombre }}</li>
        @endforeach
        </ul>
        @if (count($nombres)>5) 
            <p>Hay muchos nombres</p>
        @endif
    </body>
</html>

