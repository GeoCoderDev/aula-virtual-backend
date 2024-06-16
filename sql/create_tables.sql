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
    Nombre_Usuario VARCHAR(255) NOT NULL UNIQUE,
    Contraseña_Usuario VARCHAR(255) NOT NULL,
    Direccion_Domicilio VARCHAR(255),
    Telefono VARCHAR(20) DEFAULT '-',
    Nombre_Contacto_Emergencia VARCHAR(255),
    Parentezco_Contacto_Emergencia VARCHAR(255),
    Telefono_Contacto_Emergencia VARCHAR(20) DEFAULT '-',
    Foto_Perfil_Key_S3 VARCHAR(255) DEFAULT NULL,
    Estado TINYINT NOT NULL DEFAULT 1 CHECK (Estado >= 0 AND Estado <= 2)
);


-- Tabla de Profesores
CREATE TABLE IF NOT EXISTS T_Profesores (
    DNI_Profesor VARCHAR(20) PRIMARY KEY,
    Id_Usuario INT,
    FOREIGN KEY (Id_Usuario) REFERENCES T_Usuarios(Id_Usuario)
);


-- Tabla de Estudiantes
CREATE TABLE IF NOT EXISTS T_Estudiantes (
    DNI_Estudiante VARCHAR(20) PRIMARY KEY,
    Id_Usuario INT,
    Id_Aula INT,
    FOREIGN KEY (Id_Usuario) REFERENCES T_Usuarios(Id_Usuario),
    FOREIGN KEY (Id_Aula) REFERENCES T_Aulas(Id_Aula)
);

-- ============================================================
-- |                   CURSOS POR SECCION                     |
-- ============================================================

-- Tabla de Aulas
CREATE TABLE IF NOT EXISTS T_Aulas (
    Id_Aula INT PRIMARY KEY AUTO_INCREMENT,
    Grado VARCHAR(50) NOT NULL,
    Seccion VARCHAR(10) NOT NULL
);

-- Tabla de Cursos
CREATE TABLE IF NOT EXISTS T_Cursos (
    Id_Curso INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(255) NOT NULL UNIQUE
);

-- Tabla de Cursos-Aula
CREATE TABLE IF NOT EXISTS T_Cursos_Aula (
    Id_Curso_Aula INT PRIMARY KEY AUTO_INCREMENT,
    Id_Curso INT,
    Id_Aula INT,
    FOREIGN KEY (Id_Curso) REFERENCES T_Cursos(Id_Curso),
    FOREIGN KEY (Id_Aula) REFERENCES T_Aulas(Id_Aula)
);


-- ============================================================
-- |             ASIGNACION POR CURSO Y SECCION               |
-- ============================================================

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


-- ============================================================
-- |                TEMAS POR CURSO Y SECCION                 |
-- ============================================================

-- Tabla de Temas
CREATE TABLE IF NOT EXISTS T_Temas (
    Id_Tema INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Tema VARCHAR(255) NOT NULL,
    Id_Curso_Aula INT,
    Num_Orden INT,
    FOREIGN KEY (Id_Curso_Aula) REFERENCES T_Cursos_Aula(Id_Curso_Aula)
);


-- ============================================================
-- |           ARCHIVOS CON TITULO ALMACENADOS EN S3          |
-- ============================================================

-- Tabla de Archivos
CREATE TABLE IF NOT EXISTS T_Archivos (
    Id_Archivo INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Archivo VARCHAR(255) NOT NULL,
    Extension VARCHAR(50) NOT NULL,
    Key_S3 VARCHAR(255) NOT NULL
);

-- ============================================================
-- |                   ARCHIVOS POR TEMA                      |
-- ============================================================

-- Tabla de Archivos-Tema
CREATE TABLE IF NOT EXISTS T_Archivos_Tema (
    Id_Archivo_Tema INT PRIMARY KEY AUTO_INCREMENT,
    Id_Archivo INT,
    Id_Tema INT,
    FOREIGN KEY (Id_Archivo) REFERENCES T_Archivos(Id_Archivo),
    FOREIGN KEY (Id_Tema) REFERENCES T_Temas(Id_Tema)
);

