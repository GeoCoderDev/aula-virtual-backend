<?php

function areFieldsComplete($data, $requiredFields){

    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            // Devolver una respuesta JSON indicando el campo que falta
            Flight::json(["message" => "Falta el campo obligatorio: $field"], 400);
            return false;
        }
    }
    return true;
}