# 📋 CHULETA VUE 2 — EXAMEN COMPLETO
> **Sin internet · Todo aquí · Léelo de arriba a abajo cuando llegue el examen**

---

## 🗺️ ORDEN QUE DEBES SEGUIR SIEMPRE EN CUALQUIER PRÁCTICA

```
1️⃣  ESTRUCTURA HTML base (Bootstrap + jQuery + Vue)
2️⃣  DATOS (data: arrays, objetos, variables)
3️⃣  LOGIN (si lo piden)
4️⃣  PANTALLA PRINCIPAL (tabla/rejilla con datos)
5️⃣  BUSCADOR / FILTROS
6️⃣  CRUD (Alta, Baja, Modificación, Visualización)
7️⃣  DETALLE (vista de un elemento con sus sub-elementos)
8️⃣  COLORES por estado
9️⃣  LISTADOS / INFORMES (opcional)
```

---

## 1️⃣ ESTRUCTURA BASE — LO PRIMERO QUE ESCRIBES

> Copia esto siempre al empezar. Sin esto nada funciona.

```html
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Aplicación</title>

  <!-- ✅ Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <!-- ✅ jQuery UI CSS (para datepicker, etc.) -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

  <style>
    /* Tus estilos personalizados aquí */
    [v-cloak] { display: none; }  /* Evita parpadeo de Vue */
  </style>
</head>
<body>

  <!-- ✅ Aquí va toda tu app Vue -->
  <div id="app" v-cloak>
    <!-- CONTENIDO -->
  </div>

  <!-- ✅ Scripts AL FINAL del body, en este ORDEN -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>

  <script>
    new Vue({
      el: '#app',
      data: {
        // TUS DATOS AQUÍ
      },
      methods: {
        // TUS FUNCIONES AQUÍ
      },
      computed: {
        // TUS CALCULADOS AQUÍ
      },
      mounted() {
        // SE EJECUTA AL CARGAR (para jQuery UI, etc.)
      }
    });
  </script>
</body>
</html>
```

---

## 2️⃣ DATA — CÓMO DECLARAR DATOS

> Aquí pones TODOS los datos que va a manejar tu app.

```javascript
data: {
  // ── Pantalla activa (para navegación sin router) ──────────────
  pantallaActual: 'login',   // 'login' | 'clientes' | 'detalle' | 'polizas'

  // ── Login ─────────────────────────────────────────────────────
  loginUsuario: '',
  loginPassword: '',
  usuarioLogueado: null,
  errorLogin: '',

  // ── Tablas de datos ───────────────────────────────────────────
  usuarios: [
    { id: 1, usuario: 'admin', password: '1234', nombre: 'Administrador' },
    { id: 2, usuario: 'user1', password: 'abc',  nombre: 'Usuario Uno'  }
  ],

  clientes: [
    { id: 1, nombre: 'Juan',  apellidos: 'García López',  telefono: '600111222',
      localidad: 'Sevilla', cp: '41001', provincia: 'Sevilla', tipo: 'particular' },
    { id: 2, nombre: 'Acme', apellidos: 'S.L.',            telefono: '954333444',
      localidad: 'Madrid',  cp: '28001', provincia: 'Madrid',  tipo: 'empresa'    }
  ],

  polizas: [
    { id: 1, clienteId: 1, numero: 'POL-001', importe: 500,
      fecha: '2024-01-15', estado: 'cobrada',    observaciones: 'Al día' },
    { id: 2, clienteId: 1, numero: 'POL-002', importe: 300,
      fecha: '2024-03-10', estado: 'anulada',    observaciones: 'Cliente canceló' },
    { id: 3, clienteId: 2, numero: 'POL-003', importe: 800,
      fecha: '2024-05-20', estado: 'a cuenta',   observaciones: 'Pendiente pago' }
  ],

  pagos: [
    { id: 1, polizaId: 1, fecha: '2024-01-15', importe: 200 },
    { id: 2, polizaId: 1, fecha: '2024-02-15', importe: 300 }
  ],

  // ── Formularios (para Alta/Edición) ──────────────────────────
  formCliente: {
    id: null, nombre: '', apellidos: '', telefono: '',
    localidad: '', cp: '', provincia: '', tipo: 'particular'
  },
  formPoliza: {
    id: null, clienteId: null, numero: '', importe: 0,
    fecha: '', estado: 'cobrada', observaciones: ''
  },
  formPago: {
    id: null, polizaId: null, fecha: '', importe: 0
  },

  // ── Buscadores ────────────────────────────────────────────────
  busqueda: '',
  filtroEstado: '',

  // ── Selección actual ─────────────────────────────────────────
  clienteSeleccionado: null,
  polizaSeleccionada: null,

  // ── Modo del formulario ──────────────────────────────────────
  modoFormulario: 'nuevo',   // 'nuevo' | 'editar'

  // ── Otros ────────────────────────────────────────────────────
  mensajeOk: '',
  mensajeError: ''
}
```

---

## 3️⃣ LOGIN — PANTALLA DE ACCESO

### HTML del formulario login

```html
<!-- Mostrar solo si estamos en pantalla login -->
<div v-if="pantallaActual === 'login'" class="container">
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="panel panel-primary" style="margin-top: 80px;">
        <div class="panel-heading">
          <h3 class="panel-title text-center">🔐 Iniciar Sesión</h3>
        </div>
        <div class="panel-body">

          <!-- Mensaje de error -->
          <div v-if="errorLogin" class="alert alert-danger">
            {{ errorLogin }}
          </div>

          <div class="form-group">
            <label>Usuario</label>
            <input type="text" class="form-control"
                   v-model="loginUsuario"
                   placeholder="Introduce tu usuario">
          </div>

          <div class="form-group">
            <label>Contraseña</label>
            <input type="password" class="form-control"
                   v-model="loginPassword"
                   placeholder="Introduce tu contraseña"
                   @keyup.enter="hacerLogin">
          </div>

          <button class="btn btn-primary btn-block" @click="hacerLogin">
            Entrar
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
```

### Método login en Vue

```javascript
methods: {
  hacerLogin() {
    // Buscar el usuario en la tabla
    const encontrado = this.usuarios.find(u =>
      u.usuario === this.loginUsuario &&
      u.password === this.loginPassword
    );

    if (encontrado) {
      this.usuarioLogueado = encontrado;
      this.errorLogin = '';
      this.pantallaActual = 'clientes';  // Ir a la pantalla principal
    } else {
      this.errorLogin = 'Usuario o contraseña incorrectos';
    }
  },

  cerrarSesion() {
    this.usuarioLogueado = null;
    this.loginUsuario = '';
    this.loginPassword = '';
    this.pantallaActual = 'login';
  }
}
```

---

## 4️⃣ NAVBAR — MENÚ DE NAVEGACIÓN

