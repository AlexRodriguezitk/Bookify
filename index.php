<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Auth;

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Middleware para proteger rutas
Flight::before('start', function () {
    $route = Flight::request()->url;
    
    // Rutas públicas (no requieren autenticación)
    $publicRoutes = [
        '/api/login',
        '/api/register'
    ];

    // Si la ruta no está en la lista de rutas públicas, requiere autenticación
    if (!in_array($route, $publicRoutes)) {
        App\Auth::requireAuth();
    }
});

// Cargar rutas
require 'routes/router.php';

// Iniciar Flight
Flight::start();