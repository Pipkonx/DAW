<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($client) ? __('Editar Cliente') : __('Nuevo Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ isset($client) ? route('clients.update', $client) : route('clients.store') }}" method="POST">
                    @csrf
                    @if(isset($client))
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">CIF</label>
                            <input type="text" name="cif" class="form-control @error('cif') is-invalid @enderror" value="{{ old('cif', $client->cif ?? '') }}">
                            @error('cif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Nombre/Razón Social</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $client->name ?? '') }}">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $client->phone ?? '') }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $client->email ?? '') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Cuenta Bancaria (IBAN)</label>
                            <input type="text" name="bank_account" class="form-control @error('bank_account') is-invalid @enderror" value="{{ old('bank_account', $client->bank_account ?? '') }}">
                            @error('bank_account') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">País</label>
                            <select name="country" class="form-select @error('country') is-invalid @enderror">
                                @foreach($countries as $country)
                                    <option value="{{ $country }}" {{ old('country', $client->country ?? '') == $country ? 'selected' : '' }}>{{ $country }}</option>
                                @endforeach
                            </select>
                            @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Moneda</label>
                            <select name="currency" class="form-select @error('currency') is-invalid @enderror">
                                <option value="EUR" {{ old('currency', $client->currency ?? '') == 'EUR' ? 'selected' : '' }}>Euro (€)</option>
                                <option value="USD" {{ old('currency', $client->currency ?? '') == 'USD' ? 'selected' : '' }}>Dólar ($)</option>
                                <option value="GBP" {{ old('currency', $client->currency ?? '') == 'GBP' ? 'selected' : '' }}>Libra (£)</option>
                            </select>
                            @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Cuota Mensual</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="monthly_fee" class="form-control @error('monthly_fee') is-invalid @enderror" value="{{ old('monthly_fee', $client->monthly_fee ?? '') }}">
                                <span class="input-group-text">€/$</span>
                            </div>
                            @error('monthly_fee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if(isset($client))
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                                <option value="1" {{ old('is_active', $client->is_active ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ old('is_active', $client->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($client) ? 'Actualizar' : 'Guardar' }}
                        </button>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>