```html
<!-- Solo se muestra si hay usuario logueado -->
<nav class="navbar navbar-default navbar-fixed-top" v-if="usuarioLogueado">
  <div class="container-fluid">
    <div class="navbar-header">
      <span class="navbar-brand">🏢 Agencia de Seguros</span>
    </div>
    <ul class="nav navbar-nav">
      <li :class="{ active: pantallaActual === 'clientes' }">
        <a href="#" @click.prevent="pantallaActual = 'clientes'">Clientes</a>
      </li>
      <li :class="{ active: pantallaActual === 'polizas' }">
        <a href="#" @click.prevent="pantallaActual = 'polizas'">Pólizas</a>
      </li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li>
        <a href="#">👤 {{ usuarioLogueado.nombre }}</a>
      </li>
      <li>
        <a href="#" @click.prevent="cerrarSesion">Cerrar sesión</a>
      </li>
    </ul>
  </div>
</nav>

<!-- Separador para el navbar fixed -->
<div v-if="usuarioLogueado" style="margin-top: 70px;"></div>
```

---

## 5️⃣ TABLA / REJILLA DE CLIENTES (PANTALLA PRINCIPAL)

### HTML de la tabla con buscador

```html
<div v-if="pantallaActual === 'clientes'" class="container-fluid">
  <h2>👥 Listado de Clientes</h2>

  <!-- Barra de herramientas: buscador + botón nuevo -->
  <div class="row" style="margin-bottom: 15px;">
    <div class="col-md-6">
      <div class="input-group">
        <span class="input-group-addon">🔍</span>
        <input type="text" class="form-control"
               v-model="busqueda"
               placeholder="Buscar por nombre, apellidos, localidad...">
      </div>
    </div>
    <div class="col-md-6 text-right">
      <button class="btn btn-success" @click="abrirFormularioNuevo('cliente')">
        ➕ Nuevo Cliente
      </button>
    </div>
  </div>

  <!-- Tabla -->
  <table class="table table-striped table-bordered table-hover">
    <thead class="thead-inverse" style="background-color: #337ab7; color: white;">
      <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Apellidos</th>
        <th>Teléfono</th>
        <th>Localidad</th>
        <th>CP</th>
        <th>Provincia</th>
        <th>Tipo</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <!-- v-for para mostrar cada cliente filtrado -->
      <tr v-for="cliente in clientesFiltrados" :key="cliente.id">
        <td>{{ cliente.id }}</td>
        <td>{{ cliente.nombre }}</td>
        <td>{{ cliente.apellidos }}</td>
        <td>{{ cliente.telefono }}</td>
        <td>{{ cliente.localidad }}</td>
        <td>{{ cliente.cp }}</td>
        <td>{{ cliente.provincia }}</td>
        <td>
          <!-- Badge según tipo -->
          <span class="label label-info" v-if="cliente.tipo === 'empresa'">Empresa</span>
          <span class="label label-default" v-else>Particular</span>
        </td>
        <td>
          <button class="btn btn-xs btn-primary" @click="verDetalle(cliente)">
            👁 Ver
          </button>
          <button class="btn btn-xs btn-warning" @click="editarCliente(cliente)">
            ✏️ Editar
          </button>
          <button class="btn btn-xs btn-danger" @click="eliminarCliente(cliente.id)">
            🗑 Borrar
          </button>
        </td>
      </tr>

      <!-- Fila si no hay resultados -->
      <tr v-if="clientesFiltrados.length === 0">
        <td colspan="9" class="text-center text-muted">
          No se encontraron clientes
        </td>
      </tr>
    </tbody>
  </table>

  <!-- Contador de registros -->
  <p class="text-muted">
    Mostrando {{ clientesFiltrados.length }} de {{ clientes.length }} clientes
  </p>
</div>
```

### Computed para el buscador (filtrado automático)

```javascript
computed: {
  // Filtra clientes según lo que escriba en la búsqueda
  clientesFiltrados() {
    if (!this.busqueda) return this.clientes;

    const busq = this.busqueda.toLowerCase();
    return this.clientes.filter(c =>
      c.nombre.toLowerCase().includes(busq)      ||
      c.apellidos.toLowerCase().includes(busq)   ||
      c.localidad.toLowerCase().includes(busq)   ||
      c.provincia.toLowerCase().includes(busq)   ||
      c.telefono.includes(busq)                  ||
      c.cp.includes(busq)
    );
  }
}
```

---

## 6️⃣ CRUD — ALTA, BAJA, EDICIÓN

### Formulario Modal (Bootstrap)

```html
<!-- Modal para crear/editar cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title">
          {{ modoFormulario === 'nuevo' ? '➕ Nuevo Cliente' : '✏️ Editar Cliente' }}
        </h4>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Nombre *</label>
              <input type="text" class="form-control" v-model="formCliente.nombre">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Apellidos *</label>
              <input type="text" class="form-control" v-model="formCliente.apellidos">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Teléfono</label>
              <input type="text" class="form-control" v-model="formCliente.telefono">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Tipo</label>
              <select class="form-control" v-model="formCliente.tipo">
                <option value="particular">Particular</option>
                <option value="empresa">Empresa</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Localidad</label>
              <input type="text" class="form-control" v-model="formCliente.localidad">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>CP</label>
              <input type="text" class="form-control" v-model="formCliente.cp">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>Provincia</label>
              <input type="text" class="form-control" v-model="formCliente.provincia">
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-success" @click="guardarCliente">
          💾 Guardar
        </button>
      </div>
    </div>
  </div>
</div>
```

### Métodos CRUD en Vue

```javascript
methods: {
  // ── Abrir modal vacío para nuevo ─────────────────────────────
  abrirFormularioNuevo(tipo) {
    this.modoFormulario = 'nuevo';
    // Resetear el formulario
    this.formCliente = {
      id: null, nombre: '', apellidos: '', telefono: '',
      localidad: '', cp: '', provincia: '', tipo: 'particular'
    };
    $('#modalCliente').modal('show');  // Abrir modal con jQuery
  },

  // ── Abrir modal con datos para editar ─────────────────────────
  editarCliente(cliente) {
    this.modoFormulario = 'editar';
    // Copiar datos (¡no asignar directamente para no modificar el original!)
    this.formCliente = { ...cliente };
    $('#modalCliente').modal('show');
  },

  // ── Guardar (decide si crear o actualizar) ────────────────────
  guardarCliente() {
    // Validación básica
    if (!this.formCliente.nombre || !this.formCliente.apellidos) {
      alert('Nombre y Apellidos son obligatorios');
      return;
    }

    if (this.modoFormulario === 'nuevo') {
      // CREAR: busca el ID máximo y suma 1
      const maxId = this.clientes.length > 0
        ? Math.max(...this.clientes.map(c => c.id))
        : 0;
      this.formCliente.id = maxId + 1;
      this.clientes.push({ ...this.formCliente });
    } else {
      // EDITAR: encuentra el índice y reemplaza
      const idx = this.clientes.findIndex(c => c.id === this.formCliente.id);
      if (idx !== -1) {
        // Vue.set para que sea reactivo
        this.$set(this.clientes, idx, { ...this.formCliente });
      }
    }

    $('#modalCliente').modal('hide');  // Cerrar modal
    this.mensajeOk = 'Cliente guardado correctamente';
    setTimeout(() => this.mensajeOk = '', 3000);
  },

  // ── Eliminar ──────────────────────────────────────────────────
  eliminarCliente(id) {
    if (!confirm('¿Seguro que quieres eliminar este cliente?')) return;
    this.clientes = this.clientes.filter(c => c.id !== id);
  },

  // ── Ver detalle ───────────────────────────────────────────────
  verDetalle(cliente) {
    this.clienteSeleccionado = cliente;
    this.pantallaActual = 'detalle';
  }
}
```

