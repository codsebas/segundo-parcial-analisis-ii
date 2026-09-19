-- ==========================================================
-- Sistema de Gestión de Citas Médicas
-- Base de datos: clinica_db
-- ==========================================================

CREATE DATABASE IF NOT EXISTS clinica_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clinica_db;

-- ----------------------------------------------------------
-- 1. Tabla: pacientes
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(25),
    email VARCHAR(120),
    fecha_nacimiento DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 2. Tabla: doctores
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS doctores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    especialidad VARCHAR(100) NOT NULL,
    telefono VARCHAR(25),
    email VARCHAR(120),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 3. Tabla: citas
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    doctor_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    motivo TEXT NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada', 'atendida') NOT NULL DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_citas_paciente FOREIGN KEY (paciente_id) 
        REFERENCES pacientes(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_citas_doctor FOREIGN KEY (doctor_id) 
        REFERENCES doctores(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_citas_doctor_fecha (doctor_id, fecha),
    INDEX idx_citas_paciente (paciente_id),
    INDEX idx_citas_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- 4. Datos Semilla (Seeds)
-- ==========================================================

-- Doctores iniciales
INSERT INTO doctores (id, nombre, especialidad, telefono, email) VALUES
(1, 'Dr. Alejandro Morales', 'Medicina General', '+502 5551-1001', 'amorales@clinica.com'),
(2, 'Dra. Beatriz Castillo', 'Cardiología', '+502 5552-2002', 'bcastillo@clinica.com'),
(3, 'Dr. Carlos Mendoza', 'Pediatría', '+502 5553-3003', 'cmendoza@clinica.com'),
(4, 'Dra. Diana Herrera', 'Dermatología', '+502 5554-4004', 'dherrera@clinica.com'),
(5, 'Dr. Eduardo Vásquez', 'Odontología', '+502 5555-5005', 'evasquez@clinica.com')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- Pacientes iniciales
INSERT INTO pacientes (id, nombre, telefono, email, fecha_nacimiento) VALUES
(1, 'Juan Pérez Gómez', '+502 4441-1111', 'jperez@gmail.com', '1988-04-12'),
(2, 'María Fernanda López', '+502 4442-2222', 'mflopez@gmail.com', '1995-09-23'),
(3, 'Roberto Carlos Soto', '+502 4443-3333', 'rsoto@gmail.com', '1982-11-05'),
(4, 'Lucía Gabriela Méndez', '+502 4444-4444', 'lmendez@gmail.com', '2001-02-18'),
(5, 'Esteban Ramos Cruz', '+502 4445-5555', 'eramos@gmail.com', '1976-07-30'),
(6, 'Sofía Isabel Reyes', '+502 4446-6666', 'sreyes@gmail.com', '1999-12-14')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- Citas médicas iniciales (referenciadas a fechas relativas para visualización inmediata en calendario)
INSERT INTO citas (id, paciente_id, doctor_id, fecha, hora_inicio, hora_fin, motivo, estado) VALUES
(1, 1, 1, CURDATE(), '08:00:00', '08:45:00', 'Chequeo general anual y toma de presión', 'confirmada'),
(2, 2, 2, CURDATE(), '09:00:00', '10:00:00', 'Evaluación de electrocardiograma de control', 'pendiente'),
(3, 3, 3, CURDATE(), '10:30:00', '11:15:00', 'Control pediátrico y esquema de vacunación', 'atendida'),
(4, 4, 4, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', '09:45:00', 'Consulta dermatológica por dermatitis atópica', 'confirmada'),
(5, 5, 5, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '11:00:00', '12:00:00', 'Limpieza dental profunda y profilaxis', 'pendiente'),
(6, 6, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', '14:45:00', 'Revisión de resultados de laboratorio', 'pendiente'),
(7, 1, 2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '15:00:00', '16:00:00', 'Cita cancelada por motivos de viaje del paciente', 'cancelada'),
(8, 2, 3, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '16:00:00', '16:45:00', 'Valoración pediátrica de crecimiento', 'confirmada')
ON DUPLICATE KEY UPDATE motivo = VALUES(motivo);
