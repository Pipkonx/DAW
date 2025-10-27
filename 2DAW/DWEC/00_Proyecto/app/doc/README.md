
---

## 🧱 Paso 1 — Base de datos (MySQL)

> Genera el script SQL para crear la base de datos `finanzas_db` con las siguientes tablas:
>
> - **usuarios**  
>   Campos:  
>   `id (PK)`, `nombre`, `email (único)`, `password_hash`, `fecha_registro`
>
> - **finanzas**  
>   Campos:  
>   `id (PK)`, `id_usuario (FK)`, `tipo (‘ingreso’ o ‘gasto’)`, `monto`, `descripcion`, `fecha_registro`

---

## ⚙️ Paso 2 — Conexión a la base de datos

> Crea `/config/database.php` para conectar con MySQL usando PDO, manejo de errores con try/catch y soporte UTF-8.

---

## 👤 Paso 3 — Modelo de Usuario (`Usuario.php`)

> Funciones requeridas:
> - `registrarUsuario($nombre, $email, $password)`
> - `iniciarSesion($email, $password)`
> - `obtenerUsuarioPorId($id)`

---

## 💰 Paso 4 — Modelo de Finanzas (`Finanzas.php`)

> Funciones requeridas:
> - `registrarMovimiento($idUsuario, $tipo, $monto, $descripcion, $fecha)`
> - `obtenerMovimientosPorUsuario($idUsuario)`
> - `obtenerResumenAnual($idUsuario, $anio)` → Retorna ingresos totales, gastos totales y ahorro.

---

## 🧩 Paso 5 — Controladores

> **UsuarioController.php:**  
> Maneja registro, inicio y cierre de sesión (sesiones seguras).
>
> **FinanzasController.php:**  
> Maneja registro de ingresos/gastos, obtención de datos vía AJAX, y comunicación con los modelos.

---

## 🎨 Paso 6 — Vistas HTML

> Crea las siguientes vistas con diseño limpio (usa CSS minimalista y responsivo):
> - **login.php:** formulario de acceso.  
> - **registro.php:** formulario de registro.  
> - **dashboard.php:**  
>   - Formulario para agregar ingreso o gasto.  
>   - Tabla dinámica de movimientos.  
>   - Gráficos de ahorro/gastos (Chart.js).  

---

## 🧭 Paso 7 — JavaScript (Frontend)

> Crea los archivos:
>
> **`main.js`**
> - Valida formularios (registro, login, ingresos/gastos).  
> - Envía datos con `fetch()` (POST) a los controladores PHP.  
> - Actualiza dinámicamente las tablas y gráficos sin recargar.  
>
> **`graficos.js`**
> - Usa **Chart.js** para mostrar:
>   - Gráfico de barras de ingresos vs gastos mensuales.  
>   - Gráfico de pastel de distribución de ahorro vs gasto.  
> - Estilo minimalista: colores suaves, tipografía “Poppins” o “Inter”.

---

## 🔐 Paso 8 — Seguridad y validaciones

> - Encripta contraseñas con `password_hash()` y verifica con `password_verify()`.  
> - Usa sesiones para restringir acceso al dashboard.  
> - Escapa y valida todas las entradas del usuario (PHP y JS).  
> - Implementa tokens CSRF en formularios si es posible.  

---

## 📘 Paso 9 — Archivo README de instalación

> Debe incluir:
> - Pasos para crear la base de datos.  
> - Configuración de `/config/database.php`.  
> - Requisitos: PHP 8+, MySQL 5.7+, servidor local (XAMPP, Laragon).  
>
> Ejemplo de conexión:
> ```php
> $host = "localhost";
> $dbname = "finanzas_db";
> $user = "root";
> $password = "";
> ```

---

## 🚀 Resultado esperado

Una aplicación funcional donde el usuario puede:

1. Registrarse e iniciar sesión.  
2. Agregar ingresos mensuales y gastos diarios.  
3. Ver su historial financiero y gráficos dinámicos.  
4. Analizar cuánto ahorra y gasta al año, de forma visual y clara.  
5. Interactuar sin recargar la página gracias a JavaScript y Fetch API.  
6. Disfrutar una interfaz **minimalista, moderna y responsiva**.

---

## 🧩 Instrucción final

> “Genera el código completo de este proyecto paso a paso (empezando por la base de datos y configuración), siguiendo el patrón MVC e incluyendo el uso de JavaScript para validaciones, AJAX y gráficos dinámicos.”

---

📌 **Autor del prompt:**  
_Generador MVC para aplicación de finanzas personales con PHP + JS_
