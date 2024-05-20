<?php
function isSQLInjection($cadena) {
    // Patrones comunes de inyección SQL
    $patrones = array(
        "/\b(SELECT|UPDATE|DELETE)\b/i",  // Palabras clave de consulta
        "/\b(INSERT INTO|DROP TABLE|ALTER TABLE)\b/i",  // Palabras clave de modificación de tabla
        "/\b(UNION\s+SELECT|SELECT\s+.*\s+FROM|INSERT\s+INTO.*\s+VALUES)\b/i",  // Sentencias SQL compuestas
        "/\b(AND\s+\d+\s*=\s*\d+|OR\s+\d+\s*=\s*\d+|HAVING\s+\d+\s*=\s*\d+)\b/i"  // Operadores lógicos de comparación
    );

    // Comprueba si la cadena coincide con algún patrón de inyección SQL
    foreach ($patrones as $patron) {
        if (preg_match($patron, $cadena)) {
            return true; // Se encontró un patrón de inyección SQL
        }
    }
    
    return false; // No se encontraron patrones de inyección SQL
}
