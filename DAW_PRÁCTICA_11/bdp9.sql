-- Crear base de datos
CREATE DATABASE IF NOT EXISTS practica9;
USE practica9;

-- Tabla Paises
CREATE TABLE IF NOT EXISTS Paises (
    IdPais INT NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(100) NOT NULL,
    PRIMARY KEY (IdPais)    
);

-- Tabla Estilos
CREATE TABLE IF NOT EXISTS Estilos(
    IdEstilo INT NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(100) NOT NULL,
    Descripcion TEXT,
    Fichero VARCHAR(100),
    PRIMARY KEY (IdEstilo)
);

-- Tabla TiposAnuncios
CREATE TABLE IF NOT EXISTS TiposAnuncios(
    IdTAnuncio SMALLINT NOT NULL AUTO_INCREMENT,
    NomTAnuncio VARCHAR(100) NOT NULL,
    PRIMARY KEY (IdTAnuncio)
);

-- Tabla TiposViviendas
CREATE TABLE IF NOT EXISTS TiposViviendas(
    IdTVivienda SMALLINT NOT NULL AUTO_INCREMENT,
    NomTVivienda VARCHAR(100) NOT NULL,
    PRIMARY KEY (IdTVivienda)
);

-- Tabla Usuarios
CREATE TABLE IF NOT EXISTS Usuarios (
    IdUsuario INT NOT NULL AUTO_INCREMENT,
    NomUsuario VARCHAR(15) NOT NULL UNIQUE,
    Clave VARCHAR(15),
    Email VARCHAR(254),
    Sexo SMALLINT,
    FNacimiento DATE,
    Ciudad VARCHAR(100),
    Pais INT,
    Foto VARCHAR(250),
    FRegistro DATETIME,
    Estilo INT,
    PRIMARY KEY (IdUsuario),
    FOREIGN KEY(Pais) REFERENCES Paises(IdPais) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY(Estilo) REFERENCES Estilos(IdEstilo) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Tabla Anuncios
CREATE TABLE IF NOT EXISTS Anuncios(
    IdAnuncio INT NOT NULL AUTO_INCREMENT,
    TAnuncio SMALLINT,
    TVivienda SMALLINT,
    FPrincipal VARCHAR(100),
    Alternativo VARCHAR(100) NOT NULL,
    Titulo VARCHAR(200),
    Precio DECIMAL(10,2),
    Texto TEXT,
    Ciudad VARCHAR(100),
    Pais INT,
    Superficie DECIMAL(10,2),
    NHabitaciones INT,
    NBanyos INT,
    Planta INT,
    Anyo INT,
    FRegistro DATETIME,
    Usuario INT,
    PRIMARY KEY (IdAnuncio),
    FOREIGN KEY(TAnuncio) REFERENCES TiposAnuncios(IdTAnuncio) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY(TVivienda) REFERENCES TiposViviendas(IdTVivienda) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY(Pais) REFERENCES Paises(IdPais) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY(Usuario) REFERENCES Usuarios(IdUsuario) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Tabla Fotos
CREATE TABLE IF NOT EXISTS Fotos(
    IdFoto INT NOT NULL AUTO_INCREMENT,
    Titulo VARCHAR(100),
    Foto VARCHAR(100),
    Alternativo VARCHAR(250) NOT NULL,
    Anuncio INT,
    PRIMARY KEY (IdFoto),
    FOREIGN KEY(Anuncio) REFERENCES Anuncios(IdAnuncio) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Tabla Solicitudes
CREATE TABLE IF NOT EXISTS Solicitudes(
    IdSolicitud INT NOT NULL AUTO_INCREMENT,
    Anuncio INT,
    Texto VARCHAR(4000),
    Nombre VARCHAR(200),
    Email VARCHAR(254),
    Direccion VARCHAR(300),
    Telefono VARCHAR(20),
    Color VARCHAR(100),
    Copias INT,
    Resolucion INT,
    Fecha DATE,
    IColor BOOLEAN,
    IPrecio BOOLEAN,
    FRegistro DATETIME,
    Coste DECIMAL(10,2),
    PRIMARY KEY (IdSolicitud),
    FOREIGN KEY(Anuncio) REFERENCES Anuncios(IdAnuncio) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Tabla TiposMensajes
CREATE TABLE IF NOT EXISTS TiposMensajes(
    IdTMensaje SMALLINT NOT NULL AUTO_INCREMENT,
    NomTMensaje VARCHAR(100) NOT NULL,
    PRIMARY KEY (IdTMensaje)
);

-- Tabla Mensajes
CREATE TABLE IF NOT EXISTS Mensajes(
    IdMensaje INT NOT NULL AUTO_INCREMENT,
    TMensaje SMALLINT,
    Texto VARCHAR(4000),
    Anuncio INT,
    UsuOrigen INT,
    UsuDestino INT,
    FRegistro DATETIME,
    PRIMARY KEY (IdMensaje),
    FOREIGN KEY(TMensaje) REFERENCES TiposMensajes(IdTMensaje) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY(Anuncio) REFERENCES Anuncios(IdAnuncio) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY(UsuOrigen) REFERENCES Usuarios(IdUsuario) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY(UsuDestino) REFERENCES Usuarios(IdUsuario) ON DELETE SET NULL ON UPDATE CASCADE
);

-- INSERT EN PAISES
INSERT INTO Paises(Nombre) VALUES
('España'),
('México'),
('Argentina'),
('Chile'),
('Colombia'),
('Estados Unidos'),
('Francia'),
('Alemania'),
('Italia'),
('Reino Unido'),
('Portugal'),
('Perú'),
('Uruguay'),
('Venezuela'),
('Brasil'),
('Canadá'),
('Japón'),
('China'),
('Australia'),
('Suiza');

-- INSERT EN TIPOS DE ANUNCIOS
INSERT INTO TiposAnuncios (NomTAnuncio) VALUES
('Venta'),
('Alquiler');

-- INSERT EN TIPOS DE VIVIENDAS
INSERT INTO TiposViviendas (NomTVivienda) VALUES
('Piso'),
('Casa'),
('Chalet'),
('Apartamento'),
('Estudio'),
('Dúplex'),
('Ático');

-- INSERTS EN ESTILOS
INSERT INTO Estilos(Nombre, Descripcion, Fichero) VALUES
('Oscuro', 'Estilo más oscuro', 'css/oscuro.css'),
('Clásico', 'Estilo clásico y elegante', 'css/clasico.css'),
('Rústico', 'Estilo rústico y cálido', 'css/rustico.css');

-- INSERTS EN TiposMensajes
INSERT INTO TiposMensajes(NomTMensaje) VALUES
('Más información'), ('Solicitar una cita'), ('Comunicar una oferta');

-- INSERTS EN Usuarios
INSERT INTO Usuarios(NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, FRegistro, Estilo)
VALUES 
('juan', '1234', 'juan@email.com', 1, '1990-05-15', 'Madrid', 1, 'img/usuario1.jpg', NOW(), 1),
('maria', 'abcd', 'maria@email.com', 2, '1985-11-30', 'Barcelona', 1, 'img/usuario2.jpg', NOW(), 2);

-- INSERTS EN Anuncios
INSERT INTO Anuncios(TAnuncio, TVivienda, FPrincipal, Alternativo, Titulo, Precio, Texto, Ciudad, Pais, Superficie, NHabitaciones, NBanyos, Planta, Anyo, FRegistro, Usuario)
VALUES
(1, 2, 'img/piso1.jpg', 'img/piso1_alt.jpg', 'Piso moderno en Madrid', 250000.00, 'Piso luminoso con 3 habitaciones y 2 baños.', 'Madrid', 1, 85.50, 3, 2, 2, 2018, NOW(), 1),
(2, 3, 'img/oficina1.jpg', 'img/oficina1_alt.jpg', 'Oficina en alquiler en Valencia', 1200.00, 'Oficina céntrica con 100m2 y buena iluminación.', 'Valencia', 1, 100.00, 0, 1, 1, 2015, NOW(), 2);

-- INSERTS EN Fotos
INSERT INTO Fotos(Titulo, Foto, Alternativo, Anuncio)
VALUES
('Salón', 'img/piso1_salon.jpg', 'Salón amplio y luminoso', 1),
('Cocina', 'img/piso1_cocina.jpg', 'Cocina equipada', 1),
('Oficina entrada', 'img/oficina1_entrada.jpg', 'Entrada oficina', 2);

-- INSERTS EN Solicitudes
INSERT INTO Solicitudes(Anuncio, Texto, Nombre, Email, Direccion, Telefono, Color, Copias, Resolucion, Fecha, IColor, IPrecio, FRegistro, Coste)
VALUES
(1, 'Me interesa este piso, ¿puedo visitarlo?', 'Carlos', 'carlos@email.com', 'Calle Falsa 123, Madrid', '600123456', 'Blanco', 1, 300, '2025-11-01', 1, 1, NOW(), 250000.00);

-- INSERTS EN Mensajes
INSERT INTO Mensajes(TMensaje, Texto, Anuncio, UsuOrigen, UsuDestino, FRegistro)
VALUES
(1, 'Hola, ¿me puedes dar más información sobre el piso?', 1, 2, 1, NOW()),
(2, 'Quiero solicitar una cita para ver la oficina', 2, 1, 2, NOW());
