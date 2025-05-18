-- db.sql

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS visitantes 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos creada
USE visitantes;

-- Eliminar tabla antigua si existe (opcional)
-- DROP TABLE IF EXISTS visitante;

-- Crear la tabla 'visitante' con campos divididos
CREATE TABLE IF NOT EXISTS visitante (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    lugar_nacimiento CHAR(2) NOT NULL, -- Ej: DF, AG, GT...
    direccion_actual VARCHAR(255) NOT NULL,
    sexo ENUM('H', 'M') NOT NULL, -- H = Hombre, M = Mujer
    correo_electronico VARCHAR(255) NOT NULL UNIQUE,
    curp VARCHAR(18) NOT NULL,
    rfc VARCHAR(13) NOT NULL,
    fecha_registro DATETIME NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;