<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Auth;
use App\database\Database;
use App\Controllers\InstallController;

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtener configuración
$config = require __DIR__ . '/App/config/config.php';
$baseUrl = $config['base_url'];

// Crear instancia de base de datos (sin forzar la conexión)
$db = Database::getInstance();

// Middleware para verificar autenticación y conexión a la BD
Flight::before('start', function () use ($db, $baseUrl) {
    $route = Flight::request()->url;
    $publicRoutes = [
        '/api/auth/login',
        '/api/auth/register',
        '/api/install'
    ];

    // Si la base de datos falla y no estamos en /api/install, redirigir al controlador
    if ($db->hasError() && $route !== '/api/install') {
        $controller = new InstallController();
        if ($baseUrl) {
            $controller->handle($db->getError()); // Llamar al controlador
            Flight::stop();
            return;
        } {
            $controller->handle('DATABASE IS NOT INSTALLED, PLEASE RUN POST-> /API/INSTALL'); // Llamar al controlador
            Flight::stop();
            return;
        }
    }

    // Aplicar autenticación a rutas privadas
    if (!in_array($route, $publicRoutes)) {
        Auth::requireAuth();
    }
});

// Cargar rutas
require 'routes/router.php';


// Iniciar Flight
Flight::start();
