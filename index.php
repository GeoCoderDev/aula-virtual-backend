<?php

    require './vendor/autoload.php';

    Flight::route('GET /api', function () {
        echo '¡Hola Mundo!';
    });


    
    // Finalmente, inicia el framework.
    Flight::start();


?>