use Virtual_Classroom_B;

CREATE TABLE IF NOT EXISTS T_Superadmin (
    Id_Superadmin INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Usuario VARCHAR(255) NOT NULL,
    Contraseña VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS T_Admins (
    Id_Admin INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Usuario VARCHAR(255) NOT NULL,
    Contraseña VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS T_Users (
    Id_User INT PRIMARY KEY AUTO_INCREMENT,
    Nombres VARCHAR(255) NOT NULL,
    Apellidos VARCHAR(255) NOT NULL,
    Fecha_Nacimiento DATE,
    Nombre_Usuario VARCHAR(255) NOT NULL,
    Contraseña_Usuario VARCHAR(255) NOT NULL,
    Dirección_Domicilio VARCHAR(255),
    Nombre_Contacto_Emergencia VARCHAR(255),
    Parentezco_Contacto_Emergencia VARCHAR(255),
    Telefono_Contacto_Emergencia VARCHAR(20),
    Foto_Perfil_Key_S3 VARCHAR(255)
);

-- Creación de la tabla T_Teachers
CREATE TABLE IF NOT EXISTS T_Teachers (
    DNI_Teacher VARCHAR(20) PRIMARY KEY,
    Id_User INT,
    FOREIGN KEY (Id_User) REFERENCES T_Users(Id_User)
);

-- Creación de la tabla T_Classrooms
CREATE TABLE IF NOT EXISTS T_Classrooms (
    Id_Classroom INT PRIMARY KEY AUTO_INCREMENT,
    Grado VARCHAR(50) NOT NULL,
    Seccion VARCHAR(10) NOT NULL
);

-- Creación de la tabla T_Students
CREATE TABLE IF NOT EXISTS T_Students (
    DNI_Student VARCHAR(20) PRIMARY KEY,
    Id_User INT,
    Id_Classroom INT,
    FOREIGN KEY (Id_User) REFERENCES T_Users(Id_User),
    FOREIGN KEY (Id_Classroom) REFERENCES T_Classrooms(Id_Classroom)
);


-- Creación de la tabla T_Courses
CREATE TABLE IF NOT EXISTS T_Courses (
    Id_Course INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(255) NOT NULL
);


-- Creación de la tabla T_Courses_Classroom
CREATE TABLE IF NOT EXISTS T_Courses_Classroom (
    Id_Course_Classroom INT PRIMARY KEY AUTO_INCREMENT,
    Id_Course INT,
    Id_Classroom INT,
    FOREIGN KEY (Id_Course) REFERENCES T_Courses(Id_Course),
    FOREIGN KEY (Id_Classroom) REFERENCES T_Classrooms(Id_Classroom)
);

-- Creación de la tabla T_Topics
CREATE TABLE IF NOT EXISTS T_Topics (
    Id_Topic INT PRIMARY KEY AUTO_INCREMENT,
    Nom_Tema VARCHAR(255) NOT NULL,
    Id_Course_Classroom INT,
    Num_Orden INT,
    FOREIGN KEY (Id_Course_Classroom) REFERENCES T_Courses_Classroom(Id_Course_Classroom)
);
