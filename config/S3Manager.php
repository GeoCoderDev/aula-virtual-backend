<?php

namespace Config;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;

class S3Manager
{
    private $s3;

    public function __construct()
    {
        // Carga las variables de entorno desde el archivo .env
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();

        // Configura las credenciales de AWS
        $credentials = new Credentials($_ENV["AWS_ACCESS_KEY_ID"], $_ENV["AWS_SECRET_ACCESS_KEY"]);

        // Crea un cliente S3 y lo asigna al campo de clase $s3
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region'  => $_ENV["AWS_REGION"],
            'credentials' => $credentials
        ]);
    }

    public function listObjects()
    {
        $bucketName = $_ENV["AWS_BUCKET_NAME"];

        try {
            // Lista los archivos en el bucket
            $objects = $this->s3->listObjects([
                'Bucket' => $bucketName
            ]);

            return $objects['Contents'];
        } catch (AwsException $e) {
            return false;
        }
    }

    public function getObjectUrl($key)
    {
        $bucketName = $_ENV["AWS_BUCKET_NAME"];

        try {
            // Obtiene la URL del objeto en el bucket
            $result = $this->s3->getObjectUrl($bucketName, $key);

            return $result;
        } catch (AwsException $e) {
            return false;
        }
    }

    public function uploadFile($filePath, $key)
    {
        $bucketName = $_ENV["AWS_BUCKET_NAME"];

        try {
            // Sube el archivo al bucket
            $result = $this->s3->putObject([
                'Bucket' => $bucketName,
                'Key' => $key,
                'Body' => fopen($filePath, 'rb'),
                'ACL' => 'public-read' // Opcional: cambia los permisos del objeto si lo necesitas
            ]);

            return $result;
        } catch (AwsException $e) {
            echo $e;
            return false;
        }
    }

    public function deleteObject($key)
    {
        $bucketName = $_ENV["AWS_BUCKET_NAME"];

        try {
            // Elimina el objeto del bucket
            $result = $this->s3->deleteObject([
                'Bucket' => $bucketName,
                'Key' => $key
            ]);

            return $result;
        } catch (AwsException $e) {
            return false;
        }
    }
}

?>
