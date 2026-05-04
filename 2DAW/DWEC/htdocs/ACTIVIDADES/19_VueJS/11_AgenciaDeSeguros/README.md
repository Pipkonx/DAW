# 🛡️ Proyecto Aeterna Seguros - Guía Completa de Desarrollo

Esta guía explica paso a paso cómo construir esta aplicación desde cero. Es ideal para entender cómo conectar una base de datos MySQL con un frontend en Vue.js a través de PHP.

---

## 1️⃣ Fase 1: La Base de Datos (MySQL)
Lo primero es tener dónde guardar los datos.

1.  **Crea la base de datos**: En PHPMyAdmin, crea una llamada `aeternaseguros`.
2.  **Crea las tablas**: Define qué información quieres guardar. Por ejemplo, para **Asegurados**:
    - `id` (INT, Auto Increment, Primary Key)
    - `nombre`, `apellidos`, `tlf` (VARCHAR)
3.  **Exporta el SQL**: Guarda siempre tu estructura en un archivo `.sql`.

---

## 2️⃣ Fase 2: La Conexión (PHP)
Necesitas un archivo central que hable con la base de datos.

- **Archivo**: `php/config/conexion.php`
- **Contenido**:
```php
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "aeternaseguros";

// Conexión usando mysqli
$conn = new mysqli($host, $user, $pass, $db);

// Configurar para que acepte tildes y Ñ
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
```

---

## 3️⃣ Fase 3: El Backend (CRUD en PHP)
Para cada tabla, necesitas archivos que realicen las acciones. Vamos a ver el ejemplo de **Asegurados**.

### A. Mostrar datos (Leer)
- **Archivo**: `php/usuarios/GETusuario.php`
- **Lógica**: Haz un `SELECT *`, guarda el resultado en un array y muéstralo como JSON.
```php
include '../config/conexion.php';
$sql = "SELECT * FROM usuarios";
$res = $conn->query($sql);
$usuarios = [];
while($row = $res->fetch_assoc()) { $usuarios[] = $row; }
echo json_encode($usuarios);
```

### B. Agregar datos (Crear)
- **Archivo**: `php/usuarios/addUser.php`
- **Lógica**: Recibe los datos por `POST` (JSON), haz un `INSERT`.
```php
$data = json_decode(file_get_contents("php://input"));
$sql = "INSERT INTO usuarios (nombre, apellidos) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $data->nombre, $data->apellidos);
$stmt->execute();
echo json_encode(["status" => "success"]);
```

### C. Modificar datos (Editar)
- **Archivo**: `php/usuarios/modificarUser.php`
- **Lógica**: Igual que agregar, pero usando `UPDATE ... WHERE id = ?`.

### D. Borrar datos (Eliminar)
- **Archivo**: `php/usuarios/deleteUsuario.php`
- **Lógica**: Recibe el `id` por la URL (`$_GET['id']`) y haz un `DELETE`.

---

## 4️⃣ Fase 4: La Lógica Frontend (Javascript + Vue.js)
En el archivo `js/utiles.js` controlamos el comportamiento.

1.  **Variables (`data`)**: Crea variables para guardar lo que viene del PHP.
    ```javascript
    usuarios: [],
    usuario: { id: '', nombre: '' }, // Objeto para el formulario
    mostrarForm: false
    ```
2.  **Funciones (`methods`)**:
    - **Listar**: Llama al `GETusuario.php` y guarda el resultado en `this.usuarios`.
    - **Guardar**: Envía `this.usuario` al archivo `addUser.php` usando `fetch`.
    - **Borrar**: Envía el ID al archivo `deleteUsuario.php`.

**TRUCO**: Usa una función central `peticion(url, opciones)` para no repetir el código de `fetch` en cada método.

---

## 5️⃣ Fase 5: La Interfaz (HTML)
Es la cara de la aplicación. Usamos CDNs de **Bootstrap** para el diseño.

1.  **Listar**: Usa un `v-for` en una tabla para repetir las filas automáticamente.
    ```html
    <tr v-for="u in usuarios">
        <td>{{ u.nombre }}</td>
        <td><button @click="cargarParaEditar(u)">Editar</button></td>
    </tr>
    ```
2.  **Formulario**: Usa `v-model` en los inputs. Esto hace que lo que escribas se guarde automáticamente en el objeto `usuario` de Javascript.
    ```html
    <input type="text" v-model="usuario.nombre">
    ```
3.  **Sin Modales**: Usa `v-if="mostrarForm"` en un `div` con clase `card` de Bootstrap. Así el formulario solo aparece cuando el usuario pulsa "Añadir".

---

## 💡 Resumen para el desarrollador
1.  **¿Quieres añadir una tabla?** Empieza por el SQL.
2.  **¿Quieres que el botón funcione?** Crea el archivo PHP, luego el método en JS y finalmente pon el `@click` en el HTML.
3.  **¿No se ven los datos?** Revisa la consola del navegador (F12) y asegúrate de que el PHP está devolviendo un JSON válido.

¡Con esta estructura puedes crear cualquier aplicación de gestión! 🚀

---

## 6️⃣ Fase 6: SQL Avanzado (El truco del COALESCE)
A veces, al pedir totales a la base de datos (como la suma de pagos), si no hay registros, SQL nos devuelve un valor nulo (`NULL`). Esto puede romper nuestro código en Javascript.

- **¿Qué es COALESCE?**: Es una función que devuelve el primer valor no nulo de una lista.
- **Ejemplo**: `COALESCE(SUM(importe), 0)`
  - Si hay pagos, devuelve la suma.
  - Si no hay pagos, devuelve `0`.
- **Uso en el proyecto**: Lo usamos en las consultas de pólizas para saber cuánto se ha pagado en total sin que falle si la póliza es nueva.

---

## 7️⃣ Fase 7: Simplificación de Código (PHP Básico)
Para que el programa sea más fácil de mantener y entender, hemos aplicado varias simplificaciones:

1.  **Eliminación de `try-catch`**: En entornos de aprendizaje o aplicaciones sencillas, podemos prescindir de ellos para que el código sea más lineal y corto.
2.  **Recepción de Datos Directa**: Usamos una lógica simple para detectar si los datos vienen por formulario normal (`$_POST`) o por JSON (`file_get_contents`).
    ```php
    $data = $_POST ?: json_decode(file_get_contents('php://input'), true);
    ```
3.  **PDO sin complicaciones**: Configuramos la conexión para que sea directa, permitiendo que los errores se vean de forma natural durante el desarrollo.

---

## 8️⃣ Tipos de Usuarios en el Sistema
Es importante no confundir los dos tipos de "usuarios" que tiene la base de datos:

1.  **Asegurados (Tabla `usuarios`)**: Son los clientes a los que les hacemos las pólizas. Tienen datos como nombre, apellidos, teléfono y dirección.
2.  **Usuarios del Sistema (Tabla `logins`)**: Son los que pueden entrar a la aplicación.
    
    **Credenciales por defecto:**
    | Tipo | Usuario | Contraseña |
    | :--- | :--- | :--- |
    | **Administrador** | `admin` | `admin` |
    | **Normal / Empleado** | `empleado1` | `1234` |

---

## 🛠️ Tecnologías utilizadas
- **Frontend**: Vue.js (CDN), Bootstrap 5, Javascript Moderno (Fetch API).
- **Backend**: PHP 8 (PDO y MySQLi).
- **Base de Datos**: MariaDB / MySQL.


