<?php

namespace routes;

use App\controllers\UserController;
use App\controllers\RolController;


use Flight;



//Rutas usuarios
Flight::route('GET /users', [new UserController(), 'index']);
Flight::route('GET /users/@id', [new UserController(), 'show']);
Flight::route('POST /users', [new UserController(), 'store']);
Flight::route('PUT /users/@id', [new UserController(), 'update']);
Flight::route('DELETE /users/@id', [new UserController(), 'destroy']);



//Rutas roles
Flight::route('GET /roles', [new RolController(), 'index']);
Flight::route('GET /roles/@id', [new RolController(), 'show']);
Flight::route('POST /roles', [new RolController(), 'store']);
Flight::route('PUT /roles/@id', [new RolController(), 'update']);
Flight::route('DELETE /roles/@id', [new RolController(), 'destroy']);
