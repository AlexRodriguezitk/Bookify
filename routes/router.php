<?php

namespace routes;

use App\controllers\UserController;
use App\controllers\RolController;
use App\controllers\TerminalController;


use Flight;



//rutas Api Grupo
Flight::group('/api', function () {
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

//terminales
Flight::route('GET /terminals', [new TerminalController(), 'index']);
Flight::route('GET /terminals/@id', [new TerminalController(), 'show']);
Flight::route('POST /terminals', [new TerminalController(), 'store']);
Flight::route('PUT /terminals/@id', [new TerminalController(), 'update']);
Flight::route('DELETE /terminals/@id', [new TerminalController(), 'destroy']);
Flight::route('POST /terminals/assign/@id', [new TerminalController(), 'assing']);
Flight::route('POST /terminals/unassign/@id', [new TerminalController(), 'unassing']);
Flight::route('GET /terminals/Assignments/@id', [new TerminalController(), 'getAssignments']);

});