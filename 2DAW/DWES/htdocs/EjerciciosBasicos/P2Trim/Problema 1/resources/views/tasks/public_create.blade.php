@extends('layouts.public')

@section('content')
<div class="card p-4">
    <h4 class="mb-4">Registrar Nueva Incidencia</h4>
    
    <form action="{{ route('tasks.public.store') }}" method="POST">
        @csrf
        
        <div class="row g-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Por favor, identifíquese con sus datos de cliente para registrar la incidencia.
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">CIF del Cliente</label>
                <input type="text" name="cif" class="form-control @error('cif') is-invalid @enderror" value="{{ old('cif') }}" placeholder="Ej: B12345678" required>
                @error('cif') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Teléfono Registrado</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Su teléfono de contacto" required>
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <hr class="my-4">

            <div class="col-md-6">
                <label class="form-label">Persona de Contacto para esta Incidencia</label>
                <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person') }}" required>
                @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Teléfono de Contacto Directo</label>
                <input type="text" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" value="{{ old('contact_phone') }}" required>
                @error('contact_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-12">
                <label class="form-label">Email de Contacto</label>
                <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror" value="{{ old('contact_email') }}" required>
                @error('contact_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-12">
                <label class="form-label">Dirección de la Incidencia</label>
                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" required>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Ciudad</label>
                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" required>
                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Código Postal</label>
                <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code') }}" required>
                @error('postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Provincia</label>
                <select name="province_code" class="form-select @error('province_code') is-invalid @enderror" required>
                    <option value="">Seleccione...</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->code }}" {{ old('province_code') == $province->code ? 'selected' : '' }}>{{ $province->name }}</option>
                    @endforeach
                </select>
                @error('province_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Descripción Detallada de la Incidencia</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" placeholder="Describa el problema lo mejor posible..." required>{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 mt-4 text-end">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane"></i> Enviar Incidencia
                </button>
            </div>
        </div>
    </form>
</div>
@endsection