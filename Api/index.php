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
$is_installed = $config['is_installed'];

// Crear instancia de base de datos (sin forzar la conexión)
$db = Database::getInstance();

// Middleware para verificar autenticación y conexión a la BD
Flight::before('start', function () use ($db, $is_installed) {
    $route = Flight::request()->url;
    $publicRoutes = [
        '/auth/login',
        '/auth/register',
        '/install',
        '/status',
        '/'
    ];

    // Si la base de datos falla y no estamos en /api/install, redirigir al controlador
    if ($db->hasError() && $route !== '/install') {
        $controller = new InstallController();
        if ($is_installed) {
            $controller->handle($db->getError()); // Llamar al controlador
            Flight::stop();
            return;
        } {
            $controller->handle('Database is not installed. Please run POST /api/install to set it up.'); // Llamar al controlador
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
