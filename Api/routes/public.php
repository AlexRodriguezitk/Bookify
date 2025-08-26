<?php

namespace routes;

use Flight;

use App\controllers\InstallController;

Flight::route('POST /install', [new InstallController(), 'Install']);

//GET STATUS return the status of the api instalation
Flight::route('GET /', [new InstallController(), 'status']);


// Manejo de rutas 404 con verificación de .htaccess
Flight::map('notFound', function () {
    $installController = new InstallController();
    $installController->setHtaccess(); // Verifica y actualiza .htaccess si es necesario

    Flight::json([
        'message' => '404 Not Found',
        'error' => 'The page you have requested could not be found.'
    ], 404);
});