---

## 7️⃣ PANTALLA DETALLE — CLIENTE CON SUS PÓLIZAS

```html
<div v-if="pantallaActual === 'detalle' && clienteSeleccionado" class="container-fluid">

  <!-- Botón volver -->
  <button class="btn btn-default" @click="pantallaActual = 'clientes'">
    ← Volver a Clientes
  </button>

  <hr>

  <!-- Datos del cliente -->
  <div class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title">
        👤 {{ clienteSeleccionado.nombre }} {{ clienteSeleccionado.apellidos }}
      </h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-md-3"><strong>Teléfono:</strong> {{ clienteSeleccionado.telefono }}</div>
        <div class="col-md-3"><strong>Localidad:</strong> {{ clienteSeleccionado.localidad }}</div>
        <div class="col-md-3"><strong>CP:</strong> {{ clienteSeleccionado.cp }}</div>
        <div class="col-md-3"><strong>Provincia:</strong> {{ clienteSeleccionado.provincia }}</div>
      </div>
    </div>
  </div>

  <!-- Pólizas del cliente -->
  <h4>📋 Pólizas</h4>
  <button class="btn btn-success btn-sm" @click="nuevaPolizaParaCliente">
    ➕ Nueva Póliza
  </button>

  <table class="table table-bordered" style="margin-top:10px;">
    <thead>
      <tr>
        <th>Nº Póliza</th>
        <th>Importe</th>
        <th>Fecha</th>
        <th>Estado</th>
        <th>Observaciones</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <!-- ⭐ :class para aplicar color según estado -->
      <tr v-for="poliza in polizasDelCliente"
          :key="poliza.id"
          :class="colorFila(poliza.estado)">
        <td>{{ poliza.numero }}</td>
        <td>{{ poliza.importe }} €</td>
        <td>{{ poliza.fecha }}</td>
        <td>
          <span :class="badgeEstado(poliza.estado)">{{ poliza.estado }}</span>
        </td>
        <td>{{ poliza.observaciones }}</td>
        <td>
          <button class="btn btn-xs btn-info" @click="verPagos(poliza)">
            💳 Pagos
          </button>
          <button class="btn btn-xs btn-warning" @click="editarPoliza(poliza)">
            ✏️ Editar
          </button>
          <button class="btn btn-xs btn-danger" @click="eliminarPoliza(poliza.id)">
            🗑 Borrar
          </button>
        </td>
      </tr>
      <tr v-if="polizasDelCliente.length === 0">
        <td colspan="6" class="text-center text-muted">
          Este cliente no tiene pólizas
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

### Computed y métodos para pólizas

```javascript
computed: {
  // Pólizas del cliente seleccionado
  polizasDelCliente() {
    if (!this.clienteSeleccionado) return [];
    return this.polizas.filter(p => p.clienteId === this.clienteSeleccionado.id);
  },

  // Todas las pólizas ordenadas por fecha descendente (pantalla general)
  todasPolizasOrdenadas() {
    return [...this.polizas].sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
  }
},

methods: {
  // ── Color de fila según estado ────────────────────────────────
  colorFila(estado) {
    const colores = {
      'cobrada':     'success',   // Verde
      'a cuenta':    'warning',   // Amarillo
      'liquidada':   'info',      // Azul
      'anulada':     'danger',    // Rojo
      'pre-anulada': 'active'     // Gris
    };
    return colores[estado] || '';
  },

  // ── Badge del estado ──────────────────────────────────────────
  badgeEstado(estado) {
    const badges = {
      'cobrada':     'label label-success',
      'a cuenta':    'label label-warning',
      'liquidada':   'label label-info',
      'anulada':     'label label-danger',
      'pre-anulada': 'label label-default'
    };
    return badges[estado] || 'label label-default';
  }
}
```

---

## 8️⃣ PAGOS — CONTROL DE CUOTAS

```html
<!-- Sección de pagos dentro del detalle de póliza -->
<div v-if="polizaSeleccionada" class="panel panel-default" style="margin-top:20px;">
  <div class="panel-heading">
    <h4>💳 Pagos de la póliza {{ polizaSeleccionada.numero }}</h4>
    <small>
      Total pagado: <strong>{{ totalPagado }} €</strong> /
      Importe póliza: <strong>{{ polizaSeleccionada.importe }} €</strong> —
      Restante: <strong :class="restante < 0 ? 'text-danger' : 'text-success'">
        {{ restante }} €
      </strong>
    </small>
  </div>
  <div class="panel-body">
    <table class="table table-sm table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>Fecha</th>
          <th>Importe</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="pago in pagosDePoliza" :key="pago.id">
          <td>{{ pago.id }}</td>
          <td>{{ pago.fecha }}</td>
          <td>{{ pago.importe }} €</td>
          <td>
            <button class="btn btn-xs btn-danger" @click="eliminarPago(pago.id)">
              🗑
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Formulario añadir pago -->
    <div class="row">
      <div class="col-md-4">
        <input type="date" class="form-control" v-model="formPago.fecha"
               placeholder="Fecha">
      </div>
      <div class="col-md-4">
        <input type="number" class="form-control" v-model.number="formPago.importe"
               placeholder="Importe (€)">
      </div>
      <div class="col-md-4">
        <button class="btn btn-primary" @click="agregarPago">
          ➕ Añadir Pago
        </button>
      </div>
    </div>
  </div>
</div>
```

### Computed y métodos para pagos

```javascript
computed: {
  pagosDePoliza() {
    if (!this.polizaSeleccionada) return [];
    return this.pagos.filter(p => p.polizaId === this.polizaSeleccionada.id);
  },

  totalPagado() {
    return this.pagosDePoliza.reduce((acc, p) => acc + p.importe, 0);
  },

  restante() {
    if (!this.polizaSeleccionada) return 0;
    return this.polizaSeleccionada.importe - this.totalPagado;
  }
},

