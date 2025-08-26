<?php

namespace routes;

use App\Modelsv2\UserModel;
use App\traits\ApiResponse;
use App\Langs\LangManager;

use Flight;

class ApiHelper
{
    use ApiResponse;
}

$api = new ApiHelper();

Flight::route('GET /ping', function () use ($api) {
    return $api->success(["pong" => true], "OK");
});

Flight::route('GET /fail', function () use ($api) {
    return $api->failed(null, "Error simulado");
});

// Flight::route('GET /test', function () use ($api) {
//     $UserModel = new UserModel();
//     $user = $UserModel->getAll();
//     $user_serializeds = array_map(fn($u) => $u->jsonSerialize(), $user);
//     $count = $UserModel->CountQuery();
//     return $api->success($user_serializeds, "OK", null, ['total' => $count]);
// });


// Supongamos que $api ya es tu objeto con ApiResponse
Flight::route('GET /test', function () use ($api) {

    $lang = new LangManager();

    try {
        $UserModel = new UserModel();
        $users = $UserModel->getAll();
        $usersSerialized = array_map(fn($u) => $u->jsonSerialize(), $users);
        $count = $UserModel->CountQuery();

        // Mensaje dinámico con placeholders
        $message = $lang->get('results_found', ['x' => $count]);

        // Devolver éxito con datos y metadatos
        return $api->success(
            $usersSerialized,
            $message,
            null,
            ['total' => $count]
        );
    } catch (\Exception $e) {
        // Mensaje de error dinámico
        $errorMsg = $lang->get('error_with_code', [
            'code' => $e->getCode(),
            'description' => $e->getMessage()
        ]);
        return $api->failed(null, $errorMsg, 500);
    }
});
