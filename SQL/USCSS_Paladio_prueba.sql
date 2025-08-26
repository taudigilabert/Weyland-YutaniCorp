DROP DATABASE IF EXISTS USCSS_Paladio;
CREATE DATABASE USCSS_Paladio;
USE USCSS_Paladio;

-- Tabla Roles
CREATE TABLE IF NOT EXISTS roles (
    rol_id INT AUTO_INCREMENT PRIMARY KEY,
    rol_nombre VARCHAR(100) NOT NULL UNIQUE,
    rol_descripcion TEXT
);

-- Tabla Nave
CREATE TABLE IF NOT EXISTS nave (
    nav_id INT AUTO_INCREMENT PRIMARY KEY,
    nav_nombre VARCHAR(100) NOT NULL,
    nav_tipo VARCHAR(50),
    nav_descripcion TEXT
);

-- Tabla Usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    usu_id INT AUTO_INCREMENT PRIMARY KEY,
    usu_nombre VARCHAR(100) NOT NULL,
    usu_apellido VARCHAR(100) NOT NULL,
    usu_alias VARCHAR(100) UNIQUE NOT NULL,
    rol_id INT NOT NULL,
    usu_genero ENUM('Masculino', 'Femenino', 'Otro'),
    usu_biografia TEXT,
    usu_imagen VARCHAR(255),
    usu_idnave INT,
    usu_numero_empleado INT UNIQUE NOT NULL,
    usu_fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    usu_contrasena VARCHAR(255) NOT NULL,
    usu_activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (rol_id) REFERENCES roles(rol_id) ON DELETE CASCADE,
    FOREIGN KEY (usu_idnave) REFERENCES nave(nav_id) ON DELETE SET NULL
);

-- Tabla Informes Usuario
CREATE TABLE IF NOT EXISTS informes_usuario (
    inf_id INT AUTO_INCREMENT PRIMARY KEY,
    usu_id INT NOT NULL,
    inf_concepto VARCHAR(500) NOT NULL,
    inf_contenido TEXT NOT NULL,
    inf_fecha DATE NOT NULL,
    inf_estado ENUM('abierto', 'en progreso', 'archivado') DEFAULT 'abierto',
    FOREIGN KEY (usu_id) REFERENCES usuarios(usu_id) ON DELETE CASCADE
);

-- Tabla Imágenes de Informes
CREATE TABLE IF NOT EXISTS informe_imagenes (
    img_id INT AUTO_INCREMENT PRIMARY KEY,
    inf_id INT NOT NULL,
    img_ruta VARCHAR(255) NOT NULL,
    FOREIGN KEY (inf_id) REFERENCES informes_usuario(inf_id) ON DELETE CASCADE
);

-- Tabla Mensajes
CREATE TABLE IF NOT EXISTS mensajes (
    men_id INT AUTO_INCREMENT PRIMARY KEY,
    men_remitente INT NOT NULL,
    men_contenido TEXT,
    men_fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    rol_id INT DEFAULT NULL,
    FOREIGN KEY (men_remitente) REFERENCES usuarios(usu_id) ON DELETE CASCADE,
    FOREIGN KEY (rol_id) REFERENCES roles(rol_id) ON DELETE SET NULL
);

-- Tabla Receptores de Mensajes
CREATE TABLE IF NOT EXISTS mensaje_receptores (
    mec_id INT AUTO_INCREMENT PRIMARY KEY,
    men_id INT NOT NULL,
    mec_receptor INT NOT NULL,
    mec_leido BOOLEAN DEFAULT FALSE,
    mec_notificacion BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (men_id) REFERENCES mensajes(men_id) ON DELETE CASCADE,
    FOREIGN KEY (mec_receptor) REFERENCES usuarios(usu_id) ON DELETE CASCADE
);

-- Tabla Imágenes de Mensajes
CREATE TABLE IF NOT EXISTS mensajes_imagenes (
    mimg_id INT AUTO_INCREMENT PRIMARY KEY,
    men_id INT NOT NULL,
    mimg_ruta VARCHAR(255) NOT NULL,
    FOREIGN KEY (men_id) REFERENCES mensajes(men_id) ON DELETE CASCADE
);

