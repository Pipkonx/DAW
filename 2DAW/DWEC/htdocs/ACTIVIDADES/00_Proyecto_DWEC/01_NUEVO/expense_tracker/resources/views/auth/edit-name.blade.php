@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Modificar Nombre de Usuario</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('name.update') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nuevo Nombre</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', Auth::user()->name) }}" required autocomplete="name" autofocus>

                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Actualizar Nombre
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection