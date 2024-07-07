<?php

require_once __DIR__ . "/../models/Foro.php";

class ForoController
{
    private $foroModel;

    public function __construct()
    {
        $this->foroModel = new Foro();
    }

    public function getForumData($Id_Foro, $DNI_Profesor = null, $DNI_Estudiante = null)
    {

        $foroDetails = $this->foroModel->getForumDetails($Id_Foro);

        if (!$foroDetails) {
            Flight::json(["message" => "Foro no encontrado"], 404);
            return;
        }

        $responses = $this->foroModel->getStudentsWhoRespondedByForumId($Id_Foro);

        $isTeacher = $DNI_Profesor !== null;
        $isAnswered = false;

        if ($DNI_Estudiante !== null) {
            foreach ($responses as $response) {
                if ($response['DNI_Estudiante'] === $DNI_Estudiante) {
                    $isAnswered = true;
                    break;
                }
            }
        }

        // Estructurar respuestas para incluir los datos del estudiante
        $formattedResponses = [];
        foreach ($responses as $response) {
            $formattedResponses[] = [
                "Id_Respuesta_Foro" => $response['Id_Respuesta_Foro'],
                "Respuesta" => $response['Contenido_Respuesta'],
                "Estudiante" => [
                    "DNI_Estudiante" => $response['DNI_Estudiante'],
                    "Id_Usuario" => $response['Id_Usuario'],
                    "Nombres" => $response['Nombres'],
                    "Apellidos" => $response['Apellidos'],
                    "Estado" => $response['Estado'],
                    "Foto_Perfil_URL" => $response['Foto_Perfil_URL'] ?? null
                ]
            ];
        }

        $response = [
            "Grado" => $foroDetails['Grado'],
            "Seccion" => $foroDetails['Seccion'],
            "Nombre_Curso" => $foroDetails['Nombre_Curso'],
            "Nombre_Tema" => $foroDetails['Nombre_Tema'],
            "Id_Foro" => $foroDetails['Id_Foro'],
            "Titulo" => $foroDetails['Titulo'],
            "Descripcion_Imagen_URL" => $foroDetails['Descripcion_Imagen_URL'] ?? null,
            "Descripcion_Recurso" => $foroDetails['Descripcion_Recurso'],
            "Respuestas" => $formattedResponses,
            "isTeacher" => $isTeacher,
            "isAnswered" => $isAnswered
        ];

        Flight::json($response);
    }
}
