-- Estructura de la base de datos para la Agencia de Seguros
DROP DATABASE IF EXISTS agencia_seguros_new;
CREATE DATABASE IF NOT EXISTS agencia_seguros_new;
USE agencia_seguros_new;

-- Tabla de Usuarios para Login
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Tabla de Clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(10) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    localidad VARCHAR(100),
    cp VARCHAR(10),
    provincia VARCHAR(100),
    tipo ENUM('Particular', 'Empresa') DEFAULT 'Particular'
);

-- Tabla de Pólizas
CREATE TABLE IF NOT EXISTS polizas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    numero VARCHAR(20) NOT NULL UNIQUE,
    importe DECIMAL(10, 2) NOT NULL,
    fecha DATE NOT NULL,
    estado ENUM('Cobrada', 'A cuenta', 'Liquidada', 'Anulada', 'Pre-anulada') DEFAULT 'Cobrada',
    observaciones TEXT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- Tabla de Pagos Fraccionados
CREATE TABLE IF NOT EXISTS pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poliza_id INT,
    fecha DATE NOT NULL,
    importe DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (poliza_id) REFERENCES polizas(id) ON DELETE CASCADE
);

-- Inserción de datos de prueba
INSERT INTO usuarios (username, password) VALUES 
('admin', '123'),
('user', 'user');

INSERT INTO clientes (codigo, nombre, telefono, localidad, cp, provincia, tipo) VALUES 
('C001', 'Juan Pérez', '600111222', 'Madrid', '28001', 'Madrid', 'Particular'),
('C002', 'Seguros S.A.', '912334455', 'Barcelona', '08001', 'Barcelona', 'Empresa'),
('C003', 'María García', '655443322', 'Valencia', '46001', 'Valencia', 'Particular'),
('C004', 'Tech Solutions', '934556677', 'Sevilla', '41001', 'Sevilla', 'Empresa');

INSERT INTO polizas (cliente_id, numero, importe, fecha, estado, observaciones) VALUES 
(1, 'POL-001', 500.00, '2023-10-01', 'Cobrada', 'Pago puntual'),
(1, 'POL-002', 1200.00, '2023-12-15', 'A cuenta', 'Pendiente segundo pago'),
(2, 'POL-003', 3000.00, '2024-01-10', 'Liquidada', 'Todo en regla'),
(3, 'POL-004', 150.00, '2023-08-20', 'Anulada', 'Cliente no renovó'),
(4, 'POL-005', 800.00, '2024-02-01', 'Pre-anulada', 'Falta documentación');

INSERT INTO pagos (poliza_id, fecha, importe) VALUES 
(2, '2023-12-16', 400.00),
(2, '2023-12-20', 200.00);
