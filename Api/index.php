<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Auth;
use App\database\Database;
use App\Controllers\InstallController;

// ✅ Configuración centralizada
$config = require __DIR__ . '/App/config/config.php';

// Variables desde config.php (ya procesadas desde .env o entorno)
$baseUrl      = $config['base_url'];
$is_installed = $config['is_installed'];

// Crear instancia de base de datos (sin forzar la conexión)
$db = Database::getInstance();

// Middleware para verificar autenticación y conexión a la BD
Flight::before('start', function () use ($db, $is_installed) {
    $route = Flight::request()->url;

    // ✅ Rutas públicas (sin auth)
    $publicRoutes = [
        '/auth/login/password',
        '/auth/login/verify-2fa',
        '/auth/register',
        '/install',
        '/status',
        '/',
    ];

    // Si la base de datos falla y no estamos en /install
    if ($db->hasError() && $route !== '/install') {
        $controller = new InstallController();
        if ($is_installed) {
            $controller->handle($db->getError());
        } else {
            $controller->handle(
                'Database is not installed. Please run POST /api/install to set it up.'
            );
        }
        Flight::stop();
        return;
    }

    // Autenticación para rutas privadas
    if (!in_array($route, $publicRoutes)) {
        Auth::requireAuth();
    }
});

// Cargar rutas
require 'Routes/router.php';

// Iniciar Flight
Flight::start();
