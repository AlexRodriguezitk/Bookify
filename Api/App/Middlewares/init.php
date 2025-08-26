<?php

namespace App\Middlewares;

use App\Database\Database;
use App\Controllers\InstallController;
use App\Middlewares\Auth;
use Flight;

class Init
{
    private Database $db;
    private bool $isInstalled;

    private array $publicRoutes = [
        '/',
        '/install',
        '/status',
        '/auth/register',
        '/auth/login/password',
        '/auth/login/verify-2fa',
    ];

    private array $publicPrefixes = [
        '/test',   // /test, /test/123, etc.
        '/docs',   // /docs/*
    ];

    public function __construct(Database $db, bool $isInstalled)
    {
        $this->db = $db;
        $this->isInstalled = $isInstalled;
    }

    public function register(): void
    {
        Flight::before('start', function () {
            $this->handle();
        });
    }

    private function handle(): void
    {
        $route = Flight::request()->url;

        // 1️⃣ Validar conexión con la base de datos
        if ($this->db->hasError() && $route !== '/install') {
            $this->handleDatabaseError();
            return;
        }

        // 2️⃣ Validar si la ruta es pública
        if ($this->isPublicRoute($route)) {
            return; // no requiere autenticación
        }

        // 3️⃣ Exigir autenticación en rutas privadas
        Auth::requireAuth();
    }

    private function handleDatabaseError(): void
    {
        $controller = new InstallController();

        $message = $this->isInstalled
            ? $this->db->getError()
            : 'Database is not installed. Please run POST /api/install to set it up.';

        $controller->handle($message);
        Flight::stop();
    }

    private function isPublicRoute(string $route): bool
    {
        // Coincidencia exacta
        if (in_array($route, $this->publicRoutes, true)) {
            return true;
        }

        // Coincidencia por prefijo
        foreach ($this->publicPrefixes as $prefix) {
            if (str_starts_with($route, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
