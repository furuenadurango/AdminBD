-- Crear base de datos
CREATE DATABASE IF NOT EXISTS quehaypahacer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quehaypahacer;

-- 1. Tabla de Roles
CREATE TABLE IF NOT EXISTS Roles (
    id_rol INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Inserción de roles básicos (Ignorar si ya existen)
INSERT IGNORE INTO Roles (nombre) VALUES ('Superadministrador'), ('Dueño de Comercio'), ('Usuario Final');

-- 2. Tabla de Usuarios
CREATE TABLE IF NOT EXISTS Usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_rol) REFERENCES Roles(id_rol) ON DELETE RESTRICT
);

-- 3. Tabla de Categorías (Sirve para Comercio y Turismo)
CREATE TABLE IF NOT EXISTS Categorias (
    id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('Comercio', 'Turismo') NOT NULL,
    descripcion TEXT
);

-- 4. Tabla de Comercios
CREATE TABLE IF NOT EXISTS Comercios (
    id_comercio INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    id_usuario_propietario INT NOT NULL,
    id_categoria INT NOT NULL,
    estado ENUM('Pendiente', 'Aprobado', 'Rechazado') DEFAULT 'Pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario_propietario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES Categorias(id_categoria) ON DELETE RESTRICT
);

-- 5. Tabla de Turismo (Actividades, Planes, Eventos)
CREATE TABLE IF NOT EXISTS Turismo (
    id_actividad INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    ubicacion VARCHAR(255) NOT NULL,
    precio_estimado DECIMAL(10,2),
    id_usuario_proveedor INT NOT NULL,
    id_categoria INT NOT NULL,
    estado ENUM('Pendiente', 'Aprobado', 'Rechazado') DEFAULT 'Pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario_proveedor) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES Categorias(id_categoria) ON DELETE RESTRICT
);

-- 6. Tabla de Auditoría (Para control del Superadministrador)
CREATE TABLE IF NOT EXISTS Auditoria (
    id_auditoria INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario_admin INT NOT NULL,
    accion VARCHAR(255) NOT NULL,
    tabla_afectada VARCHAR(50) NOT NULL,
    registro_id INT NOT NULL,
    fecha_accion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario_admin) REFERENCES Usuarios(id_usuario)
);
