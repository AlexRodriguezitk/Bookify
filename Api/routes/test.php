<?php

namespace routes;

use App\Modelsv2\UserModel;
use App\Repositories\UsersRep;
use App\traits\ApiResponse;
use App\Langs\LangManager;
use DateTime;
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


Flight::route('POST /test', function () use ($api) {
    try {
        $params = json_decode(Flight::request()->getBody(), true);

        $user = UsersRep::fromArray($params);

        $UserModel = new UserModel();
        $user = $UserModel->Create($user);

        return $api->success($user->jsonSerialize(), "OK");
    } catch (\InvalidArgumentException $e) {
        return $api->failed(null, $e->getMessage());
    } catch (\Exception $e) {
        return $api->failed(null, "Error inesperado: " . $e->getMessage());
    }
});

Flight::route('POST /test/up/@id', function ($id) use ($api) {
    try {
        $params = json_decode(Flight::request()->getBody(), true);
        $UserModel = new UserModel();
        $user = $UserModel->get($id);
        $user = UsersRep::fromArray($params, false, $user);
        $user = $UserModel->Update($user);

        return $api->success($user->jsonSerialize(), "OK");
    } catch (\InvalidArgumentException $e) {
        return $api->failed(null, $e->getMessage());
    } catch (\Exception $e) {
        return $api->failed(null, "Error inesperado: " . $e->getMessage());
    }
});


// Supongamos que $api ya es tu objeto con ApiResponse
// Flight::route('GET /test', function () use ($api) {

//     $lang = new LangManager();

//     try {
//         $UserModel = new UserModel();
//         $users = $UserModel->getAll();
//         $usersSerialized = array_map(fn($u) => $u->jsonSerialize(), $users);
//         $count = $UserModel->CountQuery();

//         // Mensaje dinámico con placeholders
//         $message = $lang->get('results_found', ['x' => $count]);

//         // Devolver éxito con datos y metadatos
//         return $api->success(
//             $usersSerialized,
//             $message,
//             null,
//             ['total' => $count]
//         );
//     } catch (\Exception $e) {
//         // Mensaje de error dinámico
//         $errorMsg = $lang->get('error_with_code', [
//             'code' => $e->getCode(),
//             'description' => $e->getMessage()
//         ]);
//         return $api->failed(null, $errorMsg, 500);
//     }
// });