-- Tabla Registro Médico
CREATE TABLE IF NOT EXISTS registro_medico (
    rem_id INT AUTO_INCREMENT PRIMARY KEY,
    usu_id_medico INT NOT NULL,
    usu_id_paciente INT NOT NULL,
    rem_fecha DATE NOT NULL,
    rem_contenido TEXT NOT NULL,
    rem_actualizado DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usu_id_medico) REFERENCES usuarios(usu_id) ON DELETE CASCADE,
    FOREIGN KEY (usu_id_paciente) REFERENCES usuarios(usu_id) ON DELETE CASCADE

);

-- Tabla Registro de Movimientos
CREATE TABLE IF NOT EXISTS registro_movimientos (
    reg_id INT AUTO_INCREMENT PRIMARY KEY,
    usu_id INT NOT NULL,
    reg_accion VARCHAR(100) NOT NULL,
    reg_fecha DATE NOT NULL,
    reg_hora TIME NOT NULL,
    men_id INT DEFAULT NULL,
    inf_id INT DEFAULT NULL,
    rem_id INT DEFAULT NULL,
    FOREIGN KEY (usu_id) REFERENCES usuarios(usu_id) ON DELETE CASCADE,
    FOREIGN KEY (men_id) REFERENCES mensajes(men_id) ON DELETE SET NULL,
    FOREIGN KEY (inf_id) REFERENCES informes_usuario(inf_id) ON DELETE SET NULL,
    FOREIGN KEY (rem_id) REFERENCES registro_medico(rem_id) ON DELETE SET NULL
);

-- Tabla Imágenes de Registro Médico
CREATE TABLE IF NOT EXISTS registro_imagenes (
    rei_id INT AUTO_INCREMENT PRIMARY KEY,
    rem_id INT NOT NULL,
    rei_ruta_imagen VARCHAR(500) NOT NULL,
    FOREIGN KEY (rem_id) REFERENCES registro_medico(rem_id) ON DELETE CASCADE
);

-- ============================ INDEX ============================
CREATE INDEX idx_usuario_alias ON usuarios(usu_alias);
CREATE INDEX idx_informe_fecha ON informes_Usuario(inf_fecha);

-- ============================ INSERTS ============================
-- Naves
INSERT INTO nave (nav_nombre, nav_tipo, nav_descripcion) VALUES
('USCSS Paladio', 'Investigación', 'Nave utilizada para experimentos biológicos y operaciones científicas.'),
('USCSS Nostromo', 'Carguero', 'Nave comercial dedicada al transporte de carga interestelar.'),
('USS Sulaco', 'Militar', 'Fragata de combate clase Conestoga, utilizada para operaciones militares.'),
('USCSS Prometheus', 'Exploración', 'Nave de exploración científica avanzada de Weyland Corp.'),
('USM Auriga', 'Investigación', 'Nave militar utilizada para experimentos biológicos.'),
('Sevastopol', 'Estación Espacial', 'Estación orbital equipada para comercio y soporte de vida prolongado.');

-- Roles
INSERT INTO roles (rol_nombre, rol_descripcion) VALUES
('capitan', 'Líder de la tripulación, responsable de todas las operaciones a bordo.'),
('primer_oficial', 'Segundo al mando, apoya al capitán en las operaciones.'),
('ingeniero_jefe', 'Responsable del mantenimiento y operación de los sistemas de la nave.'),
('oficial_seguridad', 'Encargado de la seguridad de la nave y la tripulación.'),
('ing_mantenimiento', 'Técnico responsable del mantenimiento de sistemas y equipo.'),
('piloto', 'Encargado de la navegación y control de la nave en vuelo.'),
('oficial_medico', 'Responsable de la salud de la tripulación y del manejo de registros médicos.'),
('cientifico_principal', 'Líder de la investigación científica a bordo.');


-- Usuarios
INSERT INTO usuarios (usu_nombre, usu_apellido, usu_alias, rol_id, usu_genero, usu_biografia, usu_imagen, usu_idnave, usu_numero_empleado, usu_contrasena)
VALUES
('James', 'Kirk', 'capitan_kirk', 1, 'Masculino', 'Capitán de la nave Paladio, siempre al frente de la misión.', 'img/usuarios/kirk.jpg', 1, 34553, '123'),
('Spock', 'Sarek', 'primer_spock', 2, 'Masculino', 'Primer oficial lógico y analítico, fundamental para la misión.', 'img/usuarios/spock.jpg', 1, 5677, '123'),
('Leonard', 'McCoy', 'doc_mccoy', 7, 'Masculino', 'Oficial médico responsable de la salud de la tripulación.', 'img/usuarios/mccoy.jpg', 1, 874545, '123'),
('Le', 'onard', 'leo', 7, 'Masculino', 'Médico responsable de la salud de la tripulación.', 'img/usuarios/mccoy.jpg', 1, 8745345, '123');

