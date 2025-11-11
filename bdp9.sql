-- Crear base de datos
CREATE DATABASE IF NOT EXISTS practica9;
USE practica9;

--Tabla Paises
CREATE TABLE IF NOT EXISTS Paises (
    IdPais INT NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(100) NOT NULL,
    PRIMARY KEY (IdPais)    
);

--Tabla Estilos
CREATE TABLE IF NOT EXISTS Estilos(
    IdEstilo INT NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(100) NOT NULL,
    Descripcion TEXT,
    Fichero VARCHAR(100),
    PRIMARY KEY (IdEstilo)
);

--Tabla TiposAnuncios
CREATE TABLE IF NOT EXISTS TiposAnuncios(
    IdTAnuncio SMALLINT NOT NULL AUTO_INCREMENT,
    NomTAnuncio VARCHAR(100) NOT NULL,
    PRIMARY KEY (IdTAnuncio)
);

--Tabla TiposViviendas
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

--Tabla Anuncios
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

--Tabla Fotos
CREATE TABLE IF NOT EXISTS Fotos(
    IdFoto INT NOT NULL AUTO_INCREMENT,
    Titulo VARCHAR(100),
    Foto VARCHAR(100),
    Alternativo VARCHAR(250) NOT NULL,
    Anuncio INT,
    PRIMARY KEY (IdFoto),
    FOREIGN KEY(Anuncio) REFERENCES Anuncios(IdAnuncio) ON DELETE SET NULL ON UPDATE CASCADE
);

--Tabla Solicitudes
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

--Tabla TiposMensajes
CREATE TABLE IF NOT EXISTS TiposMensajes(
    IdTMensaje SMALLINT NOT NULL AUTO_INCREMENT,
    NomTMensaje VARCHAR(100) NOT NULL,
    PRIMARY KEY (IdTMensaje)
);

--Tabla Mensajes
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
