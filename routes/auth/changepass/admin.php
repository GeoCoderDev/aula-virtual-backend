<?php

require_once __DIR__."/../../../controllers/Admin.php";
require_once __DIR__ ."/../../../lib/helpers/JWT/JWT_Admin.php";

Flight::route("PUT /api/auth/admin/me/password", function() {
    $controller = new AdminController();

    // Ejecutar el middleware de autorización de administrador antes de procesar la solicitud
    $adminAuth = new AdminAuthenticated();
    $adminAuth->before([]);

    // Obtener los datos de la solicitud
    $data = Flight::request()->data->getData();

    // Llamar al método updatePassword() del controlador AdminController con los datos de la nueva contraseña
    $validateResponse = $controller->updatePassword($data);

    if (is_array($validateResponse)) {
        // Obtener el nombre de usuario del administrador
        $username = $validateResponse["Nombre_Usuario"];

        // Devolver un mensaje de éxito junto con el nombre de usuario del administrador
        Flight::json(["message" => "Contraseña actualizada correctamente para el administrador $username"], 200);
    } else {
        // Si la actualización de la contraseña falla, devolver un mensaje de error
        Flight::json(['message' => 'No se pudo actualizar la contraseña del administrador'], 500);//HOLI
    }
});

?>
