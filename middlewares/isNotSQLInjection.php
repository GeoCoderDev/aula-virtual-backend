<?php


class NotSQLInjection {
    
    public function before($params) {
        // Obtener los datos de la solicitud
        $data = Flight::request()->data->getData();

        // Verificar si los datos contienen inyección SQL
        if ($this->containsSQLInjection($data)) {
            Flight::halt(400, json_encode(["message" => "Los datos contienen una posible inyección SQL"]));
        }
    }

    private function containsSQLInjection($data) {
        // Convertir los valores del array en cadenas para verificar
        $values = array_map('strval', $data);
        
        // Lista de palabras clave de SQL para buscar en los valores
        $sqlKeywords = ["SELECT", "INSERT", "UPDATE", "DELETE", "DROP", "ALTER", "TRUNCATE"];

        // Verificar si alguna palabra clave de SQL está presente en los valores
        foreach ($values as $value) {
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