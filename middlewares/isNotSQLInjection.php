<?php

class NotSQLInjection {
    
    public function before($params) {
        header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);

        // Obtener los datos de la solicitud
        $data = Flight::request()->data->getData();
        // Obtener los parámetros de consulta
        $queryParams = Flight::request()->query->getData();

        // Verificar si los datos de la solicitud contienen inyección SQL
        if ($this->containsSQLInjection($data) || $this->containsSQLInjection($queryParams)) {
            Flight::halt(400, json_encode(["message" => "Los datos de la solicitud contienen una posible inyección SQL"]));
        }
    }

    private function containsSQLInjection($data) {
        // Verificar recursivamente los datos
        return $this->checkForSQLInjection($data);
    }

    private function checkForSQLInjection($data) {
        // Lista de palabras clave de SQL para buscar en los valores
        $sqlKeywords = ["SELECT", "INSERT", "UPDATE", "DELETE", "DROP", "ALTER", "TRUNCATE"];

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($this->checkForSQLInjection($value)) {
                    return true; // Se encontró una posible inyección SQL en un array anidado
                }
            }
        } else {
            // Convertir el valor a cadena
            $value = strval($data);
            // Verificar si alguna palabra clave de SQL está presente en el valor
            foreach ($sqlKeywords as $keyword) {
                if (stripos($value, $keyword) !== false) {
                    return true; // Se encontró una posible inyección SQL
                }
            }
        }

        return false; // No se encontraron inyecciones SQL
    }
}

?>