-- ============================================================
-- |                     TAREAS POR TEMA                      |
-- ============================================================

-- Tabla de Tareas
CREATE TABLE IF NOT EXISTS T_Tarea (
    Id_Tarea INT PRIMARY KEY AUTO_INCREMENT,
    Id_Tema INT,
    Titulo VARCHAR(255) NOT NULL,
    Descrip_tarea TEXT,
    Fecha_hora_apertura DATETIME NOT NULL,
    Fecha_hora_limite DATETIME NOT NULL,
    Puntaje_Max DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (Id_Tema) REFERENCES T_Temas(Id_Tema)
);

-- Tabla de Archivos adjuntos de Tarea
CREATE TABLE IF NOT EXISTS T_Archivos_Tarea (
    Id_Archivos_Tarea INT PRIMARY KEY AUTO_INCREMENT,
    Id_Archivo INT,
    Id_Tarea INT,
    FOREIGN KEY (Id_Archivo) REFERENCES T_Archivos(Id_Archivo),
    FOREIGN KEY (Id_Tarea) REFERENCES T_Tarea(Id_Tarea)
);

-- Tabla de Respuestas de Tarea
CREATE TABLE IF NOT EXISTS T_Respuestas_Tarea (
    Id_Respuesta_Tarea INT PRIMARY KEY AUTO_INCREMENT,
    Id_Archivo INT,
    Id_Tarea INT,
    DNI_Estudiante VARCHAR(20),
    FOREIGN KEY (Id_Archivo) REFERENCES T_Archivos(Id_Archivo),
    FOREIGN KEY (Id_Tarea) REFERENCES T_Tarea(Id_Tarea),
    FOREIGN KEY (DNI_Estudiante) REFERENCES T_Estudiantes(DNI_Estudiante)
);

-- Tabla de Revisión de Tarea
CREATE TABLE IF NOT EXISTS T_Revision_Tarea (
    Id_Revision_Tarea INT PRIMARY KEY AUTO_INCREMENT,
    Id_Archivo_Tarea_Respuesta INT,
    Observacion TEXT DEFAULT NULL,
    Puntaje DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (Id_Archivo_Tarea_Respuesta) REFERENCES T_Respuestas_Tarea(Id_Respuesta_Tarea)
);

-- ============================================================
-- |                     FOROS POR TEMA                       |
-- ============================================================

-- Tabla de Foros
CREATE TABLE IF NOT EXISTS T_Foro (
    Id_Foro INT PRIMARY KEY AUTO_INCREMENT,
    Titulo VARCHAR(255) NOT NULL,
    Descrip_Foro TEXT,
    Id_Tema INT,
    Imagen_Key_S3 VARCHAR(255),
    FOREIGN KEY (Id_Tema) REFERENCES T_Temas(Id_Tema)
);

-- Tabla de Respuestas de Foro
CREATE TABLE IF NOT EXISTS T_Respuestas_Foro (
    Id_Respuesta_Foro INT PRIMARY KEY AUTO_INCREMENT,
    Contenido_Respuesta TEXT NOT NULL,
    Id_Foro INT,
    DNI_Estudiante VARCHAR(20),
    FOREIGN KEY (Id_Foro) REFERENCES T_Foro(Id_Foro),
    FOREIGN KEY (DNI_Estudiante) REFERENCES T_Estudiantes(DNI_Estudiante)
);


-- ============================================================
-- |                     URLs POR TEMA                       |
-- ============================================================

-- Tabla de URLs
CREATE TABLE IF NOT EXISTS T_URLs (
    Id_URL INT PRIMARY KEY AUTO_INCREMENT,
    Descripcion VARCHAR(255) NOT NULL,
    URL TEXT NOT NULL,
    Id_Tema INT,
    FOREIGN KEY (Id_Tema) REFERENCES T_Temas(Id_Tema)
);


