<?php

namespace routes;

use App\controllers\UserController;
use App\controllers\RolController;
use App\controllers\TerminalController;
use App\controllers\CategoryController;
use App\controllers\CTFieldsController;
use App\controllers\TicketController;
use App\controllers\InteractionController;
use App\controllers\LogController;
use App\controllers\PermissionController;


use Flight;



//rutas Api Grupo
Flight::group('/api', function () {

//Rutas de Autenticacion
Flight::route('POST /login', [new UserController(), 'login']);
Flight::route('POST /register', [new UserController(), 'register']);

//Rutas usuarios

Flight::route('GET /users', [new UserController(), 'index']);
Flight::route('GET /users/@id', [new UserController(), 'show']);
Flight::route('POST /users', [new UserController(), 'store']);
Flight::route('PUT /users/@id', [new UserController(), 'update']);
Flight::route('DELETE /users/@id', [new UserController(), 'destroy']);
Flight::route('GET /users/@id/inactive', [new UserController(), 'inactive']);
Flight::route('GET /users/@id/active', [new UserController(), 'active']);



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


//Categorias
Flight::route('GET /categories', [new CategoryController(), 'index']);
Flight::route('GET /categories/@id', [new CategoryController(), 'show']);
Flight::route('POST /categories', [new CategoryController(), 'store']);
Flight::route('PUT /categories/@id', [new CategoryController(), 'update']);
Flight::route('DELETE /categories/@id', [new CategoryController(), 'destroy']);

//Tickets
//Grupo
Flight::group('/tickets', function (){
    //Custom Fields
    Flight::route('GET /fields', [new CTFieldsController(), 'index']);
    Flight::route('GET /fields/@id', [new CTFieldsController(), 'show']);
    Flight::route('POST /fields', [new CTFieldsController(), 'store']);
    Flight::route('PUT /fields/@id', [new CTFieldsController(), 'update']);
    Flight::route('DELETE /fields/@id', [new CTFieldsController(), 'destroy']);
    Flight::route('GET /fields/category/@id', [new CTFieldsController(), 'getFieldsByCategory']);

    //Interactions
    Flight::route('GET /interactions', [new InteractionController(), 'index']);
    Flight::route('GET /interactions/@id', [new InteractionController(), 'show']);
    Flight::route('POST /interactions', [new InteractionController(), 'store']);
    Flight::route('PUT /interactions/@id', [new InteractionController(), 'update']);
    Flight::route('DELETE /interactions/@id', [new InteractionController(), 'destroy']);
    Flight::route('GET /@id/interactions', [new InteractionController(), 'getInteractionsByTicket']);

    //Tickets
    Flight::route('GET /', [new TicketController(), 'index']);
    Flight::route('GET /@id', [new TicketController(), 'show']);
    Flight::route('POST /', [new TicketController(), 'store']);
    Flight::route('PUT /@id', [new TicketController(), 'update']);
    Flight::route('DELETE /@id', [new TicketController(), 'destroy']);
    Flight::route('GET /@id/assign/@userId', [new TicketController(), 'assing']);
});


//Logs
Flight::route('GET /logs', [new LogController(), 'index']);
Flight::route('GET /logs/@id', [new LogController(), 'show']);
Flight::route('DELETE /logs', [new LogController(), 'clear']);

//Permission
Flight::route('GET /permissions', [new PermissionController(), 'index']);
Flight::route('GET /permissions/@id', [new PermissionController(), 'show']);
Flight::route('POST /permissions', [new PermissionController(), 'store']);
Flight::route('PUT /permissions/@id',[new PermissionController(), 'update']);
Flight::route('DELETE /permissions/@id',[new PermissionController(), 'destroy']);
Flight::route('POST /permissions/assign/@id', [new PermissionController(), 'assing']);
Flight::route('POST /permissions/unassign/@id', [new PermissionController(), 'unassing']);
Flight::route('GET /permissions/Assignments/@id', [new PermissionController(), 'getAssignments']);
});