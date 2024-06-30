<?php

require_once __DIR__ . '/../config/Database.php';

use Config\Database;

class HorarioCursoAula
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function getByAula($Id_Aula)
    {
        $stmt = $this->conn->prepare("SELECT 
            HCA.Id_Horario_Curso_Aula,
            HCA.Dia_Semana,
            HA.Id_Hora_Academica,
            HCA.Cant_Horas_Academicas,
            C.Nombre AS Nombre_Curso,
            AU.Grado,
            AU.Seccion,
            A.DNI_Profesor,
            U.Nombres AS Nombre_Profesor,
            U.Apellidos AS Apellido_Profesor
            FROM 
                T_Horario_Curso_Aula AS HCA
            INNER JOIN 
                T_Cursos_Aula AS CA ON HCA.Id_Curso_Aula = CA.Id_Curso_Aula
            INNER JOIN 
                T_Cursos AS C ON CA.Id_Curso = C.Id_Curso
            INNER JOIN 
                T_Aulas AS AU ON CA.Id_Aula = AU.Id_Aula
            INNER JOIN 
                T_Horas_Academicas AS HA ON HCA.Id_Hora_Academica = HA.Id_Hora_Academica
            LEFT JOIN 
                T_Asignaciones AS A ON HCA.Id_Horario_Curso_Aula = A.Id_Horario_Curso_Aula
            LEFT JOIN 
                T_Profesores AS P ON A.DNI_Profesor = P.DNI_Profesor
            LEFT JOIN 
                T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario
            WHERE 
                AU.Id_Aula = :Id_Aula
        ");

        $stmt->execute(['Id_Aula' => $Id_Aula]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByDNIProfesor($DNI_Profesor)
    {
        $stmt = $this->conn->prepare("
        SELECT 
            HCA.Id_Horario_Curso_Aula, 
            HCA.Dia_Semana, 
            HCA.Id_Hora_Academica, 
            HCA.Cant_Horas_Academicas, 
            C.Nombre AS Nombre_Curso, 
            AU.Grado, 
            AU.Seccion 
        FROM 
            T_Horario_Curso_Aula AS HCA 
        INNER JOIN 
            T_Cursos_Aula AS CA ON HCA.Id_Curso_Aula = CA.Id_Curso_Aula 
        INNER JOIN 
            T_Cursos AS C ON CA.Id_Curso = C.Id_Curso 
        INNER JOIN 
            T_Aulas AS AU ON CA.Id_Aula = AU.Id_Aula 
        INNER JOIN 
            T_Horas_Academicas AS HA ON HCA.Id_Hora_Academica = HA.Id_Hora_Academica 
        INNER JOIN 
            T_Asignaciones AS A ON HCA.Id_Horario_Curso_Aula = A.Id_Horario_Curso_Aula 
        WHERE 
            A.DNI_Profesor = :DNI_Profesor
    ");
        $stmt->execute(['DNI_Profesor' => $DNI_Profesor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
