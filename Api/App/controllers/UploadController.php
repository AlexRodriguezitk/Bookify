<?php

namespace App\controllers;

use App\traits\ApiResponse;
use App\traits\HasPermissions;

use Flight;

class UploadController
{
    use ApiResponse;
    use HasPermissions;

    public function uploadFile()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'TICKET.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        // Obtener configuración
        $config = require __DIR__ . '/../config/config.php';
        $baseUrl = rtrim($config['base_url'], '/'); // Asegurar que no termine en /

        // Verifica si se subió un archivo
        if (!isset($_FILES['file'])) {
            Flight::json(["error" => "No se envió ningún archivo."], 400);
            return;
        }

        $file = $_FILES['file'];
        $filename = basename($file['name']);
        $fileTmpPath = $file['tmp_name'];

        // Verifica errores en la subida
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Flight::json(["error" => "Error al subir el archivo."], 500);
            return;
        }

        // Obtiene el año y mes actual
        $year = date("Y");
        $month = date("m");

        // Ruta de destino
        $uploadDir = __DIR__ . "/../../Content/Uploads/$year/$month/";

        // Crea la carpeta si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Asegurar un nombre único
        $uniqueName = uniqid() . "-" . str_replace(" ", "_", $filename);
        $filePath = $uploadDir . $uniqueName;

        // Mover archivo
        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $relativePath = "Content/Uploads/$year/$month/$uniqueName";
            $fullUrl = $baseUrl . "/" . $relativePath; // Construir URL final limpia

            // Respuesta sin caracteres escapados
            Flight::json([
                "success" => true,
                "file_url" => $fullUrl
            ], 200, true, 'application/json', JSON_UNESCAPED_SLASHES);
        } else {
            Flight::json(["error" => "Error al guardar el archivo."], 500);
        }
    }
}
