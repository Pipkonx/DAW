<div class="form-container">
    <form action="{{ $formActionUrl }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label>NIF/CIF:</label><br>
        <input type="text" name="nifCif" value="{{ htmlspecialchars($_POST['nifCif'] ?? ($nifCif ?? '')) }}"><br>
        {{--? No quiere que pongamos los {!! !!} --}}
        {{--? Tampoco podemos usar @error --}}
        {!! \App\Models\Funciones::verErrores('nif_cif') !!}
        {{-- @error('nif_cif')
        <div class="error">{{ $message }}</div>
        @enderror --}}

        <br>

        <label>Persona de contacto:</label><br>
        <input type="text" name="personaNombre"
            value="{{ htmlspecialchars($_POST['personaNombre'] ?? ($personaNombre ?? '')) }}"><br>
        {!! \App\Models\Funciones::verErrores('nombre_persona') !!}
        {{-- @error('nombre_persona')
        <div class="error">{{ $message }}</div>
        @enderror --}}


        <br>
        <label>Teléfono:</label><br>
        <input type="text" name="telefono" value="{{ htmlspecialchars($_POST['telefono'] ?? ($telefono ?? '')) }}"><br>
        {!! \App\Models\Funciones::verErrores('telefono') !!}
        {{-- @error('telefono')
        <div class="error">{{ $message }}</div>
        @enderror --}}


        <br>

        <label>Correo electrónico:</label><br>
        <input type="text" name="correo" value="{{ htmlspecialchars($_POST['correo'] ?? ($correo ?? '')) }}"><br>
        {!! \App\Models\Funciones::verErrores('correo') !!}
        {{-- @error('correo')
        <div class="error">{{ $message }}</div>
        @enderror --}}


        <br>

        <label>Descripción de la tarea:</label><br>
        <textarea
            name="descripcionTarea">{{ htmlspecialchars($_POST['descripcionTarea'] ?? ($descripcionTarea ?? '')) }}</textarea><br>
        {!! \App\Models\Funciones::verErrores('descripcion_tarea') !!}
        {{-- @error('descripcion_tarea')
        <div class="error">{{ $message }}</div>
        @enderror --}}


        <br>

        <label>Dirección:</label><br>
        <input type="text" name="direccionTarea"
            value="{{ htmlspecialchars($_POST['direccionTarea'] ?? ($direccionTarea ?? '')) }}"><br><br>

        <label>Población:</label><br>
        <input type="text" name="poblacion"
            value="{{ htmlspecialchars($_POST['poblacion'] ?? ($poblacion ?? '')) }}"><br><br>

        <label>Código Postal:</label><br>
        <input type="text" name="codigoPostal"
            value="{{ htmlspecialchars($_POST['codigoPostal'] ?? ($codigoPostal ?? '')) }}"><br><br>

        <label>Provincia:</label><br>
        <select name="provincia">
            <option value="">Seleccione provincia</option>
            <?php \App\Models\Funciones::mostrarProvincias($_POST['provincia'] ?? ($provincia ?? '')) ?>
        </select><br>

        <br>

        <label>Estado:</label>
        <span class="radio-group-item"><input type="radio" id="estadoB" name="estadoTarea" value="B" {{ (($_POST['estadoTarea'] ?? ($estadoTarea ?? '')) == "B") ? "checked" : "" }}> <label
                for="estadoB">Esperando ser aprobada</label></span>
        <span class="radio-group-item"><input type="radio" id="estadoP" name="estadoTarea" value="P" {{ (($_POST['estadoTarea'] ?? ($estadoTarea ?? '')) == "P") ? "checked" : "" }}> <label
                for="estadoP">Pendiente</label></span>
        <span class="radio-group-item"><input type="radio" id="estadoR" name="estadoTarea" value="R" {{ (($_POST['estadoTarea'] ?? ($estadoTarea ?? '')) == "R") ? "checked" : "" }}> <label
                for="estadoR">Realizada</label></span>
        <span class="radio-group-item"><input type="radio" id="estadoC" name="estadoTarea" value="C" {{ (($_POST['estadoTarea'] ?? ($estadoTarea ?? '')) == "C") ? "checked" : "" }}> <label
                for="estadoC">Cancelada</label></span>

        <label>Operario encargado:</label><br>
        <select name="operarioEncargado">
            <option value="">Seleccione operario</option>
            <option value="Juan Pérez" {{ (($_POST['operarioEncargado'] ?? ($operarioEncargado ?? '')) == "Juan Pérez") ? "selected" : "" }}>Juan Pérez</option>
            <option value="María López" {{ (($_POST['operarioEncargado'] ?? ($operarioEncargado ?? '')) == "María López") ? "selected" : "" }}>María López</option>
            <option value="Carlos Ruiz" {{ (($_POST['operarioEncargado'] ?? ($operarioEncargado ?? '')) == "Carlos Ruiz") ? "selected" : "" }}>Carlos Ruiz</option>
            <option value="Ana María Fernández" {{ (($_POST['operarioEncargado'] ?? ($operarioEncargado ?? '')) == "Ana María Fernández") ? "selected" : "" }}>Ana
                María
                Fernández</option>
            <option value="Sara Martínez" {{ (($_POST['operarioEncargado'] ?? ($operarioEncargado ?? '')) == "Sara Martínez") ? "selected" : "" }}>Sara Martínez
            </option>
            <option value="Lucía Hurtado" {{ (($_POST['operarioEncargado'] ?? ($operarioEncargado ?? '')) == "Lucía Hurtado") ? "selected" : "" }}>Lucía Hurtado
            </option>
        </select><br><br>

        <label>Fecha de realización:</label><br>
        <input type="date" name="fechaRealizacion"
            value="{{ htmlspecialchars($_POST['fechaRealizacion'] ?? ($fechaRealizacion ?? '')) }}"><br>
        {!! \App\Models\Funciones::verErrores('fecha_realizacion') !!}


        <br>

        <label for="anotacionesAnteriores">Anotaciones anteriores:</label><br>
        <textarea id="anotacionesAnteriores"
            name="anotacionesAnteriores">{{ htmlspecialchars($_POST['anotacionesAnteriores'] ?? ($anotacionesAnteriores ?? '')) }}</textarea><br><br>

        <label for="fichero_resumen">Fichero resumen:</label>
        <input type="file" id="fichero_resumen" name="fichero_resumen"><br><br>

        <label for="fotos">Fotos del trabajo:</label>
        <input type="file" id="fotos" name="fotos[]" multiple><br><br>

        @if(isset($id))<input type="hidden" name="id" value="{{ $id }}">@endif
        @php
            $rutaBase = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'operario') ? 'operario/tareas' : 'admin/tareas';
        @endphp
        <a class="btn btn-cancel" href="{{ url($rutaBase) }}">Cancelar</a>
        <input type="submit" value="{{ isset($id) ? 'Guardar cambios' : 'Crear tarea' }}">
    </form>