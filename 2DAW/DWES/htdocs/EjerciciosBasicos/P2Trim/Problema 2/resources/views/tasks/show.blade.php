<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles de la Tarea #') . $task->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Información General</h5>
                        <p><strong>Cliente:</strong> {{ $task->client->name }}</p>
                        <p><strong>Estado:</strong> 
                            <span class="badge {{ $task->status == 'done' ? 'bg-success' : ($task->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </p>
                        <p><strong>Fecha Creación:</strong> {{ $task->created_at->format('d/m/Y H:i') }}</p>
                        @if($task->completion_date)
                            <p><strong>Fecha Realización:</strong> {{ \Carbon\Carbon::parse($task->completion_date)->format('d/m/Y') }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h5>Contacto y Ubicación</h5>
                        <p><strong>Contacto:</strong> {{ $task->contact_person }} ({{ $task->contact_phone }})</p>
                        <p><strong>Email:</strong> {{ $task->contact_email }}</p>
                        <p><strong>Dirección:</strong> {{ $task->address }}, {{ $task->city }} ({{ $task->postal_code }}), {{ $task->province->name }}</p>
                    </div>
                </div>

                <hr>

                <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        @if(auth()->user()->isAdmin())
                            <!-- Admin can edit everything -->
                            <div class="col-md-6">
                                <label class="form-label">Operario Asignado</label>
                                <select name="operator_id" class="form-select">
                                    @foreach(\App\Models\User::where('role', 'operator')->get() as $operator)
                                        <option value="{{ $operator->id }}" {{ $task->operator_id == $operator->id ? 'selected' : '' }}>{{ $operator->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Estado</label>
                                <select name="status" class="form-select">
                                    <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Realizada</option>
                                    <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $task->description) }}</textarea>
                            </div>
                            <!-- Hidden fields for other required admin validation fields to avoid errors if not editing them -->
                            <input type="hidden" name="client_id" value="{{ $task->client_id }}">
                            <input type="hidden" name="contact_person" value="{{ $task->contact_person }}">
                            <input type="hidden" name="contact_phone" value="{{ $task->contact_phone }}">
                            <input type="hidden" name="contact_email" value="{{ $task->contact_email }}">
                            <input type="hidden" name="address" value="{{ $task->address }}">
                            <input type="hidden" name="city" value="{{ $task->city }}">
                            <input type="hidden" name="postal_code" value="{{ $task->postal_code }}">
                            <input type="hidden" name="province_code" value="{{ $task->province_code }}">
                        @else
                            <!-- Operator limited view -->
                            <div class="col-md-6">
                                <label class="form-label">Cambiar Estado</label>
                                <select name="status" class="form-select">
                                    <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Realizada</option>
                                    <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>
                        @endif

                        <div class="col-md-12">
                            <label class="form-label">Anotaciones Posteriores (Resumen del trabajo)</label>
                            <textarea name="posterior_notes" class="form-control @error('posterior_notes') is-invalid @enderror" rows="3">{{ old('posterior_notes', $task->posterior_notes) }}</textarea>
                            @error('posterior_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fecha de Realización</label>
                            <input type="date" name="completion_date" class="form-control @error('completion_date') is-invalid @enderror" value="{{ old('completion_date', $task->completion_date) }}">
                            @error('completion_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Adjuntar Archivo (PDF, Imagen, Doc)</label>
                            <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
                            @if($task->attachment_path)
                                <div class="mt-2 text-sm text-gray-500">
                                    Archivo actual: <i class="fas fa-file"></i> Subido
                                </div>
                            @endif
                            @error('attachment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Actualizar Tarea</button>
                        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>