methods: {
  verPagos(poliza) {
    this.polizaSeleccionada = poliza;
    this.formPago = { id: null, polizaId: poliza.id, fecha: '', importe: 0 };
  },

  agregarPago() {
    // Validar que no nos pasemos del importe
    if (this.formPago.importe <= 0) {
      alert('El importe debe ser mayor que 0');
      return;
    }
    if (this.formPago.importe > this.restante) {
      alert(`No puedes pagar más de ${this.restante} €`);
      return;
    }
    if (!this.formPago.fecha) {
      alert('Debes indicar una fecha');
      return;
    }

    const maxId = this.pagos.length > 0
      ? Math.max(...this.pagos.map(p => p.id))
      : 0;

    this.pagos.push({
      id: maxId + 1,
      polizaId: this.polizaSeleccionada.id,
      fecha: this.formPago.fecha,
      importe: this.formPago.importe
    });

    // Resetear formulario
    this.formPago.fecha = '';
    this.formPago.importe = 0;
  },

  eliminarPago(id) {
    if (!confirm('¿Eliminar este pago?')) return;
    this.pagos = this.pagos.filter(p => p.id !== id);
  }
}
```

---

## 9️⃣ LISTADO GENERAL DE PÓLIZAS (todas ordenadas)

```html
<div v-if="pantallaActual === 'polizas'" class="container-fluid">
  <h2>📋 Todas las Pólizas</h2>

  <!-- Filtro por estado -->
  <div class="row" style="margin-bottom:15px;">
    <div class="col-md-3">
      <select class="form-control" v-model="filtroEstado">
        <option value="">-- Todos los estados --</option>
        <option value="cobrada">Cobrada</option>
        <option value="a cuenta">A cuenta</option>
        <option value="liquidada">Liquidada</option>
        <option value="anulada">Anulada</option>
        <option value="pre-anulada">Pre-anulada</option>
      </select>
    </div>
  </div>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Nº Póliza</th>
        <th>Cliente</th>
        <th>Importe</th>
        <th>Fecha ↓</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="poliza in polizasFiltradas"
          :key="poliza.id"
          :class="colorFila(poliza.estado)">
        <td>{{ poliza.numero }}</td>
        <td>{{ nombreCliente(poliza.clienteId) }}</td>
        <td>{{ poliza.importe }} €</td>
        <td>{{ poliza.fecha }}</td>
        <td>
          <span :class="badgeEstado(poliza.estado)">{{ poliza.estado }}</span>
        </td>
        <td>
          <!-- Ir al cliente de esa póliza -->
          <button class="btn btn-xs btn-primary"
                  @click="irACliente(poliza.clienteId)">
            👤 Ver Cliente
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

### Computed y métodos para la pantalla de pólizas

```javascript
computed: {
  polizasFiltradas() {
    let lista = [...this.polizas].sort((a, b) =>
      new Date(b.fecha) - new Date(a.fecha)  // Descendente
    );
    if (this.filtroEstado) {
      lista = lista.filter(p => p.estado === this.filtroEstado);
    }
    return lista;
  }
},

methods: {
  nombreCliente(clienteId) {
    const c = this.clientes.find(c => c.id === clienteId);
    return c ? `${c.nombre} ${c.apellidos}` : 'Desconocido';
  },

  irACliente(clienteId) {
    this.clienteSeleccionado = this.clientes.find(c => c.id === clienteId);
    this.pantallaActual = 'detalle';
  }
}
```

---

## 🔟 LISTADO/INFORME CON FILTROS (OPCIONAL)

```html
<div v-if="pantallaActual === 'informe'" class="container-fluid">
  <h2>📊 Informe de Clientes y Pólizas</h2>

  <!-- Filtros del informe -->
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="row">
        <div class="col-md-3">
          <label>Cliente desde ID</label>
          <input type="number" class="form-control" v-model.number="informeDesdeId">
        </div>
        <div class="col-md-3">
          <label>Cliente hasta ID</label>
          <input type="number" class="form-control" v-model.number="informeHastaId">
        </div>
        <div class="col-md-3">
          <label>Fecha desde</label>
          <input type="date" class="form-control" v-model="informeFechaDesde">
        </div>
        <div class="col-md-3">
          <label>Fecha hasta</label>
          <input type="date" class="form-control" v-model="informeFechaHasta">
        </div>
      </div>
      <div class="row" style="margin-top:10px;">
        <div class="col-md-3">
          <label>Estado póliza</label>
          <select class="form-control" v-model="informeEstado">
            <option value="">Todas</option>
            <option value="cobrada">Cobrada</option>
            <option value="a cuenta">A cuenta</option>
            <option value="liquidada">Liquidada</option>
            <option value="anulada">Anulada</option>
          </select>
        </div>
        <div class="col-md-3" style="margin-top:25px;">
          <button class="btn btn-primary" @click="generarInforme">
            🔍 Generar Informe
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Resultado del informe -->
  <div v-if="resultadoInforme.length > 0">
    <div v-for="item in resultadoInforme" :key="item.cliente.id" style="margin-bottom:20px;">
      <h4>👤 {{ item.cliente.nombre }} {{ item.cliente.apellidos }}</h4>
      <table class="table table-bordered table-sm">
        <thead>
          <tr>
            <th>Nº Póliza</th>
            <th>Fecha</th>
            <th>Importe</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in item.polizas" :key="p.id"
              :style="p.estado !== 'liquidada' ? 'color: red; font-weight: bold;' : ''">
            <td>{{ p.numero }}</td>
            <td>{{ p.fecha }}</td>
            <td>{{ p.importe }} €</td>
            <td>{{ p.estado }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div v-else class="alert alert-info">
    Usa los filtros y pulsa "Generar Informe"
  </div>
</div>
```

```javascript
data: {
  // ... (añadir a data existente)
  informeDesdeId: 1,
  informeHastaId: 9999,
  informeFechaDesde: '',
  informeFechaHasta: '',
  informeEstado: '',
  resultadoInforme: []
},
methods: {
  generarInforme() {
    // Filtrar clientes por rango de ID
    const clientesFiltrados = this.clientes.filter(c =>
      c.id >= this.informeDesdeId && c.id <= this.informeHastaId
    );

    this.resultadoInforme = clientesFiltrados.map(cliente => {
      // Pólizas de este cliente con filtros de fecha y estado
      let polizas = this.polizas.filter(p => p.clienteId === cliente.id);

      if (this.informeFechaDesde) {
        polizas = polizas.filter(p => p.fecha >= this.informeFechaDesde);
      }
      if (this.informeFechaHasta) {
        polizas = polizas.filter(p => p.fecha <= this.informeFechaHasta);
      }
      if (this.informeEstado) {
        polizas = polizas.filter(p => p.estado === this.informeEstado);
      }

      return { cliente, polizas };
    }).filter(item => item.polizas.length > 0);  // Solo clientes con pólizas
  }
}
```

---

## 🎨 ESTILOS ÚTILES DE BOOTSTRAP 3

### Clases de color para filas de tabla

