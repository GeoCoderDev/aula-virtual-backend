<?php
use Config\S3Manager;
require_once __DIR__ . '/../models/Estudiante.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../lib/helpers/encriptations/userEncriptation.php';
require_once __DIR__.'/../config/S3Manager.php';
require_once __DIR__.'/../lib/helpers/functions/generateProfilePhotoKeyS3.php';

class EstudianteController
{
    
    public function getAll($includePassword = false, $limit = 200, $startFrom = 0, $dni = null, $nombre = null, $apellidos = null, $grado = null, $seccion = null)
    {
        $estudianteModel = new Estudiante();
        $estudiantes = $estudianteModel->getAll($includePassword, $limit, $startFrom, $dni, $nombre, $apellidos, $grado, $seccion);
        return $estudiantes;
    }


    public function getStudentCount($dni = null, $nombre = null, $apellidos = null, $grado = null, $seccion = null) {
        $estudianteModel = new Estudiante();
        // Pasar los parámetros de consulta al modelo para obtener el conteo de estudiantes
        $count = $estudianteModel->getStudentCount($dni, $nombre, $apellidos, $grado, $seccion);
        return $count;
    }


    public function getByDNI($DNI_Estudiante)
    {
        $estudianteModel = new Estudiante();
        $estudiante = $estudianteModel->getByDNI($DNI_Estudiante);
        return json_encode($estudiante);
    }

    public function validateDNIAndUsername($data) {
    $estudianteModel = new Estudiante();
    $estudianteFinded = $estudianteModel->getByDNI($data->DNI_Estudiante);

    if ($estudianteFinded && $estudianteFinded["Nombre_Usuario"]==$data->Username_Estudiante) {
        return $estudianteFinded;      
    }

    return false;
    }

