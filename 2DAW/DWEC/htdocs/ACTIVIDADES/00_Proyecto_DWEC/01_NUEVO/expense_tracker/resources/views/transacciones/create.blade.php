<div class="form-header">Añadir Nueva Transacción</div>

<div class="form-body">
    <form action="{{ route('transacciones.store') }}" method="POST">
        @csrf
        <div class="form-field">
            <label for="monto" class="form-label">Monto</label>
            <input type="number" class="form-input" id="monto" name="monto" step="0.01" required>
        </div>
        <div class="form-field">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" class="form-input" id="descripcion" name="descripcion" required>
        </div>
        <div class="form-field">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" class="form-input" id="fecha" name="fecha" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="form-field">
            <label for="tipo" class="form-label">Tipo</label>
            <select class="form-input" id="tipo" name="tipo" required>
                <option value="ingreso">Ingreso</option>
                <option value="gasto">Gasto</option>
                <option value="inversion">Inversión</option>
            </select>
        </div>
        <div class="form-field">
            <label for="categoria_principal" class="form-label">Categoría Principal</label>
            <select class="form-input" id="categoria_principal" name="categoria_principal_id" required>
                <option value="">Selecciona una categoría</option>
                <option value="1">Categoría Hardcodeada 1</option>
                <option value="2">Categoría Hardcodeada 2</option>
                <option value="3">Categoría Hardcodeada 3</option>
            </select>
        </div>
        </div>
        <div class="form-field" id="subcategoria_field" style="display: none;">
            <label for="categori-id" class="form-label">Subcategoría</label>
            <select class="form-input" id="categori-id" name="categori-id">
                <option value="">Selecciona una subcategoría</option>
            </select>
        </div>

        <script>
            document.getElementById('categoria_principal').addEventListener('change', function() {
                var parentId = this.value;
                var subcategoriaField = document.getElementById('subcategoria_field');
                var subcategoriaSelect = document.getElementById('categori-id');

                subcategoriaSelect.innerHTML = '<option value="">Selecciona una subcategoría</option>'; // Clear previous options

                if (parentId) {
                    fetch('/transacciones/subcategorias?parent_id=' + parentId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                data.forEach(function(subcategoria) {
                                    var option = document.createElement('option');
                                    option.value = subcategoria.id;
                                    option.textContent = subcategoria.nombre;
                                    subcategoriaSelect.appendChild(option);
                                });
                                subcategoriaField.style.display = 'block';
                            } else {
                                subcategoriaField.style.display = 'none';
                            }
                        })
                        .catch(error => console.error('Error fetching subcategories:', error));
                } else {
                    subcategoriaField.style.display = 'none';
                }
            });
        </script>
        <button type="submit" class="button-primary">Guardar Transacción</button>
        <a href="{{ route('home') }}" class="button-secondary">Cancelar</a>
    </form>
</div>