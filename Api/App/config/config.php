<?php
// Cargar la librería dotenv
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// Ruta donde debería estar el .env
$envPath = __DIR__ . '/../../';
$envFile = $envPath . '.env';

// 1. Cargar archivo .env si existe
if (file_exists($envFile)) {
    $dotenv = Dotenv::createImmutable($envPath);
    $dotenv->load();
} else {
    // 2. Si no existe, probar variables de entorno
    if (
        getenv('DB_HOST') === false &&
        getenv('DB_NAME') === false &&
        getenv('DB_USER') === false &&
        getenv('DB_PASSWORD') === false &&
        getenv('BASE_URL') === false &&
        getenv('IS_INSTALLED') === false &&
        getenv('APP_LOCALE') === false
    ) {
        // 3. Si tampoco hay, crear un .env vacío
        file_put_contents($envFile, "# Archivo .env creado automáticamente\n");
    }
}

// 4. Configuración centralizada
return [
    'db' => [
        'host'     => $_ENV['DB_HOST']     ?? getenv('DB_HOST')     ?: null,
        'name'     => $_ENV['DB_NAME']     ?? getenv('DB_NAME')     ?: null,
        'user'     => $_ENV['DB_USER']     ?? getenv('DB_USER')     ?: null,
        'password' => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: null,
    ],
    'locale'       => $_ENV['APP_LOCALE'] ?? getenv('APP_LOCALE') ?: 'es-col',
    'base_url'     => rtrim($_ENV['BASE_URL'] ?? getenv('BASE_URL') ?: '', '/'),
    'is_installed' => $_ENV['IS_INSTALLED'] ?? getenv('IS_INSTALLED') ?: null,
];
