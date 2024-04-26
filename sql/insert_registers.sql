
use virtual_classroom_b;
-- 1 REGISTRO superadmin_dir y password1
-- 2 REGISTRO superadmin_secr y password2

-- Registros para la tabla T_Superadmin
INSERT INTO T_Superadmin (Nombre_Usuario, Contraseña) VALUES
('TWYrUXNHT0YwVU94dmZGQ09jeGJpdz09', 'SmJ4UkkzU3ZDNkdnSVVXWFlMa2M2dz09'),
('ajNzZHo3bmpZdXJrVGZuZmp0a0FTZz09', 'UDg3WjhWd0dTcXNCS3l4clVKL09Sdz09');


-- 1 REGISTRO administrador_1 y password1
-- 2 REGISTRO administrador_2 y password2

-- Registros para la tabla T_Admins
INSERT INTO T_Administradores (Nombre_Usuario, Contraseña) VALUES
('YkVzVzlJTGNnK3hZTmorei9PV2Zodz09', 'SmJ4UkkzU3ZDNkdnSVVXWFlMa2M2dz09'),
('c1FYeVpLdlBOYWUyVk5td05sOWdXdz09', 'UDg3WjhWd0dTcXNCS3l4clVKL09Sdz09');


-- Registros para la tabla T_Usuarios
INSERT INTO T_Usuarios (Nombres, Apellidos, Fecha_Nacimiento, Nombre_Usuario, Contraseña_Usuario, Dirección_Domicilio, Nombre_Contacto_Emergencia, Parentezco_Contacto_Emergencia, Telefono_Contacto_Emergencia, Foto_Perfil_Key_S3)
VALUES 
('Juan', 'Perez', '1995-03-15', 'juanperez', 'contraseña1', 'Av. Lima 123', 'María Perez', 'Madre', '987654321', 'foto1.jpg'),
('Maria', 'Gomez', '1997-09-20', 'mariagomez', 'contraseña2', 'Jr. Arequipa 456', 'Carlos Gomez', 'Padre', '999888777', 'foto2.jpg'),
('Pedro', 'Martinez', '1999-11-10', 'pedromartinez', 'contraseña3', 'Calle Tacna 789', 'Ana Martinez', 'Hermana', '666555444', 'foto3.jpg'),
('Luis', 'Gonzalez', '2000-02-05', 'luisgonzalez', 'contraseña4', 'Pje. Huancayo 101', 'Elena Gonzalez', 'Madre', '333222111', 'foto4.jpg'),
('Ana', 'Rodriguez', '1998-07-08', 'anarodriguez', 'contraseña5', 'Av. Cusco 202', 'Raul Rodriguez', 'Padre', '111000999', 'foto5.jpg'),
('Carlos', 'Hernandez', '1985-06-10', 'carloshernandez', 'contraseña6', 'Av. Arequipa 303', 'Laura Hernandez', 'Esposa', '555444333', 'foto6.jpg'),
('Laura', 'Gutierrez', '1988-12-25', 'lauragutierrez', 'contraseña7', 'Jr. Puno 505', 'Diego Gutierrez', 'Esposo', '222111000', 'foto7.jpg'),
('Elena', 'Alvarez', '1990-09-01', 'elenaalvarez', 'contraseña8', 'Calle Moquegua 707', 'Jorge Alvarez', 'Padre', '777666555', 'foto8.jpg'),
('Diego', 'Morales', '1992-07-30', 'diegomorales', 'contraseña9', 'Av. Trujillo 909', 'Ana Morales', 'Madre', '444333222', 'foto9.jpg'),
('Jorge', 'Castillo', '1994-04-18', 'jorgecastillo', 'contraseña10', 'Jr. Tacna 111', 'Carla Castillo', 'Madre', '888777666', 'foto10.jpg');

-- Registros para la tabla T_Profesores
INSERT INTO T_Profesores (DNI_Profesor, Id_Usuario)
VALUES 
('12345678', 6),
('87654321', 7),
('98765432', 8),
('23456789', 9),
('34567890', 10);

-- Registros para la tabla T_Aulas
INSERT INTO T_Aulas (Grado, Seccion)
VALUES 
('1', 'A'),
('1', 'B'),
('2', 'A'),
('2', 'B'),
('3', 'A');

-- Registros para la tabla T_Estudiantes
INSERT INTO T_Estudiantes (DNI_Estudiante, Id_Usuario, Id_Aula)
VALUES 
('11111111', 1, 1),
('22222222', 2, 2),
('33333333', 3, 3),
('44444444', 4, 4),
('55555555', 5, 5);


-- Registros para la tabla T_Cursos
INSERT INTO T_Cursos (Nombre)
VALUES 
('Matemáticas'),
('Ciencias'),
('Historia'),
('Lenguaje'),
('Educación Física');

-- Registros para la tabla T_Cursos_Aula
INSERT INTO T_Cursos_Aula (Id_Curso, Id_Aula)
VALUES 
(1, 1),
(2, 1),
(3, 2),
(4, 2),
(5, 3);

-- Registros para la tabla T_Temas
INSERT INTO T_Temas (Nombre_Tema, Id_Curso_Aula, Num_Orden)
VALUES 
('Álgebra', 1, 1),
('Geometría', 1, 2),
('Química Orgánica', 2, 1),
('Física Moderna', 2, 2),
('Revolución Francesa', 3, 1);