-- ============================================================
-- |                 CUESTIONARIOS POR TEMA                   |
-- ============================================================

-- Tabla de Cuestionarios
CREATE TABLE IF NOT EXISTS T_Cuestionario (
    Id_Cuestionario INT PRIMARY KEY AUTO_INCREMENT,
    Nom_Cuest VARCHAR(255) NOT NULL,
    Id_Tema INT,
    Fecha_hora_apertura DATETIME NOT NULL,
    Fecha_hora_limite DATETIME NOT NULL,
    Puntaje_Max DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (Id_Tema) REFERENCES T_Temas(Id_Tema)
);

-- Tabla de Preguntas por Cuestionario
CREATE TABLE IF NOT EXISTS T_Preguntas_Cuestionario (
    Id_Pregunta INT PRIMARY KEY AUTO_INCREMENT,
    Id_Cuestionario INT,
    Contenido_Pregunta TEXT NOT NULL,
    Tipo TINYINT NOT NULL, -- Tipo de pregunta (0, 1, o 2)
    Puntaje_Max DECIMAL(5,2) NOT NULL,
    Imagen_Key_S3 VARCHAR(255),
    FOREIGN KEY (Id_Cuestionario) REFERENCES T_Cuestionario(Id_Cuestionario)
);

-- Tabla de alternativas por pregunta
CREATE TABLE IF NOT EXISTS T_Alternativas_Preg_Cuest (
    Id_Alternativa_Preg_Cuest INT PRIMARY KEY AUTO_INCREMENT,
    Id_Pregunta INT,
    Alt_Contenido TEXT NOT NULL,
    Correcta BOOLEAN NOT NULL, -- Verdadero o falso
    FOREIGN KEY (Id_Pregunta) REFERENCES T_Preguntas_Cuestionario(Id_Pregunta)
);

-- Tabla de espuestas a preguntas del cuestionario
CREATE TABLE IF NOT EXISTS T_Resp_Preg_Cuest (
    Id_Resp_Preg_Cuest INT PRIMARY KEY AUTO_INCREMENT,
    Id_Alternativa_Preg_Cuest INT,
    DNI_Estudiante VARCHAR(20),
    Contenido TEXT,
    FOREIGN KEY (Id_Alternativa_Preg_Cuest) REFERENCES T_Alternativas_Preg_Cuest(Id_Alternativa_Preg_Cuest),
    FOREIGN KEY (DNI_Estudiante) REFERENCES T_Estudiantes(DNI_Estudiante)
);


-- Tabla de revision para preguntas de respuesta libre
CREATE TABLE IF NOT EXISTS T_Revision_Resp_Preg_Libre (
    Id_Revision_Resp_Preg_Libre INT PRIMARY KEY AUTO_INCREMENT,
    Id_Resp_Preg_Cuest INT,
    Puntaje DECIMAL(5,2) NOT NULL,
    Observacion TEXT,
    FOREIGN KEY (Id_Resp_Preg_Cuest) REFERENCES T_Resp_Preg_Cuest(Id_Resp_Preg_Cuest)
);


-- Tabla de revision para cuestionarios
CREATE TABLE IF NOT EXISTS T_Revision_Cuestionario (
    Id_Revision_Cuestionario INT PRIMARY KEY AUTO_INCREMENT,
    Id_Cuestionario INT,
    DNI_Estudiante VARCHAR(20),
    Puntaje DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (Id_Cuestionario) REFERENCES T_Cuestionario(Id_Cuestionario),
    FOREIGN KEY (DNI_Estudiante) REFERENCES T_Estudiantes(DNI_Estudiante)
);


-- ============================================================
-- |                    CONFIGURACIONES                       |
-- ============================================================

-- Tabla de Configuraciones de la aplicacion
CREATE TABLE IF NOT EXISTS T_Configuraciones (
    Id_Conf INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Conf VARCHAR(255) NOT NULL UNIQUE,
    Valor TEXT NOT NULL,
    Descripcion TEXT NULL,
    Ult_Actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


