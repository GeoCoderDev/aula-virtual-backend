<?php

require_once __DIR__ . '/../config/Database.php';

use Config\Database;

class Asignacion
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function getAsignationsByAula($Id_Aula)
    {
        $stmt = $this->conn->prepare(
            "SELECT A.*, C.Nombre AS Nombre_Curso, CA.Id_Curso_Aula, AU.Grado, AU.Seccion, 
                    HCA.Id_Horario_Curso_Aula, HCA.Dia_Semana, HA.Id_Hora_Academica, HA.Valor AS Hora_Inicio, 
                    HCA.Cant_Horas_Academicas 
             FROM T_Asignaciones AS A 
             INNER JOIN T_Horario_Curso_Aula AS HCA ON A.Id_Horario_Curso_Aula = HCA.Id_Horario_Curso_Aula 
             INNER JOIN T_Horas_Academicas AS HA ON HCA.Id_Hora_Academica = HA.Id_Hora_Academica
             INNER JOIN T_Cursos_Aula AS CA ON HCA.Id_Curso_Aula = CA.Id_Curso_Aula 
             INNER JOIN T_Cursos AS C ON CA.Id_Curso = C.Id_Curso 
             INNER JOIN T_Aulas AS AU ON CA.Id_Aula = AU.Id_Aula 
             WHERE AU.Id_Aula = :Id_Aula"
        );

        $stmt->execute(['Id_Aula' => $Id_Aula]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getAsignationsByTeacher($DNI_Profesor)
    {
        $stmt = $this->conn->prepare("SELECT A.*, C.Nombre AS Nombre_Curso, CA.Id_Curso_Aula, AU.Grado, AU.Seccion, HCA.Id_Horario_Curso_Aula, HCA.Dia_Semana, HA.Id_Hora_Academica, HA.Valor AS Hora_Inicio, HCA.Cant_Horas_Academicas 
                                  FROM T_Asignaciones AS A 
                                  INNER JOIN T_Horario_Curso_Aula AS HCA ON A.Id_Horario_Curso_Aula = HCA.Id_Horario_Curso_Aula 
                                  INNER JOIN T_Horas_Academicas AS HA ON HCA.Id_Hora_Academica = HA.Id_Hora_Academica
                                  INNER JOIN T_Cursos_Aula AS CA ON HCA.Id_Curso_Aula = CA.Id_Curso_Aula 
                                  INNER JOIN T_Cursos AS C ON CA.Id_Curso = C.Id_Curso 
                                  INNER JOIN T_Aulas AS AU ON CA.Id_Aula = AU.Id_Aula 
                                  INNER JOIN T_Profesores AS P ON A.DNI_Profesor = P.DNI_Profesor 
                                  WHERE P.DNI_Profesor = :DNI_Profesor");

        $stmt->execute(['DNI_Profesor' => $DNI_Profesor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}
