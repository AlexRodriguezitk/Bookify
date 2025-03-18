<?php

// Configurar la URL de la API
$url = "http://localhost/bookify/api/upload";

// Token de autenticación Bearer
$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDEzMDY4MzUsImV4cCI6MTc0MTMxMDQzNSwic3ViIjoxLCJyb2xlIjoxfQ._zF4foYQe3a9vSCbLHcDnI9CcGPFb37YjIKngvjhbUg";

// Archivo a subir (Cambia la ruta según tu archivo de prueba)
$filePath = "C:/Users/iTark/OneDrive/Imágenes/WhatsApp Image 2025-01-20 at 1.40-Photoroom.jpg";

// Verificar si el archivo existe
if (!file_exists($filePath)) {
    die("❌ El archivo no existe: $filePath");
}

// Crear el array con el archivo
$file = new CURLFile($filePath, mime_content_type($filePath), basename($filePath));
$postData = ["file" => $file];

// Inicializar cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Ejecutar la petición y obtener la respuesta
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Mostrar respuesta
if ($httpCode === 200) {
    echo "✅ Archivo subido con éxito:\n$response";
} else {
    echo "❌ Error en la subida (HTTP $httpCode):\n$response";
}

?>
