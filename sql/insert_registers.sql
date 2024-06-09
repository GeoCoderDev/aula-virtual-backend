
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
INSERT INTO T_Usuarios (Nombres, Apellidos, Fecha_Nacimiento, Nombre_Usuario, Contraseña_Usuario, Direccion_Domicilio, Nombre_Contacto_Emergencia, Parentezco_Contacto_Emergencia, Telefono_Contacto_Emergencia, Foto_Perfil_Key_S3)
VALUES 
('Juan', 'Perez', '1995-03-15', 'juanperez', 'dnNvZG56OWhCU3FVdXlwRmNhVFhHUT09', 'Av. Lima 123', 'María Perez', 'Madre', '987654321', 'foto1.jpg'),
('Maria', 'Gomez', '1997-09-20', 'mariagomez', 'dnNvZG56OWhCU3FVdXlwRmNhVFhHUT09', 'Jr. Arequipa 456', 'Carlos Gomez', 'Padre', '999888777', 'foto2.jpg'),
('Pedro', 'Martinez', '1999-11-10', 'pedromartinez', 'dnNvZG56OWhCU3FVdXlwRmNhVFhHUT09', 'Calle Tacna 789', 'Ana Martinez', 'Hermana', '666555444', 'foto3.jpg'),
('Luis', 'Gonzalez', '2000-02-05', 'luisgonzalez', 'dnNvZG56OWhCU3FVdXlwRmNhVFhHUT09', 'Pje. Huancayo 101', 'Elena Gonzalez', 'Madre', '333222111', 'foto4.jpg'),
('Ana', 'Rodriguez', '1998-07-08', 'anarodriguez', 'dnNvZG56OWhCU3FVdXlwRmNhVFhHUT09', 'Av. Cusco 202', 'Raul Rodriguez', 'Padre', '111000999', 'foto5.jpg'),
('Carlos', 'Hernandez', '1985-06-10', 'carloshernandez', 'dnNvZG56OWhCU3FVdXlwRmNhVFhHUT09', 'Av. Arequipa 303', 'Laura Hernandez', 'Esposa', '555444333', 'foto6.jpg'),
('Laura', 'Gutierrez', '1988-12-25', 'lauragutierrez', 'dnNvZG56OWhCU3FVdXlwRmNhVFhHUT09', 'Jr. Puno 505', 'Diego Gutierrez', 'Esposo', '222111000', 'foto7.jpg'),
('Elena', 'Alvarez', '1990-09-01', 'elenaalvarez', 'dnNvZG56OWhCU3FVdXlwRmNhVFhHUT09', 'Calle Moquegua 707', 'Jorge Alvarez', 'Padre', '777666555', 'foto8.jpg'),
('Diego', 'Morales', '1992-07-30', 'diegomorales', 'dnNvZG56OWhCU3FVdXlwRmNhVFhHUT09', 'Av. Trujillo 909', 'Ana Morales', 'Madre', '444333222', 'foto9.jpg'),
('Jorge', 'Castillo', '1994-04-18', 'jorgecastillo', 'dnNvZG56OWhCU3FVdXlwRmNhVFhHUT09', 'Jr. Tacna 111', 'Carla Castillo', 'Madre', '888777666', 'foto10.jpg'),
('Roberto', 'López', '1980-05-25', 'robertolopez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Av. Huancayo 123', 'María López', 'Madre', '987654321', 'foto21.jpg'),
('Silvia', 'Torres', '1982-08-15', 'silviatorres', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Jr. Puno 456', 'Carlos Torres', 'Padre', '999888777', 'foto22.jpg'),
('Fernando', 'García', '1975-12-10', 'fernandogarcia', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Calle Lima 789', 'Elena García', 'Madre', '666555444', 'foto23.jpg'),
('Patricia', 'Martínez', '1983-03-20', 'patriciamartinez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Av. Amazonas 101', 'Andres Martínez', 'Padre', '333222111', 'foto24.jpg'),
('Javier', 'Díaz', '1988-11-05', 'javierdiaz', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Av. Trujillo 202', 'Laura Díaz', 'Esposa', '111000999', 'foto25.jpg'),
('Sandra', 'Pérez', '1979-09-28', 'sandraperez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Jr. Arequipa 303', 'Diego Pérez', 'Esposo', '555444333', 'foto26.jpg'),
('Alejandro', 'Sánchez', '1987-04-15', 'alejandrosanchez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Pje. Lima 505', 'Marta Sánchez', 'Madre', '222111000', 'foto27.jpg'),
('Carmen', 'Gómez', '1976-01-12', 'carmengomez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Calle Tacna 707', 'José Gómez', 'Padre', '777666555', 'foto28.jpg'),
('Gabriel', 'Hernández', '1986-07-30', 'gabrielhernandez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Av. Cusco 909', 'Sara Hernández', 'Madre', '444333222', 'foto29.jpg'),
('María', 'López', '1978-03-18', 'marialopez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Jr. Moquegua 111', 'Jorge López', 'Padre', '888777666', 'foto30.jpg'),
('Carlos', 'Martínez', '2001-05-15', 'carlosmartinez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Av. Lima 123', 'María Martínez', 'Madre', '987654321', 'foto31.jpg'),
('Andrea', 'Gómez', '2003-09-20', 'andreagomez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Jr. Arequipa 456', 'Carlos Gómez', 'Padre', '999888777', 'foto32.jpg'),
('Luis', 'Hernández', '2002-11-10', 'luishernandez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Calle Tacna 789', 'Ana Hernández', 'Hermana', '666555444', 'foto33.jpg'),
('Laura', 'Díaz', '2000-02-05', 'lauradiaz', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Pje. Huancayo 101', 'Elena Díaz', 'Madre', '333222111', 'foto34.jpg'),
('Javier', 'Rodríguez', '2004-07-08', 'javierrodriguez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Av. Cusco 202', 'Raul Rodríguez', 'Padre', '111000999', 'foto35.jpg'),
('María', 'Sánchez', '2005-06-10', 'mariasanchez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Av. Arequipa 303', 'Laura Sánchez', 'Esposa', '555444333', 'foto36.jpg'),
('Diego', 'Gutiérrez', '2006-12-25', 'diegogutierrez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Jr. Puno 505', 'Diego Gutiérrez', 'Esposo', '222111000', 'foto37.jpg'),
('Sofía', 'Alvarez', '2007-09-01', 'sofiaalvarez', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Calle Moquegua 707', 'Jorge Álvarez', 'Padre', '777666555', 'foto38.jpg'),
('Ana', 'Morales', '2008-07-30', 'anamorales', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Av. Trujillo 909', 'Ana Morales', 'Madre', '444333222', 'foto39.jpg'),
('Pedro', 'Castillo', '2009-04-18', 'pedrocastillo', 'UUtUaWVONDBYRGtQdGxlTGdRTk5yZz09', 'Jr. Tacna 111', 'Carla Castillo', 'Madre', '888777666', 'foto40.jpg');


-- Registros para la tabla T_Profesores
INSERT INTO T_Profesores (DNI_Profesor, Id_Usuario)
VALUES 
('12345678', 6),
('87654321', 7),
('98765432', 8),
('23456789', 9),
('34567890', 10),
('45678901', 11),
('56789012', 12),
('67890123', 13),
('78901234', 14),
('89012345', 15),
('90123456', 16),
('01234567', 17),
('98765402', 18),
('87634321', 19),
('76543210', 20);


-- Registros para la tabla T_Aulas
INSERT INTO T_Aulas (Grado, Seccion)
VALUES 
('1', 'A'),
('1', 'B'),
('1', 'C'),
('2', 'A'),
('2', 'B'),
('2', 'C'),
('3', 'A'),
('3', 'C'),
('3', 'D'),
('4', 'A'),
('4', 'B'),
('4', 'C'),
('5', 'A'),
('5', 'B'),
('5', 'C');

-- Registros para la tabla T_Estudiantes
INSERT INTO T_Estudiantes (DNI_Estudiante, Id_Usuario, Id_Aula)
VALUES 
('11111111', 1, 1),
('22222222', 2, 11),
('33333333', 3, 3),
('44444444', 4, 4),
('55555555', 5, 5),
('66666666', 21, 14),
('77777777', 22, 15),
('88888888', 23, 12),
('99999999', 24, 7),
('10101010', 25, 5),
('11111119', 26, 11),
('12121212', 27, 2),
('13131313', 28, 6),
('14141414', 29, 4),
('15151515', 30, 9);

-- Registros para la tabla T_Cursos
INSERT INTO T_Cursos (Nombre)
VALUES 
('Matemáticas'),
('Lenguaje'),
('Ciencias Naturales'),
('Historia'),
('Geografía'),
('Educación Física'),
('Arte'),
('Música'),
('Tecnología'),
('Inglés');

-- Registros para la tabla T_Cursos_Aula
INSERT INTO T_Cursos_Aula (Id_Curso, Id_Aula)
VALUES 
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10);

-- Registros para la tabla T_Temas
INSERT INTO T_Temas (Nombre_Tema, Id_Curso_Aula, Num_Orden)
VALUES 
('Álgebra', 1, 1),
('Geometría', 1, 2),
('Literatura', 2, 1),
('Redacción', 2, 2),
('Biología', 3, 1),
('Física', 3, 2),
('Edad Antigua', 4, 1),
('Edad Media', 4, 2),
('Continente Americano', 5, 1),
('Europa', 5, 2);

-- Registros para la tabla T_Horario_Curso_Aula
INSERT INTO T_Horario_Curso_Aula (Id_Curso_Aula, Dia_Semana, Hora_Inicio, Cant_Horas_Academicas)
VALUES 
(1, 'Lunes', '08:00:00', 2),
(1, 'Miércoles', '08:00:00', 2),
(2, 'Martes', '10:00:00', 2),
(2, 'Jueves', '10:00:00', 2),
(3, 'Lunes', '14:00:00', 2),
(3, 'Miércoles', '14:00:00', 2),
(4, 'Martes', '08:00:00', 2),
(4, 'Jueves', '08:00:00', 2),
(5, 'Lunes', '10:00:00', 2),
(5, 'Miércoles', '10:00:00', 2),
(6, 'Martes', '14:00:00', 2),
(6, 'Jueves', '14:00:00', 2),
(7, 'Lunes', '08:00:00', 2),
(7, 'Miércoles', '08:00:00', 2),
(8, 'Martes', '10:00:00', 2),
(8, 'Jueves', '10:00:00', 2),
(9, 'Lunes', '14:00:00', 2),
(9, 'Miércoles', '14:00:00', 2),
(10, 'Martes', '08:00:00', 2),
(10, 'Jueves', '08:00:00', 2);

-- Registros para la tabla T_Asignaciones
INSERT INTO T_Asignaciones (DNI_Profesor, Id_Horario_Curso_Aula)
VALUES 
('12345678', 1),
('87654321', 2),
('98765432', 3),
('23456789', 4),
('34567890', 5),
('45678901', 6),
('56789012', 7),
('67890123', 8),
('78901234', 9),
('89012345', 10),
('90123456', 11),
('01234567', 12),
('98765432', 13),
('87654321', 14),
('76543210', 15),
('12345678', 16),
('87654321', 17),
('98765432', 18),
('23456789', 19),
('34567890', 20);

-- Registros para la tabla configuraciones

INSERT INTO T_Configuraciones (Nombre_Conf, Valor, Descripcion) 
VALUES 
('Hora_Academica_Minutos', '45', 'Duración de una hora académica en minutos'),
('Inicio_Año', '2024-03-25', 'Fecha de inicio del año escolar'),
('Fin_Año', '2024-12-13', 'Fecha de fin del año escolar'),
('Dias_Clase', 'Lunes, Martes, Miércoles, Jueves, Viernes', 'Días de la semana en los que se asiste al colegio'),
('Hora_Inicio_Clases', '08:00', 'Hora de inicio de clases diario'),
('Hora_Final_Clases', '15:00', 'Hora final de clases diario'),
('Hora_Inicio_Recreo1', '10:30', 'Hora de inicio del primer recreo'),
('Hora_Final_Recreo1', '11:00', 'Hora final del primer recreo');
