<?php

namespace routes;

use App\Modelsv2\UserModel;
use App\traits\ApiResponse;

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

Flight::route('GET /test/@id', function ($id) use ($api) {

    $user = (new UserModel())->get($id);
    $params = Flight::request()->query;

    if (!$user) {
        $api->failed(null, 'User not found', 404);
        return;
    }

    if ($params['password'] == 'true') {
        $api->success(['password' => $user->getPassword()], 'User found', 200);
    } else {
        $api->success([$user->jsonSerialize()], 'User found', 200);
    }
});
