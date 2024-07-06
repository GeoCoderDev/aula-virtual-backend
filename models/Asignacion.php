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
            "SELECT A.Id_Asignacion, 
                    A.DNI_Profesor, 
                    A.Id_Horario_Curso_Aula, 
                    C.Nombre AS Nombre_Curso, 
                    CA.Id_Curso_Aula, 
                    HCA.Dia_Semana, 
                    HCA.Cant_Horas_Academicas,
                    HA.Id_Hora_Academica,
                    U.Nombres AS Nombre_Profesor,
                    U.Apellidos AS Apellido_Profesor
             FROM T_Asignaciones AS A 
             INNER JOIN T_Horario_Curso_Aula AS HCA ON A.Id_Horario_Curso_Aula = HCA.Id_Horario_Curso_Aula 
             INNER JOIN T_Horas_Academicas AS HA ON HCA.Id_Hora_Academica = HA.Id_Hora_Academica
             INNER JOIN T_Cursos_Aula AS CA ON HCA.Id_Curso_Aula = CA.Id_Curso_Aula 
             INNER JOIN T_Cursos AS C ON CA.Id_Curso = C.Id_Curso 
             INNER JOIN T_Aulas AS AU ON CA.Id_Aula = AU.Id_Aula 
             INNER JOIN T_Profesores AS P ON A.DNI_Profesor = P.DNI_Profesor
             INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario
             WHERE AU.Id_Aula = :Id_Aula"
        );

        $stmt->execute(['Id_Aula' => $Id_Aula]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAsignationsByTeacher($DNI_Profesor)
    {
        $stmt = $this->conn->prepare(
            "SELECT A.Id_Asignacion, 
                    A.DNI_Profesor, 
                    A.Id_Horario_Curso_Aula, 
                    C.Nombre AS Nombre_Curso, 
                    CA.Id_Curso_Aula, 
                    AU.Grado, 
                    AU.Seccion, 
                    HCA.Dia_Semana, 
                    HCA.Cant_Horas_Academicas,
                    HA.Id_Hora_Academica,
                    U.Nombres AS Nombre_Profesor,
                    U.Apellidos AS Apellido_Profesor
             FROM T_Asignaciones AS A 
             INNER JOIN T_Horario_Curso_Aula AS HCA ON A.Id_Horario_Curso_Aula = HCA.Id_Horario_Curso_Aula 
             INNER JOIN T_Horas_Academicas AS HA ON HCA.Id_Hora_Academica = HA.Id_Hora_Academica
             INNER JOIN T_Cursos_Aula AS CA ON HCA.Id_Curso_Aula = CA.Id_Curso_Aula 
             INNER JOIN T_Cursos AS C ON CA.Id_Curso = C.Id_Curso 
             INNER JOIN T_Aulas AS AU ON CA.Id_Aula = AU.Id_Aula 
             INNER JOIN T_Profesores AS P ON A.DNI_Profesor = P.DNI_Profesor 
             INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario
             WHERE P.DNI_Profesor = :DNI_Profesor"
        );

        $stmt->execute(['DNI_Profesor' => $DNI_Profesor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function checkAvailability($DNI_Profesor, $Dia_Semana, $Id_Hora_Academica_Inicio, $Cant_Horas_Academicas)
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS conflict 
             FROM T_Asignaciones AS A
             INNER JOIN T_Horario_Curso_Aula AS HCA ON A.Id_Horario_Curso_Aula = HCA.Id_Horario_Curso_Aula 
             WHERE A.DNI_Profesor = :DNI_Profesor 
             AND HCA.Dia_Semana = :Dia_Semana
             AND (
                (HCA.Id_Hora_Academica <= :Id_Hora_Academica_Inicio 
                AND (HCA.Id_Hora_Academica + HCA.Cant_Horas_Academicas) > :Id_Hora_Academica_Inicio)
                OR (:Id_Hora_Academica_Inicio <= HCA.Id_Hora_Academica 
                AND (:Id_Hora_Academica_Inicio + :Cant_Horas_Academicas) > HCA.Id_Hora_Academica)
             )"
        );

        $stmt->execute([
            'DNI_Profesor' => $DNI_Profesor,
            'Dia_Semana' => $Dia_Semana,
            'Id_Hora_Academica_Inicio' => $Id_Hora_Academica_Inicio,
            'Cant_Horas_Academicas' => $Cant_Horas_Academicas,
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['conflict'] == 0;
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO T_Asignaciones (DNI_Profesor, Id_Horario_Curso_Aula)  VALUES (:DNI_Profesor, :Id_Horario_Curso_Aula)"
        );

        $stmt->execute([
            'DNI_Profesor' => $data['DNI_Profesor'],
            'Id_Horario_Curso_Aula' => $data['Id_Horario_Curso_Aula'],
        ]);

        return $this->conn->lastInsertId();
    }


    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    public function commit()
    {
        $this->conn->commit();
    }

    public function rollBack()
    {
        $this->conn->rollBack();
    }
}