    public function create($data)
{
    // Definir los campos requeridos
    $requiredFields = ['DNI_Estudiante', 'Nombres', 'Apellidos', 'Fecha_Nacimiento', 'Nombre_Usuario', 'Contraseña_Usuario', 'Direccion_Domicilio', 'Nombre_Contacto_Emergencia', 'Parentezco_Contacto_Emergencia', 'Telefono_Contacto_Emergencia', 'Grado', 'Seccion'];
    
    // Verificar si todos los campos requeridos están presentes en $data
    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            // Devolver una respuesta JSON indicando el campo que falta
            Flight::json(["message" => "Falta el campo obligatorio: $field"], 400);
            return;
        }
    }

    // Si todos los campos requeridos están presentes, continuar con la lógica para insertar en la base de datos
    $DNI_Estudiante = $data['DNI_Estudiante'];
    $Nombres = $data['Nombres'];
    $Apellidos = $data['Apellidos'];
    $Fecha_Nacimiento = $data['Fecha_Nacimiento'];
    $Nombre_Usuario = $data['Nombre_Usuario'];
    $Contraseña_Usuario = $data['Contraseña_Usuario'];
    $Direccion_Domicilio = $data['Direccion_Domicilio'];
    $Nombre_Contacto_Emergencia = $data['Nombre_Contacto_Emergencia'];
    $Parentezco_Contacto_Emergencia = $data['Parentezco_Contacto_Emergencia'];
    $Telefono_Contacto_Emergencia = $data['Telefono_Contacto_Emergencia'];
    $Grado = $data['Grado'];
    $Seccion = $data['Seccion'];
    $Foto_Perfil_Key_S3 = null;

    $estudianteModel = new Estudiante();
    $existingEstudiante = $estudianteModel->getByDNI($DNI_Estudiante);

    if ($existingEstudiante) {
        Flight::json(["message" => "Ya existe un estudiante con ese DNI"], 409);
        return;
    }

    // Verificar si se ha enviado la foto de perfil
    if(isset($_FILES['Foto_Perfil']) && $_FILES['Foto_Perfil']['error'] === UPLOAD_ERR_OK) {
        // Obtener la información de la foto de perfil
        $fotoPerfil = $_FILES['Foto_Perfil'];
        $extension = pathinfo($fotoPerfil['name'], PATHINFO_EXTENSION); // Obtener la extensión del archivo

        // Validar la extensión del archivo
        $allowedExtensions = ['jpg', 'jpeg', 'png']; // Extensiones permitidas
        if (!in_array(strtolower($extension), $allowedExtensions)) {
            Flight::json(["message" => "La extensión del archivo de la foto de perfil no es válida. Solo se permiten archivos jpg, jpeg y png."], 400);
            return;
        }

        $Foto_Perfil_Key_S3 = generateProfilePhotoKeyS3($Nombre_Usuario, $DNI_Estudiante, $extension);
        
        // Creando un cliente de S3
        $s3Manager = new S3Manager();

        // Ruta temporal del archivo
        $tempFilePath = $fotoPerfil['tmp_name'];

        // Subir el archivo al bucket de S3
        $uploadResult = $s3Manager->uploadFile($tempFilePath, $Foto_Perfil_Key_S3);

        if(!$uploadResult) {
            Flight::json(["message" => "Error al subir la foto de perfil"], 500);
            return;
        }       
    }

    $aulaController = new AulaController();

    // Obtener el ID del aula correspondiente al grado y la sección
    $aula = $aulaController->getByGradoSeccion($Grado, $Seccion);

    if (!$aula) {
        Flight::json(["message" => "No se encontró el aula correspondiente al grado $Grado y la sección $Seccion"], 404);
        return;
    }

    $Id_Aula = $aula['Id_Aula'];

    $usuarioModelo = new Usuario();
    $existingUsuario = $usuarioModelo->getByUsername($Nombre_Usuario);

    if ($existingUsuario) {
        Flight::json(["message" => "Ya existe un usuario con ese nombre de usuario"], 409);
        return;
    }

    $Id_Usuario = $usuarioModelo->create(
        $Nombres,
        $Apellidos,
        $Fecha_Nacimiento,
        $Nombre_Usuario,
        encryptUserPassword($Contraseña_Usuario),
        $Direccion_Domicilio,
        $Nombre_Contacto_Emergencia,
        $Parentezco_Contacto_Emergencia,
        $Telefono_Contacto_Emergencia,
        $Foto_Perfil_Key_S3
    );

    if ($Id_Usuario) {
        $success = $estudianteModel->create($DNI_Estudiante, $Id_Usuario, $Id_Aula);
        if ($success) {
            Flight::json(["message" => "Estudiante creado"], 201);
        } else {
            Flight::json(["message" => "Error al crear el estudiante"], 500);
        }
    } else {
        Flight::json(["message" => "Error al crear el usuario"], 500);
    }
}


    public function getCursosByDNI($DNI_Estudiante)
    {
        $estudianteModel = new Estudiante();
        $cursos = $estudianteModel->getCursosByDNI($DNI_Estudiante);
        return $cursos;
    }


    public function update($DNI_Estudiante, $data)
    {
        $Id_Usuario = $data['Id_Usuario'] ?? null;
        if (!$Id_Usuario) {
            return json_encode(["message" => "Id_Usuario debe ser proporcionado para actualizar"]);
        }
        $Id_Aula = $data['Id_Aula'];

        $estudianteModel = new Estudiante();
        $rowCount = $estudianteModel->update($DNI_Estudiante, $Id_Usuario, $Id_Aula);

        if ($rowCount > 0) {
            return json_encode(["message" => "Estudiante actualizado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún estudiante con el DNI proporcionado"]);
        }
    }

    public function delete($DNI_Estudiante)
    {
        $estudianteModel = new Estudiante();
        $rowCount = $estudianteModel->delete($DNI_Estudiante);

        if ($rowCount > 0) {
            return json_encode(["message" => "Estudiante eliminado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún estudiante con el DNI proporcionado"]);
        }
    }
}
