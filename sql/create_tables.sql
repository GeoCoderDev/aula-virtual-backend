DROP DATABASE virtual_classroom_b;
CREATE DATABASE virtual_classroom_b;
use virtual_classroom_b;

CREATE TABLE IF NOT EXISTS empleado (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    sueldo decimal(20,6) NOT NULL
);

-- Tabla de Superadministradores
CREATE TABLE IF NOT EXISTS T_Superadmin (
    Id_Superadmin INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Usuario VARCHAR(255) NOT NULL,
    Contraseña VARCHAR(255) NOT NULL
);

-- Tabla de Administradores
CREATE TABLE IF NOT EXISTS T_Administradores (
    Id_Admin INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Usuario VARCHAR(255) NOT NULL,
    Contraseña VARCHAR(255) NOT NULL
);

-- Tabla de Usuarios
CREATE TABLE IF NOT EXISTS T_Usuarios (
    Id_Usuario INT PRIMARY KEY AUTO_INCREMENT,
    Nombres VARCHAR(255) NOT NULL,
    Apellidos VARCHAR(255) NOT NULL,
    Fecha_Nacimiento DATE,
    Nombre_Usuario VARCHAR(255) NOT NULL,
    Contraseña_Usuario VARCHAR(255) NOT NULL,
    Direccion_Domicilio VARCHAR(255),
    Telefono VARCHAR(20) DEFAULT '-',
    Nombre_Contacto_Emergencia VARCHAR(255),
    Parentezco_Contacto_Emergencia VARCHAR(255),
    Telefono_Contacto_Emergencia VARCHAR(20),
    Foto_Perfil_Key_S3 VARCHAR(255) DEFAULT NULL,
    Estado TINYINT NOT NULL DEFAULT 1 CHECK (Estado >= 0 AND Estado <= 2)
);


-- Tabla de Profesores
CREATE TABLE IF NOT EXISTS T_Profesores (
    DNI_Profesor VARCHAR(20) PRIMARY KEY,
    Id_Usuario INT,
    FOREIGN KEY (Id_Usuario) REFERENCES T_Usuarios(Id_Usuario)
);

-- Tabla de Aulas
CREATE TABLE IF NOT EXISTS T_Aulas (
    Id_Aula INT PRIMARY KEY AUTO_INCREMENT,
    Grado VARCHAR(50) NOT NULL,
    Seccion VARCHAR(10) NOT NULL
);

-- Tabla de Estudiantes
CREATE TABLE IF NOT EXISTS T_Estudiantes (
    DNI_Estudiante VARCHAR(20) PRIMARY KEY,
    Id_Usuario INT,
    Id_Aula INT,
    FOREIGN KEY (Id_Usuario) REFERENCES T_Usuarios(Id_Usuario),
    FOREIGN KEY (Id_Aula) REFERENCES T_Aulas(Id_Aula)
);

-- Tabla de Cursos
CREATE TABLE IF NOT EXISTS T_Cursos (
    Id_Curso INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(255) NOT NULL
);

-- Tabla de Cursos-Aula
CREATE TABLE IF NOT EXISTS T_Cursos_Aula (
    Id_Curso_Aula INT PRIMARY KEY AUTO_INCREMENT,
    Id_Curso INT,
    Id_Aula INT,
    FOREIGN KEY (Id_Curso) REFERENCES T_Cursos(Id_Curso),
    FOREIGN KEY (Id_Aula) REFERENCES T_Aulas(Id_Aula)
);

-- Tabla de Temas
CREATE TABLE IF NOT EXISTS T_Temas (
    Id_Tema INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Tema VARCHAR(255) NOT NULL,
    Id_Curso_Aula INT,
    Num_Orden INT,
    FOREIGN KEY (Id_Curso_Aula) REFERENCES T_Cursos_Aula(Id_Curso_Aula)
);

-- Tabla de Horario de Curso-Aula
CREATE TABLE IF NOT EXISTS T_Horario_Curso_Aula (
    Id_Horario_Curso_Aula INT PRIMARY KEY AUTO_INCREMENT,
    Id_Curso_Aula INT,
    Dia_Semana VARCHAR(20) NOT NULL,
    Hora_Inicio TIME NOT NULL,
    Cant_Horas_Academicas INT NOT NULL,
    FOREIGN KEY (Id_Curso_Aula) REFERENCES T_Cursos_Aula(Id_Curso_Aula)
);

-- Tabla de Asignaciones
CREATE TABLE IF NOT EXISTS T_Asignaciones (
    Id_Asignacion INT PRIMARY KEY AUTO_INCREMENT,
    DNI_Profesor VARCHAR(20),
    Id_Horario_Curso_Aula INT,
    FOREIGN KEY (DNI_Profesor) REFERENCES T_Profesores(DNI_Profesor),
    FOREIGN KEY (Id_Horario_Curso_Aula) REFERENCES T_Horario_Curso_Aula(Id_Horario_Curso_Aula)
);

-- Tabla de Configuraciones de la aplicacion
CREATE TABLE T_Configuraciones (
    Id_Conf INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Conf VARCHAR(255) NOT NULL UNIQUE,
    Valor TEXT NOT NULL,
    Descripcion TEXT NULL,
    Ult_Actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


