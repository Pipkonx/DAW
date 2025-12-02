<div class="form-container">
    <form action="{{ $formActionUrl }}" method="POST" enctype="multipart/form-data">
        @csrf
        @php
            $isAdmin = (($_SESSION['rol'] ?? '') === 'admin');
            $isEdit = isset($id);
            $disableOperatorFields = $isAdmin && $isEdit;
        @endphp
        <label>NIF/CIF:</label><br>
        <input type="text" name="nifCif" value="{{ htmlspecialchars($_POST['nifCif'] ?? ($nifCif ?? '')) }}"><br>
        @if($msg = \App\Models\M_Funciones::getError('nif_cif'))
            <div class="error">{{ $msg }}</div>
        @endif

        <br>

        <label>Persona de contacto:</label><br>
        <input type="text" name="personaNombre"
            value="{{ htmlspecialchars($_POST['personaNombre'] ?? ($personaNombre ?? '')) }}"><br>
        @if($msg = \App\Models\M_Funciones::getError('nombre_persona'))
            <div class="error">{{ $msg }}</div>
        @endif


        <br>
        <label>Teléfono:</label><br>
        <input type="text" name="telefono" value="{{ htmlspecialchars($_POST['telefono'] ?? ($telefono ?? '')) }}"><br>
        @if($msg = \App\Models\M_Funciones::getError('telefono'))
            <div class="error">{{ $msg }}</div>
        @endif


        <br>

        <label>Correo electrónico:</label><br>
        <input type="text" name="correo" value="{{ htmlspecialchars($_POST['correo'] ?? ($correo ?? '')) }}"><br>
        @if($msg = \App\Models\M_Funciones::getError('correo'))
            <div class="error">{{ $msg }}</div>
        @endif


        <br>

        <label>Descripción de la tarea:</label><br>
        <textarea
            name="descripcionTarea">{{ htmlspecialchars($_POST['descripcionTarea'] ?? ($descripcionTarea ?? '')) }}</textarea><br>
        @if($msg = \App\Models\M_Funciones::getError('descripcion_tarea'))
            <div class="error">{{ $msg }}</div>
        @endif


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
            @php
                $provSeleccionada = $_POST['provincia'] ?? ($provincia ?? '');
            @endphp
            @foreach(\App\Models\M_Funciones::$provincias as $codigo => $nombre)
                <option value="{{ $codigo }}" {{ $codigo == $provSeleccionada ? 'selected' : '' }}>
                    {{ $nombre }}
                </option>
            @endforeach
        </select><br>

        <br>

        <label>Estado:</label>
        @if($disableOperatorFields)
             <input type="hidden" name="estadoTarea" value="{{ $_POST['estadoTarea'] ?? ($estadoTarea ?? '') }}">
        @endif
        <span class="radio-group-item"><input type="radio" id="estadoB" name="estadoTarea" value="B" {{ (($_POST['estadoTarea'] ?? ($estadoTarea ?? '')) == "B") ? "checked" : "" }} {{ $disableOperatorFields ? 'disabled' : '' }}> <label
                for="estadoB">Esperando ser aprobada</label></span>
        <span class="radio-group-item"><input type="radio" id="estadoP" name="estadoTarea" value="P" {{ (($_POST['estadoTarea'] ?? ($estadoTarea ?? '')) == "P") ? "checked" : "" }} {{ $disableOperatorFields ? 'disabled' : '' }}> <label
                for="estadoP">Pendiente</label></span>
        <span class="radio-group-item"><input type="radio" id="estadoR" name="estadoTarea" value="R" {{ (($_POST['estadoTarea'] ?? ($estadoTarea ?? '')) == "R") ? "checked" : "" }} {{ $disableOperatorFields ? 'disabled' : '' }}> <label
                for="estadoR">Realizada</label></span>
        <span class="radio-group-item"><input type="radio" id="estadoC" name="estadoTarea" value="C" {{ (($_POST['estadoTarea'] ?? ($estadoTarea ?? '')) == "C") ? "checked" : "" }} {{ $disableOperatorFields ? 'disabled' : '' }}> <label
                for="estadoC">Cancelada</label></span>

        <label>Operario encargado:</label><br>
        <select name="operarioEncargado">
            <option value="">Seleccione operario</option>
            @if(isset($operarios) && is_array($operarios))
                @foreach($operarios as $op)
                    <option value="{{ $op['nombre'] }}" {{ (($_POST['operarioEncargado'] ?? ($operarioEncargado ?? '')) == $op['nombre']) ? "selected" : "" }}>
                        {{ $op['nombre'] }}
                    </option>
                @endforeach
            @endif
        </select><br><br>

        <label>Fecha de realización:</label><br>
        <input type="date" name="fechaRealizacion"
            value="{{ htmlspecialchars($_POST['fechaRealizacion'] ?? ($fechaRealizacion ?? '')) }}" {{ $disableOperatorFields ? 'disabled' : '' }}><br>
        @if($disableOperatorFields)
             <input type="hidden" name="fechaRealizacion" value="{{ $_POST['fechaRealizacion'] ?? ($fechaRealizacion ?? '') }}">
        @endif
        @if($msg = \App\Models\M_Funciones::getError('fecha_realizacion'))
            <div class="error">{{ $msg }}</div>
        @endif


        <br>

        <label for="anotacionesAnteriores">Anotaciones anteriores:</label><br>
        <textarea id="anotacionesAnteriores"
            name="anotacionesAnteriores" readonly>{{ htmlspecialchars($_POST['anotacionesAnteriores'] ?? ($anotacionesAnteriores ?? '')) }}</textarea><br><br>

        <label for="anotacionesPosteriores">Anotaciones posteriores:</label><br>
        <textarea id="anotacionesPosteriores"
            name="anotacionesPosteriores">{{ htmlspecialchars($_POST['anotacionesPosteriores'] ?? ($anotacionesPosteriores ?? '')) }}</textarea><br><br>

        <label for="fichero_resumen">Fichero resumen:</label>
        @if(!empty($ficherosResumen))
            <div class="file-item">
                <a href="{{ url('evidencias/' . $id . '/' . $ficherosResumen[0]) }}" target="_blank">{{ $ficherosResumen[0] }}</a>
                <button type="submit" form="form-delete-resumen-admin" class="btn-danger" style="font-size: 0.8em; padding: 2px 5px;">Eliminar</button>
            </div>
        @else
            <input type="file" id="fichero_resumen" name="fichero_resumen"><br><br>
        @endif

        <label for="fotos">Fotos del trabajo:</label>
        @if(!empty($fotos))
            @foreach($fotos as $foto)
                <div class="file-item">
                    <a href="{{ url('evidencias/' . $id . '/' . $foto) }}" target="_blank">{{ $foto }}</a>
                    <button type="submit" form="form-delete-foto-admin-{{ md5($foto) }}" class="btn-danger" style="font-size: 0.8em; padding: 2px 5px;">Eliminar</button>
                </div>
            @endforeach
        @endif
        <input type="file" id="fotos" name="fotos[]" multiple><br><br>

        @if(isset($id))<input type="hidden" name="id" value="{{ $id }}">@endif
        @php
            $rutaBase = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'operario') ? 'operario/tareas' : 'admin/tareas';
        @endphp
        <a class="btn btn-cancel" href="{{ url($rutaBase) }}">Cancelar</a>
        <input type="submit" value="{{ isset($id) ? 'Guardar cambios' : 'Crear tarea' }}">
    </form>
    
    @if(isset($id) && !empty($ficherosResumen))
         <form id="form-delete-resumen-admin" action="{{ url('admin/tareas/eliminar-fichero') }}" method="POST" style="display:none;">
             @csrf
             <input type="hidden" name="id" value="{{ $id }}">
             <input type="hidden" name="filename" value="{{ $ficherosResumen[0] }}">
         </form>
    @endif
    @if(isset($id) && !empty($fotos))
        @foreach($fotos as $foto)
            <form id="form-delete-foto-admin-{{ md5($foto) }}" action="{{ url('admin/tareas/eliminar-fichero') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="id" value="{{ $id }}">
                <input type="hidden" name="filename" value="{{ $foto }}">
            </form>
        @endforeach
    @endif