-- MENSAJE 1 A 1
-- Mensaje de Kirk a Spock
INSERT INTO mensajes (men_remitente, men_contenido)
VALUES (1, 'Spock, necesito tu análisis sobre el planeta.');

-- Asignar receptor (Spock)
INSERT INTO mensaje_receptores (men_id, mec_receptor, mec_leido, mec_notificacion)
VALUES (1, 2, FALSE, TRUE);



-- MENSAJE 1 A VARIOS (2 receptores)
-- Mensaje de Kirk a Spock y McCoy
INSERT INTO mensajes (men_remitente, men_contenido)
VALUES (1, 'Spock, McCoy, preparen los informes para la reunión de la tarde.');

-- Asignar receptores (Spock y McCoy)
INSERT INTO mensaje_receptores (men_id, mec_receptor, mec_leido, mec_notificacion)
VALUES
(2, 2, FALSE, TRUE),
(2, 3, FALSE, TRUE);


-- MENSAJE de ADMIN a ROL COMPLETO
-- Mensaje del Administrador a todos los médicos
INSERT INTO mensajes (men_remitente, men_contenido, rol_id)
VALUES (1, 'Todos los oficiales médicos, envíen sus reportes diarios.', 7);

-- Asignar receptor (rol completo: oficial_medico)
INSERT INTO mensaje_receptores (men_id, mec_receptor, mec_leido, mec_notificacion)
VALUES (3, 3, FALSE, TRUE);

INSERT INTO mensaje_receptores (men_id, mec_receptor, mec_leido, mec_notificacion)
VALUES (3, 4, FALSE, TRUE);



-- ============================ INSERTS DE PRUEBA ============================

-- Tabla Informes Usuario
INSERT INTO informes_usuario (usu_id, inf_concepto, inf_contenido, inf_fecha, inf_estado) VALUES
(1, 'Mantenimiento del motor principal', 'Revisión completa del sistema de propulsión.', '2025-01-10', 'en progreso'),
(2, 'Análisis del planeta', 'Informe detallado sobre la composición del planeta explorado.', '2025-01-15', 'abierto'),
(3, 'Estado médico de la tripulación', 'Examen médico anual completo de todos los tripulantes.', '2025-01-20', 'archivado');

-- Tabla Imágenes de Informes
INSERT INTO informe_imagenes (inf_id, img_ruta) VALUES
(1, 'img/informes/motor_principal.jpg'),
(2, 'img/informes/planeta_analisis.jpg'),
(3, 'img/informes/estado_medico.jpg');

-- Tabla Imágenes de Mensajes
INSERT INTO mensajes_imagenes (men_id, mimg_ruta) VALUES
(1, 'img/mensajes/planeta.jpg'),
(2, 'img/mensajes/reunion_preparativos.jpg'),
(3, 'img/mensajes/reportes_diarios.jpg');

-- Tabla Registro Médico
INSERT INTO registro_medico (usu_id_medico, usu_id_paciente, rem_fecha, rem_contenido) VALUES
(3, 1, '2025-01-10', 'Examen completo del capitán. Estado de salud óptimo.'),
(3, 2, '2025-01-15', 'Revisión médica del primer oficial. Sin anomalías detectadas.'),
(3, 3, '2025-01-20', 'Informe médico de autogenerado para seguimiento personal.');

-- Tabla Imágenes de Registro Médico
INSERT INTO registro_imagenes (rem_id, rei_ruta_imagen) VALUES
(1, 'img/registros/capitan_examen.jpg'),
(2, 'img/registros/primer_oficial.jpg'),
(3, 'img/registros/personal.jpg');

-- Tabla Registro de Movimientos
INSERT INTO registro_movimientos (usu_id, reg_accion, reg_fecha, reg_hora, men_id, inf_id, rem_id) VALUES
(1, 'Envió mensaje a Spock', '2025-01-10', '10:00:00', 1, NULL, NULL),
(2, 'Subió informe de análisis', '2025-01-15', '12:30:00', NULL, 2, NULL),
(3, 'Actualizó registro médico', '2025-01-20', '15:00:00', NULL, NULL, 3);

-- ============================ DATOS GENERADOS ============================
-- Estos inserts generan un escenario de prueba funcional para las tablas que no tenían datos.
