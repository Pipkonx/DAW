<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($employee) ? __('Editar Empleado') : __('Nuevo Empleado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ isset($employee) ? route('employees.update', $employee) : route('employees.store') }}" method="POST">
                    @csrf
                    @if(isset($employee))
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">DNI</label>
                            <input type="text" name="dni" class="form-control @error('dni') is-invalid @enderror" value="{{ old('dni', $employee->dni ?? '') }}" {{ isset($employee) && !auth()->user()->isAdmin() ? 'readonly' : '' }}>
                            @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name ?? '') }}" {{ isset($employee) && !auth()->user()->isAdmin() ? 'readonly' : '' }}>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $employee->email ?? '') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $employee->phone ?? '') }}" {{ isset($employee) && !auth()->user()->isAdmin() ? 'readonly' : '' }}>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $employee->address ?? '') }}" {{ isset($employee) && !auth()->user()->isAdmin() ? 'readonly' : '' }}>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fecha de Alta</label>
                            <input type="date" name="hire_date" class="form-control @error('hire_date') is-invalid @enderror" value="{{ old('hire_date', $employee->hire_date ?? '') }}">
                            @error('hire_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if(auth()->user()->isAdmin())
                        <div class="col-md-6">
                            <label class="form-label">Rol</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror">
                                <option value="operator" {{ old('role', $employee->role ?? '') == 'operator' ? 'selected' : '' }}>Operario</option>
                                <option value="admin" {{ old('role', $employee->role ?? '') == 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                                <option value="1" {{ old('is_active', $employee->is_active ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ old('is_active', $employee->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif

                        @if(!isset($employee))
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($employee) ? 'Actualizar' : 'Guardar' }}
                        </button>
                        <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>