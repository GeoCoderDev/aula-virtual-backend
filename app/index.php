<?php

require '../vendor/autoload.php';

use Dotenv\Dotenv;

// Carga las variables de entorno desde el archivo .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Configura las credenciales de AWS
$credentials = new Aws\Credentials\Credentials($_ENV["AWS_ACCESS_KEY_ID"], $_ENV["AWS_SECRET_ACCESS_KEY"]);

// Crea un cliente S3
$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => $_ENV["AWS_REGION"],
    'credentials' => $credentials
]);

// Nombre del bucket
$bucket_name = $_ENV["AWS_BUCKET_NAME"];

try {
    // Lista los archivos en el bucket
    $objects = $s3->listObjects([
        'Bucket' => $bucket_name
    ]);

    // Recorre los archivos
    foreach ($objects['Contents'] as $object) {
        echo $object['Key'] . "\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();    
}

?>