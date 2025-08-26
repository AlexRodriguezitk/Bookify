<?php

namespace routes;

use App\controllers\UserController;

use Flight;

Flight::group('/auth', function () {
    // Rutas de Autenticacion

    // 1. Ruta para el login de contraseña (primer paso)
    // Se renombra para reflejar su función, que es solo verificar la contraseña.
    Flight::route('POST /login/password', [new UserController(), 'loginPassword']);

    // 2. Nueva ruta para el segundo paso del login (verificación del código TOTP)
    // El frontend enviará el código OTP a este endpoint.
    Flight::route('POST /login/verify-2fa', [new UserController(), 'loginVerify2fa']);

    // Ruta para renovar el token (no necesita cambios)
    Flight::route('GET /renew', [new UserController(), 'renew']);

    // Ruta para el registro (no necesita cambios)
    Flight::route('POST /register', [new UserController(), 'register']);

    // Si la función GenPassword es para pruebas, no la cambies
    Flight::route('GET /generate', [new UserController(), 'GenPassword']);
    // En Flight.group('/auth', function () { ...
    Flight::route('GET /generate-totp-secret', [new UserController(), 'generateTotpSecret']);
    Flight::route('POST /enable-2fa', [new UserController(), 'enable2fa']);
});