| Estado | Clase Bootstrap | Color |
|--------|----------------|-------|
| `success` | `class="success"` | 🟢 Verde |
| `warning` | `class="warning"` | 🟡 Amarillo |
| `danger`  | `class="danger"`  | 🔴 Rojo |
| `info`    | `class="info"`    | 🔵 Azul |
| `active`  | `class="active"`  | ⚫ Gris |

### Labels / Badges

```html
<span class="label label-success">Cobrada</span>
<span class="label label-warning">A cuenta</span>
<span class="label label-danger">Anulada</span>
<span class="label label-info">Liquidada</span>
<span class="label label-default">Pre-anulada</span>
```

### Botones

```html
<button class="btn btn-primary">Azul</button>
<button class="btn btn-success">Verde</button>
<button class="btn btn-warning">Naranja</button>
<button class="btn btn-danger">Rojo</button>
<button class="btn btn-default">Gris</button>
<!-- Tamaños -->
<button class="btn btn-primary btn-lg">Grande</button>
<button class="btn btn-primary btn-sm">Pequeño</button>
<button class="btn btn-primary btn-xs">Muy pequeño</button>
<button class="btn btn-primary btn-block">Ancho completo</button>
```

### Grid (sistema de columnas)

```html
<div class="row">
  <div class="col-md-6">Mitad</div>
  <div class="col-md-6">Mitad</div>
</div>
<div class="row">
  <div class="col-md-4">Un tercio</div>
  <div class="col-md-4">Un tercio</div>
  <div class="col-md-4">Un tercio</div>
</div>
<div class="col-md-offset-3 col-md-6"><!-- Centrado --></div>
```

### Alertas

```html
<div class="alert alert-success" v-if="mensajeOk">✅ {{ mensajeOk }}</div>
<div class="alert alert-danger"  v-if="mensajeError">❌ {{ mensajeError }}</div>
<div class="alert alert-info">ℹ️ Información</div>
<div class="alert alert-warning">⚠️ Advertencia</div>
```

### Panel

```html
<div class="panel panel-primary">
  <div class="panel-heading">Título del panel</div>
  <div class="panel-body">Contenido</div>
  <div class="panel-footer">Pie del panel</div>
</div>
```

---

## 🛠️ DIRECTIVAS VUE 2 — LAS MÁS USADAS

```html
<!-- Mostrar/ocultar elemento -->
<div v-if="condicion">Se renderiza o no</div>
<div v-show="condicion">Se oculta con CSS (display:none)</div>
<div v-else-if="otraCondicion">Si no lo anterior</div>
<div v-else>Si nada anterior</div>

<!-- Bucle -->
<tr v-for="item in lista" :key="item.id">
  {{ item.nombre }}
</tr>

<!-- Enlace de datos bidireccional (formularios) -->
<input v-model="nombreVariable">
<input v-model.number="numero">    <!-- Convierte a número -->
<input v-model.trim="texto">       <!-- Elimina espacios -->

<!-- Enlace de atributos HTML -->
<img :src="imagen.url">
<a :href="enlace">texto</a>
<div :class="{ 'clase-css': condicion }">  <!-- clase condicional -->
<div :class="nombreClaseComoString">       <!-- clase dinámica -->
<div :style="{ color: colorVariable }">   <!-- estilo inline -->

<!-- Eventos -->
<button @click="miFuncion">Click</button>
<button @click="miFuncion(parametro)">Click con param</button>
<input @keyup.enter="buscar">       <!-- Enter -->
<input @keyup.esc="cancelar">       <!-- Escape -->
<form @submit.prevent="enviar">     <!-- Evita recarga -->

<!-- Texto/HTML -->
<p>{{ variable }}</p>               <!-- Texto -->
<p v-html="textoConHTML"></p>       <!-- HTML (con cuidado) -->
```

---

## ⚙️ ESTRUCTURA COMPLETA DE LA INSTANCIA VUE

```javascript
new Vue({
  el: '#app',

  // ── DATOS ────────────────────────────────────────────────────
  data: {
    mensaje: 'Hola',
    lista: []
  },

  // ── CALCULADOS (como data pero automáticos) ──────────────────
  // Se recalculan cuando cambian sus dependencias
  computed: {
    listaFiltrada() {
      return this.lista.filter(item => item.activo);
    },
    total() {
      return this.lista.reduce((acc, i) => acc + i.precio, 0);
    }
  },

  // ── MÉTODOS ──────────────────────────────────────────────────
  methods: {
    saludar() {
      alert('Hola desde Vue!');
    },
    saludarA(nombre) {
      alert(`Hola, ${nombre}!`);
    }
  },

  // ── WATCHERS (escucha cambios en variables) ──────────────────
  watch: {
    busqueda(nuevoValor, valorAnterior) {
      console.log('Búsqueda cambió a:', nuevoValor);
      this.buscar();
    }
  },

  // ── CICLO DE VIDA ────────────────────────────────────────────
  created() {
    // Se ejecuta al crear Vue (datos disponibles, DOM no)
    console.log('Vue creado');
  },
  mounted() {
    // Se ejecuta cuando el DOM está listo (usar jQuery aquí)
    $('#miDatepicker').datepicker({ dateFormat: 'yy-mm-dd' });
  }
});
```

---

## 📅 JQUERY UI — DATEPICKER (SELECTOR DE FECHA)

```javascript
mounted() {
  // Activar datepicker en un input con id="fechaInput"
  $('#fechaInput').datepicker({
    dateFormat: 'yy-mm-dd',
    onSelect: (fecha) => {
      this.formPoliza.fecha = fecha;  // Pasar fecha a Vue
    }
  });
}
```

```html
<!-- Input para datepicker -->
<div class="input-group">
  <input type="text" id="fechaInput" class="form-control"
         :value="formPoliza.fecha" placeholder="Selecciona fecha">
  <span class="input-group-addon">📅</span>
</div>
```

---

## 🪟 MODALES BOOTSTRAP — CÓMO ABRIR Y CERRAR

```javascript
// Abrir modal
$('#miModal').modal('show');

// Cerrar modal
$('#miModal').modal('hide');
```

```html
<!-- Botón que abre modal directamente sin Vue -->
<button data-toggle="modal" data-target="#miModal">Abrir</button>

<!-- Estructura modal -->
<div class="modal fade" id="miModal" tabindex="-1">
  <div class="modal-dialog modal-lg"> <!-- modal-sm, modal-lg -->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title">Título</h4>
      </div>
      <div class="modal-body">
        <!-- Contenido -->
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" @click="guardar">Guardar</button>
      </div>
    </div>
  </div>
</div>
```

---

## 🔍 TRUCOS Y PATRONES FRECUENTES

### Buscar en un array

```javascript
// Buscar el primero que cumpla condición → devuelve el objeto o undefined
const cliente = this.clientes.find(c => c.id === id);

// Filtrar → devuelve nuevo array
const activos = this.clientes.filter(c => c.activo === true);

// Comprobar si existe alguno
const existe = this.clientes.some(c => c.nombre === 'Juan');

// Encontrar índice (para editar)
const idx = this.clientes.findIndex(c => c.id === id);
```

