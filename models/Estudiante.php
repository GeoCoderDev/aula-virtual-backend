<?php
require_once __DIR__ . '../../Config/Database.php';
require_once __DIR__.'/../lib/helpers/functions/totalTimeInSeconds.php';
use Config\Database;
use Config\S3Manager;

define("DURATION_PERFIL_PHOTO_STUDENT", totalTimeInSeconds(1,0,0,0));

class Estudiante
{
    private $conn;
    private $s3Manager;

    public function __construct()
    {
        $this->conn = Database::getConnection();
        $this->s3Manager = new S3Manager();
    }

    public function getAll($includePassword = false, $limit = 200, $startFrom = 0, $dni = null, $nombre = null, $apellidos = null, $grado = null, $seccion = null, $estado = null) // Agrega el nuevo parámetro de consulta
{
    if ($includePassword) {
        $query = "SELECT U.Id_Usuario, E.DNI_Estudiante, A.Grado, A.Seccion, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3, U.Estado FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario INNER JOIN T_Aulas AS A ON E.Id_Aula = A.Id_Aula WHERE 1=1";
    } else {
        $query = "SELECT U.Id_Usuario, E.DNI_Estudiante, A.Grado, A.Seccion, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3, U.Estado FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario INNER JOIN T_Aulas AS A ON E.Id_Aula = A.Id_Aula WHERE 1=1";
    }

    // Agregar condiciones según los parámetros de búsqueda
    if ($dni !== null) {
        $query .= " AND E.DNI_Estudiante LIKE :dni";
    }
    if ($nombre !== null) {
        $query .= " AND U.Nombres LIKE :nombre";
    }
    if ($apellidos !== null) {
        $query .= " AND U.Apellidos LIKE :apellidos";
    }
    if ($grado !== null) {
        $query .= " AND A.Grado = :grado";
    }
    if ($seccion !== null) {
        $query .= " AND A.Seccion = :seccion";
    }
    if ($estado !== null) { // Agrega el filtro por estado si se proporciona
        $query .= " AND U.Estado = :estado";
    }

    // Agregar límite y offset
    $query .= " LIMIT :startFrom, :limit";

    $stmt = $this->conn->prepare($query);

    // Vincular los parámetros
    if ($dni !== null) {
        $stmt->bindValue(':dni', $dni . '%', PDO::PARAM_STR);
    }
    if ($nombre !== null) {
        $stmt->bindValue(':nombre', '%' . $nombre . '%', PDO::PARAM_STR);
    }
    if ($apellidos !== null) {
        $stmt->bindValue(':apellidos', '%' . $apellidos . '%', PDO::PARAM_STR);
    }
    if ($grado !== null) {
        $stmt->bindValue(':grado', $grado, PDO::PARAM_STR);
    }
    if ($seccion !== null) {
        $stmt->bindValue(':seccion', $seccion, PDO::PARAM_STR);
    }
    if ($estado !== null) { // Vincula el valor del estado si se proporciona
        $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
    }

    $stmt->bindValue(':startFrom', $startFrom, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Itera sobre los estudiantes para agregar la URL del objeto S3 si 'Foto_Perfil_Key_S3' no es nulo
    foreach ($students as &$student) {
        if ($student['Foto_Perfil_Key_S3'] !== null) {
            $student['Foto_Perfil_URL'] = $this->s3Manager->getObjectUrl($student['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_STUDENT);
        }
    }

    return $students;
}

    public function getByDNI($dni, $includePassword = false)
    {
        if ($includePassword) {
            $query = "SELECT U.Id_Usuario, E.DNI_Estudiante, A.Grado, A.Seccion, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3, U.Estado FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario INNER JOIN T_Aulas AS A ON E.Id_Aula = A.Id_Aula WHERE E.DNI_Estudiante = :dni";
        } else {
            $query = "SELECT U.Id_Usuario, E.DNI_Estudiante, A.Grado, A.Seccion, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3, U.Estado FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario INNER JOIN T_Aulas AS A ON E.Id_Aula = A.Id_Aula WHERE E.DNI_Estudiante = :dni";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute(['dni' => $dni]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        // Agrega la URL del objeto S3 al resultado si 'Foto_Perfil_Key_S3' no es nulo
        if ($student && $student['Foto_Perfil_Key_S3'] !== null) {
            $student['Foto_Perfil_URL'] = $this->s3Manager->getObjectUrl($student['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_STUDENT);
        }

        return $student;
    }
    public function getByUserId($Id_Usuario, $includePassword = false)
    {
        if ($includePassword) {
            $stmt = $this->conn->prepare("SELECT U.Id_Usuario, E.DNI_Estudiante, A.Grado, A.Seccion, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3, U.Estado FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario INNER JOIN T_Aulas AS A ON E.Id_Aula = A.Id_Aula WHERE E.Id_Usuario = :Id_Usuario");
        } else {
            $stmt = $this->conn->prepare("SELECT U.Id_Usuario, E.DNI_Estudiante, A.Grado, A.Seccion, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3, U.Estado FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario INNER JOIN T_Aulas AS A ON E.Id_Aula = A.Id_Aula WHERE E.Id_Usuario = :Id_Usuario");
        }
        $stmt->execute(['Id_Usuario' => $Id_Usuario]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        // Agrega la URL del objeto S3 al resultado si 'Foto_Perfil_Key_S3' no es nulo
        if ($student && $student['Foto_Perfil_Key_S3'] !== null) {
            $student['Foto_Perfil_URL'] = $this->s3Manager->getObjectUrl($student['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_STUDENT);
        }

        return $student;
    }


    public function getByUsername($username, $includePassword = false) {
        if ($includePassword) {
            $stmt = $this->conn->prepare("SELECT U.Id_Usuario, E.DNI_Estudiante, A.Grado, A.Seccion, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3, U.Estado FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario INNER JOIN T_Aulas AS A ON E.Id_Aula = A.Id_Aula WHERE U.Nombre_Usuario = :username");
        } else {
            $stmt = $this->conn->prepare("SELECT U.Id_Usuario, E.DNI_Estudiante, A.Grado, A.Seccion, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3, U.Estado FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario INNER JOIN T_Aulas AS A ON E.Id_Aula = A.Id_Aula WHERE U.Nombre_Usuario = :username");
        }
        $stmt->execute(['username' => $username]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        // Agrega la URL del objeto S3 al resultado si 'Foto_Perfil_Key_S3' no es nulo
        if ($student && $student['Foto_Perfil_Key_S3'] !== null) {
            $student['Foto_Perfil_URL'] = $this->s3Manager->getObjectUrl($student['Foto_Perfil_Key_S3']);
        }

        return $student;
    }

    public function getStudentCount($dni = null, $nombre = null, $apellidos = null, $grado = null, $seccion = null, $estado = null) {
    // Construye la consulta base para contar estudiantes
    $query = "SELECT COUNT(*) as count FROM T_Estudiantes";

    // Agrega la unión con la tabla de Aulas para aplicar los filtros de grado y sección
    $query .= " INNER JOIN T_Aulas ON T_Estudiantes.Id_Aula = T_Aulas.Id_Aula";

    // Agrega la unión con la tabla de Usuarios para aplicar los filtros de nombre y apellidos
    $query .= " INNER JOIN T_Usuarios ON T_Estudiantes.Id_Usuario = T_Usuarios.Id_Usuario";

    // Prepara un array para almacenar las condiciones de filtro
    $conditions = [];

    // Agrega las condiciones de filtro según los parámetros proporcionados
    if ($dni !== null) {
        $conditions[] = "T_Estudiantes.DNI_Estudiante LIKE '%$dni%'";
    }
    if ($nombre !== null) {
        $conditions[] = "T_Usuarios.Nombres LIKE '%$nombre%'";
    }
    if ($apellidos !== null) {
        $conditions[] = "T_Usuarios.Apellidos LIKE '%$apellidos%'";
    }
    if ($grado !== null) {
        $conditions[] = "T_Aulas.Grado LIKE '%$grado%'";
    }
    if ($seccion !== null) {
        $conditions[] = "T_Aulas.Seccion LIKE '%$seccion%'";
    }
    if ($estado !== null) { // Agrega el filtro por estado si se proporciona
        $conditions[] = "T_Usuarios.Estado = '$estado'";
    }

    // Si hay condiciones de filtro, agregalas a la consulta
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Ejecuta la consulta y obtén el resultado
    $stmt = $this->conn->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Extrae el conteo de la fila de resultado
    $count = $result['count'];

    return $count;
}

    public function create($DNI_Estudiante, $Id_Usuario, $Id_Aula)
    {
        $stmt = $this->conn->prepare("INSERT INTO T_Estudiantes (DNI_Estudiante, Id_Usuario, Id_Aula) VALUES (:DNI_Estudiante, :Id_Usuario, :Id_Aula)");
        return $stmt->execute(['DNI_Estudiante' => $DNI_Estudiante, 'Id_Usuario' => $Id_Usuario, 'Id_Aula' => $Id_Aula]);        
    }

    public function getCursosByDNI($DNI_Estudiante)
    {
        $stmt = $this->conn->prepare("SELECT C.* FROM T_Cursos AS C INNER JOIN T_Cursos_Aula AS CA ON C.Id_Curso = CA.Id_Curso INNER JOIN T_Aulas AS A ON CA.Id_Aula = A.Id_Aula INNER JOIN T_Estudiantes AS E ON A.Id_Aula = E.Id_Aula WHERE E.DNI_Estudiante = :DNI_Estudiante");
        $stmt->execute(['DNI_Estudiante' => $DNI_Estudiante]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function update($DNI_Estudiante, $Id_Usuario, $Id_Aula)
    {
        $stmt = $this->conn->prepare("UPDATE T_Estudiantes SET Id_Usuario = :Id_Usuario, Id_Aula = :Id_Aula WHERE DNI_Estudiante = :DNI_Estudiante");
        return $stmt->execute(['DNI_Estudiante' => $DNI_Estudiante, 'Id_Usuario' => $Id_Usuario, 'Id_Aula' => $Id_Aula]);

    }

    public function delete($DNI_Estudiante)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Estudiantes WHERE DNI_Estudiante = :DNI_Estudiante");
        $stmt->execute(['DNI_Estudiante' => $DNI_Estudiante]);
        return $stmt->rowCount();
    }
}
