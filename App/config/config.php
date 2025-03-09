<?php
// Cargar la librería dotenv
require_once __DIR__ . '../../../vendor/autoload.php';

// Cargar el archivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Configuración
return [
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? null,
        'name' => $_ENV['DB_NAME'] ?? null,
        'user' => $_ENV['DB_USER'] ?? null,
        'password' => $_ENV['DB_PASSWORD'] ?? null
    ],
    'base_url' => rtrim($_ENV['BASE_URL'] ?? null, '/'), // Obtener la URL base desde .env
    'is_installed' => $_ENV['IS_INSTALLED'] ?? null // Obtener la URL base desde .env
];