### Operaciones con arrays

```javascript
// Añadir al final
this.clientes.push(nuevoCliente);

// Eliminar por filtro
this.clientes = this.clientes.filter(c => c.id !== idAEliminar);

// Editar en posición (reactivo con Vue)
this.$set(this.clientes, indice, clienteEditado);
// o también:
this.clientes.splice(indice, 1, clienteEditado);

// Copiar objeto (para no modificar el original)
const copia = { ...objeto };
const copiaArray = [...array];

// Sumar valores
const total = this.pagos.reduce((acc, p) => acc + p.importe, 0);

// Máximo ID para el siguiente nuevo registro
const maxId = Math.max(...this.clientes.map(c => c.id), 0);
const nuevoId = maxId + 1;
```

### Ordenar arrays

```javascript
// Ascendente por fecha
lista.sort((a, b) => new Date(a.fecha) - new Date(b.fecha));

// Descendente por fecha
lista.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));

// Por número
lista.sort((a, b) => a.id - b.id);

// Por texto
lista.sort((a, b) => a.nombre.localeCompare(b.nombre));
```

### Formatear números como moneda

```javascript
// En métodos o computed:
formatearEuros(cantidad) {
  return cantidad.toFixed(2) + ' €';
}

// En el template:
// {{ formatearEuros(poliza.importe) }}
```

### Mostrar mensaje temporal

```javascript
this.mensajeOk = 'Operación realizada correctamente';
setTimeout(() => { this.mensajeOk = ''; }, 3000);
```

---

## 🧩 COMPONENTE COMPLETO DE EJEMPLO — APLICACIÓN SEGUROS

> Copia esto como punto de partida y modifica los datos según lo que pida el examen.

