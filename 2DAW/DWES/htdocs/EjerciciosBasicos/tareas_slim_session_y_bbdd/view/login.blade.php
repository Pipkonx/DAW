@extends('_template')

@section('cuerpo')
    <div style="width:30em; margin:1em auto; padding:.5em; border:1px solid #999">
        <h1>Login</h1>

        <form method="post">
            <p>Usuario:
                <input name="user" type="text" value="{!! filter_input(INPUT_POST, 'user') !!}">
            </p>
            <p>Clave:
                <input name="passwd" type="password">
            </p>
            <div>{!! $ge->ErrorFormateado('user') !!}</div>
            <p><button type="submit">Iniciar session</button></p>
        </form>
    </div>
@endsection