```html
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agencia de Seguros</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <style>
    [v-cloak] { display: none; }
    body { padding-top: 20px; }
    .cursor-pointer { cursor: pointer; }
  </style>
</head>
<body>
<div id="app" v-cloak>

  <!-- ══════════ LOGIN ══════════ -->
  <div v-if="!usuarioLogueado" class="container">
    <div class="col-md-4 col-md-offset-4">
      <div class="panel panel-primary" style="margin-top:80px;">
        <div class="panel-heading"><h3 class="text-center">🔐 Login</h3></div>
        <div class="panel-body">
          <div v-if="errorLogin" class="alert alert-danger">{{ errorLogin }}</div>
          <div class="form-group">
            <input class="form-control" v-model="loginUsuario" placeholder="Usuario">
          </div>
          <div class="form-group">
            <input type="password" class="form-control" v-model="loginPassword"
                   placeholder="Contraseña" @keyup.enter="hacerLogin">
          </div>
          <button class="btn btn-primary btn-block" @click="hacerLogin">Entrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════ APP PRINCIPAL ══════════ -->
  <div v-else>
    <!-- NAVBAR -->
    <nav class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-header">
          <span class="navbar-brand">🏢 Seguros App</span>
        </div>
        <ul class="nav navbar-nav">
          <li :class="{active: pantalla==='clientes'}">
            <a @click.prevent="pantalla='clientes'" href="#">Clientes</a>
          </li>
          <li :class="{active: pantalla==='polizas'}">
            <a @click.prevent="pantalla='polizas'" href="#">Pólizas</a>
          </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="#">👤 {{ usuarioLogueado.nombre }}</a></li>
          <li><a @click.prevent="cerrarSesion" href="#">Salir</a></li>
        </ul>
      </div>
    </nav>

    <div class="container-fluid">
      <!-- Alertas globales -->
      <div v-if="mensajeOk" class="alert alert-success">✅ {{ mensajeOk }}</div>

      <!-- ── PANTALLA CLIENTES ── -->
      <div v-if="pantalla==='clientes'">
        <div class="row">
          <div class="col-md-8">
            <h3>👥 Clientes</h3>
          </div>
          <div class="col-md-4 text-right" style="margin-top:15px;">
            <button class="btn btn-success" @click="nuevoCliente">➕ Nuevo</button>
          </div>
        </div>

        <!-- Buscador -->
        <div class="form-group">
          <input class="form-control" v-model="busqueda"
                 placeholder="🔍 Buscar por nombre, localidad, teléfono...">
        </div>

        <!-- Tabla -->
        <table class="table table-striped table-bordered table-hover">
          <thead style="background:#337ab7;color:white;">
            <tr>
              <th>ID</th><th>Nombre</th><th>Apellidos</th><th>Teléfono</th>
              <th>Localidad</th><th>Provincia</th><th>Tipo</th><th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in clientesFiltrados" :key="c.id">
              <td>{{ c.id }}</td>
              <td>{{ c.nombre }}</td>
              <td>{{ c.apellidos }}</td>
              <td>{{ c.telefono }}</td>
              <td>{{ c.localidad }}</td>
              <td>{{ c.provincia }}</td>
              <td>
                <span class="label label-info" v-if="c.tipo==='empresa'">Empresa</span>
                <span class="label label-default" v-else>Particular</span>
              </td>
              <td>
                <button class="btn btn-xs btn-primary" @click="verCliente(c)">👁</button>
                <button class="btn btn-xs btn-warning" @click="editCliente(c)">✏️</button>
                <button class="btn btn-xs btn-danger" @click="delCliente(c.id)">🗑</button>
              </td>
            </tr>
            <tr v-if="!clientesFiltrados.length">
              <td colspan="8" class="text-center text-muted">Sin resultados</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- ── PANTALLA DETALLE CLIENTE ── -->
      <div v-if="pantalla==='detalle' && clienteActual">
        <button class="btn btn-default" @click="pantalla='clientes'">← Volver</button>
        <hr>
        <div class="panel panel-info">
          <div class="panel-heading">
            <h4>{{ clienteActual.nombre }} {{ clienteActual.apellidos }}</h4>
          </div>
          <div class="panel-body">
            Tel: {{ clienteActual.telefono }} |
            {{ clienteActual.localidad }}, {{ clienteActual.provincia }}
          </div>
        </div>

        <h4>📋 Pólizas
          <button class="btn btn-success btn-sm" @click="nuevaPoliza">➕ Nueva</button>
        </h4>
        <table class="table table-bordered">
          <thead><tr>
            <th>Nº</th><th>Importe</th><th>Fecha</th><th>Estado</th><th>Obs.</th><th>Acc.</th>
          </tr></thead>
          <tbody>
            <tr v-for="p in polizasCliente" :key="p.id" :class="colorFila(p.estado)">
              <td>{{ p.numero }}</td>
              <td>{{ p.importe }}€</td>
              <td>{{ p.fecha }}</td>
              <td><span :class="badgeEstado(p.estado)">{{ p.estado }}</span></td>
              <td>{{ p.observaciones }}</td>
              <td>
                <button class="btn btn-xs btn-info" @click="verPagos(p)">💳</button>
                <button class="btn btn-xs btn-warning" @click="editPoliza(p)">✏️</button>
                <button class="btn btn-xs btn-danger" @click="delPoliza(p.id)">🗑</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagos de la póliza seleccionada -->
        <div v-if="polizaActual" class="panel panel-default">
          <div class="panel-heading">
            💳 Pagos — {{ polizaActual.numero }} |
            Pagado: {{ totalPagado }}€ / {{ polizaActual.importe }}€
            (Restante: <strong>{{ restante }}€</strong>)
          </div>
          <div class="panel-body">
            <table class="table table-sm table-bordered">
              <thead><tr><th>Fecha</th><th>Importe</th><th></th></tr></thead>
              <tbody>
                <tr v-for="pg in pagosPoliza" :key="pg.id">
                  <td>{{ pg.fecha }}</td>
                  <td>{{ pg.importe }}€</td>
                  <td><button class="btn btn-xs btn-danger" @click="delPago(pg.id)">🗑</button></td>
                </tr>
              </tbody>
            </table>
            <div class="row">
              <div class="col-md-4">
                <input type="date" class="form-control" v-model="fPago.fecha">
              </div>
              <div class="col-md-4">
                <input type="number" class="form-control" v-model.number="fPago.importe"
                       placeholder="Importe €">
              </div>
              <div class="col-md-4">
                <button class="btn btn-primary" @click="addPago">➕ Añadir Pago</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ── PANTALLA TODAS LAS PÓLIZAS ── -->
      <div v-if="pantalla==='polizas'">
        <h3>📋 Todas las Pólizas</h3>
        <select class="form-control" style="width:200px;margin-bottom:10px;" v-model="filtroEstado">
          <option value="">-- Todos --</option>
          <option>cobrada</option><option>a cuenta</option>
          <option>liquidada</option><option>anulada</option><option>pre-anulada</option>
        </select>
        <table class="table table-bordered">
          <thead><tr>
            <th>Nº Póliza</th><th>Cliente</th><th>Importe</th>
            <th>Fecha ↓</th><th>Estado</th><th>Acc.</th>
          </tr></thead>
          <tbody>
            <tr v-for="p in polizasFiltradas" :key="p.id" :class="colorFila(p.estado)">
              <td>{{ p.numero }}</td>
              <td>{{ nombreCliente(p.clienteId) }}</td>
              <td>{{ p.importe }}€</td>
              <td>{{ p.fecha }}</td>
              <td><span :class="badgeEstado(p.estado)">{{ p.estado }}</span></td>
              <td>
                <button class="btn btn-xs btn-primary" @click="irCliente(p.clienteId)">
                  👤 Cliente
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ══════════ MODALES ══════════ -->

  <!-- Modal Cliente -->
  <div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button class="close" data-dismiss="modal">×</button>
          <h4 class="modal-title">{{ modo==='nuevo'?'Nuevo':'Editar' }} Cliente</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Nombre *</label>
                <input class="form-control" v-model="fCliente.nombre">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Apellidos *</label>
                <input class="form-control" v-model="fCliente.apellidos">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Teléfono</label>
                <input class="form-control" v-model="fCliente.telefono">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Tipo</label>
                <select class="form-control" v-model="fCliente.tipo">
                  <option value="particular">Particular</option>
                  <option value="empresa">Empresa</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Localidad</label>
                <input class="form-control" v-model="fCliente.localidad">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>CP</label>
                <input class="form-control" v-model="fCliente.cp">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Provincia</label>
                <input class="form-control" v-model="fCliente.provincia">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button class="btn btn-success" @click="saveCliente">💾 Guardar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Póliza -->
  <div class="modal fade" id="modalPoliza" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button class="close" data-dismiss="modal">×</button>
          <h4 class="modal-title">{{ modo==='nuevo'?'Nueva':'Editar' }} Póliza</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nº Póliza</label>
            <input class="form-control" v-model="fPoliza.numero">
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Importe (€)</label>
                <input type="number" class="form-control" v-model.number="fPoliza.importe">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Fecha</label>
                <input type="date" class="form-control" v-model="fPoliza.fecha">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Estado</label>
            <select class="form-control" v-model="fPoliza.estado">
              <option value="cobrada">Cobrada</option>
              <option value="a cuenta">A cuenta</option>
              <option value="liquidada">Liquidada</option>
              <option value="anulada">Anulada</option>
              <option value="pre-anulada">Pre-anulada</option>
            </select>
          </div>
          <div class="form-group">
            <label>Observaciones</label>
            <textarea class="form-control" v-model="fPoliza.observaciones" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button class="btn btn-success" @click="savePoliza">💾 Guardar</button>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script>
new Vue({
  el: '#app',
  data: {
    pantalla: 'clientes',
    loginUsuario: '', loginPassword: '', errorLogin: '',
    usuarioLogueado: null,
    busqueda: '', filtroEstado: '',
    modo: 'nuevo',
    mensajeOk: '',
    clienteActual: null,
    polizaActual: null,

    usuarios: [
      { id:1, usuario:'admin', password:'1234', nombre:'Administrador' }
    ],
    clientes: [
      { id:1, nombre:'Juan', apellidos:'García', telefono:'600111', localidad:'Sevilla', cp:'41001', provincia:'Sevilla', tipo:'particular' },
      { id:2, nombre:'Acme', apellidos:'S.L.',   telefono:'954222', localidad:'Madrid',  cp:'28001', provincia:'Madrid',  tipo:'empresa'    }
    ],
    polizas: [
      { id:1, clienteId:1, numero:'POL-001', importe:500, fecha:'2024-01-10', estado:'cobrada',  observaciones:'' },
      { id:2, clienteId:1, numero:'POL-002', importe:300, fecha:'2024-03-15', estado:'anulada',  observaciones:'Cancelada' },
      { id:3, clienteId:2, numero:'POL-003', importe:800, fecha:'2024-06-01', estado:'a cuenta', observaciones:'' }
    ],
    pagos: [
      { id:1, polizaId:1, fecha:'2024-01-15', importe:200 },
      { id:2, polizaId:1, fecha:'2024-02-15', importe:300 }
    ],
    fCliente:{ id:null, nombre:'', apellidos:'', telefono:'', localidad:'', cp:'', provincia:'', tipo:'particular' },
    fPoliza: { id:null, clienteId:null, numero:'', importe:0, fecha:'', estado:'cobrada', observaciones:'' },
    fPago:   { polizaId:null, fecha:'', importe:0 }
  },

  computed: {
    clientesFiltrados() {
      if (!this.busqueda) return this.clientes;
      const b = this.busqueda.toLowerCase();
      return this.clientes.filter(c =>
        c.nombre.toLowerCase().includes(b) || c.apellidos.toLowerCase().includes(b) ||
        c.localidad.toLowerCase().includes(b) || c.telefono.includes(b)
      );
    },
    polizasCliente() {
      if (!this.clienteActual) return [];
      return this.polizas.filter(p => p.clienteId === this.clienteActual.id);
    },
    polizasFiltradas() {
      let l = [...this.polizas].sort((a,b) => new Date(b.fecha)-new Date(a.fecha));
      if (this.filtroEstado) l = l.filter(p => p.estado === this.filtroEstado);
      return l;
    },
    pagosPoliza() {
      if (!this.polizaActual) return [];
      return this.pagos.filter(p => p.polizaId === this.polizaActual.id);
    },
    totalPagado() {
      return this.pagosPoliza.reduce((a,p) => a + p.importe, 0);
    },
    restante() {
      if (!this.polizaActual) return 0;
      return this.polizaActual.importe - this.totalPagado;
    }
  },

  methods: {
    // LOGIN
    hacerLogin() {
      const u = this.usuarios.find(u => u.usuario===this.loginUsuario && u.password===this.loginPassword);
      if (u) { this.usuarioLogueado = u; this.errorLogin = ''; }
      else this.errorLogin = 'Credenciales incorrectas';
    },
    cerrarSesion() {
      this.usuarioLogueado = null;
      this.loginUsuario = this.loginPassword = '';
    },

    // COLORES
    colorFila(e) {
      return { cobrada:'success','a cuenta':'warning',liquidada:'info',anulada:'danger','pre-anulada':'active' }[e]||'';
    },
    badgeEstado(e) {
      return { cobrada:'label label-success','a cuenta':'label label-warning',liquidada:'label label-info',anulada:'label label-danger','pre-anulada':'label label-default' }[e]||'label label-default';
    },

    // CLIENTES
    nuevoCliente() {
      this.modo = 'nuevo';
      this.fCliente = { id:null, nombre:'', apellidos:'', telefono:'', localidad:'', cp:'', provincia:'', tipo:'particular' };
      $('#modalCliente').modal('show');
    },
    editCliente(c) {
      this.modo = 'editar';
      this.fCliente = { ...c };
      $('#modalCliente').modal('show');
    },
    saveCliente() {
      if (!this.fCliente.nombre || !this.fCliente.apellidos) { alert('Nombre y Apellidos son obligatorios'); return; }
      if (this.modo === 'nuevo') {
        this.fCliente.id = Math.max(0, ...this.clientes.map(c=>c.id)) + 1;
        this.clientes.push({ ...this.fCliente });
      } else {
        const i = this.clientes.findIndex(c => c.id === this.fCliente.id);
        this.$set(this.clientes, i, { ...this.fCliente });
      }
      $('#modalCliente').modal('hide');
      this.ok('Cliente guardado');
    },
    delCliente(id) {
      if (!confirm('¿Eliminar cliente?')) return;
      this.clientes = this.clientes.filter(c => c.id !== id);
    },
    verCliente(c) { this.clienteActual = c; this.polizaActual = null; this.pantalla = 'detalle'; },
    irCliente(cId) { this.verCliente(this.clientes.find(c=>c.id===cId)); },
    nombreCliente(id) { const c=this.clientes.find(c=>c.id===id); return c?`${c.nombre} ${c.apellidos}`:'?'; },

    // PÓLIZAS
    nuevaPoliza() {
      this.modo = 'nuevo';
      this.fPoliza = { id:null, clienteId:this.clienteActual.id, numero:'', importe:0, fecha:'', estado:'cobrada', observaciones:'' };
      $('#modalPoliza').modal('show');
    },
    editPoliza(p) {
      this.modo = 'editar';
      this.fPoliza = { ...p };
      $('#modalPoliza').modal('show');
    },
    savePoliza() {
      if (!this.fPoliza.numero) { alert('El número de póliza es obligatorio'); return; }
      if (this.modo === 'nuevo') {
        this.fPoliza.id = Math.max(0, ...this.polizas.map(p=>p.id)) + 1;
        this.polizas.push({ ...this.fPoliza });
      } else {
        const i = this.polizas.findIndex(p => p.id === this.fPoliza.id);
        this.$set(this.polizas, i, { ...this.fPoliza });
      }
      $('#modalPoliza').modal('hide');
      this.ok('Póliza guardada');
    },
    delPoliza(id) {
      if (!confirm('¿Eliminar póliza?')) return;
      this.polizas = this.polizas.filter(p => p.id !== id);
      if (this.polizaActual && this.polizaActual.id === id) this.polizaActual = null;
    },

    // PAGOS
    verPagos(p) { this.polizaActual = p; this.fPago = { polizaId:p.id, fecha:'', importe:0 }; },
    addPago() {
      if (this.fPago.importe <= 0) { alert('Importe inválido'); return; }
      if (this.fPago.importe > this.restante) { alert(`Máximo: ${this.restante}€`); return; }
      if (!this.fPago.fecha) { alert('Indica una fecha'); return; }
      const id = Math.max(0, ...this.pagos.map(p=>p.id)) + 1;
      this.pagos.push({ id, ...this.fPago });
      this.fPago.fecha = ''; this.fPago.importe = 0;
      this.ok('Pago añadido');
    },
    delPago(id) {
      if (!confirm('¿Eliminar pago?')) return;
      this.pagos = this.pagos.filter(p => p.id !== id);
    },

    // HELPER
    ok(msg) { this.mensajeOk = msg; setTimeout(()=>this.mensajeOk='', 3000); }
  }
});
</script>
</body>
</html>
```

---

## 📌 RESUMEN RÁPIDO — CHECKLIST EXAMEN

### Al leer el enunciado, identifica:

- [ ] ¿Hay login? → Añade tabla `usuarios`, método `hacerLogin`, formulario login
- [ ] ¿Hay una entidad principal? (clientes, productos...) → Tabla + CRUD + buscador
- [ ] ¿Esa entidad tiene sub-entidades? (pólizas, pedidos...) → pantalla detalle + tabla anidada
- [ ] ¿Las sub-entidades tienen colores por estado? → método `colorFila()` con `:class`
- [ ] ¿Hay una pantalla de todas las sub-entidades? → computed ordenado
- [ ] ¿Hay cuotas/pagos? → computed `totalPagado`, `restante`, validación al guardar
- [ ] ¿Hay informes/filtros? → formulario de filtros + computed o método que genera el array resultado

### Plantilla mental para cualquier entidad nueva

```
1. Añade su array en data: []
2. Añade su objeto formulario: fX: {}
3. Crea los métodos: nuevo/editar/save/delete + colorFila si tiene estados
4. Crea el computed de filtrado si hay buscador
5. Crea el HTML: tabla + modal
```

---

*✅ Con esta chuleta puedes hacer cualquier práctica de Vue 2 + Bootstrap + jQuery desde cero.*