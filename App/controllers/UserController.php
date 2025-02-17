<?php

namespace App\controllers;

use App\traits\ApiResponse;
use App\models\User;
use Flight;


class UserController
{
    use ApiResponse;

    public function index()
    {
        $users = User::getAll();
        $this->success($users, 'Users list', 200);
    }

    public function show($id)
    {
        $user = User::get($id);
        if ($user) {
            $this->success($user, 'User found', 200);
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }

    public function store()
    {
        $data = Flight::request()->data->getData();
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $user = new User(null, $data['name'], $data['username'], $data['password'], $data['phone'], $data['rol'], $data['is_active']);
        $user = User::create($user);
        $this->success([$user], 'User created', 201);
    }

    public function update($id)
    {
        $data = Flight::request()->data->getData();
        $user = User::get($id);
        if ($user) {
            $user->name = $data['name'];
            $user->username = $data['username'] ?? $user->username;
            $user->password = $data['password'] ? password_hash($data['password'], PASSWORD_BCRYPT) : $user->password;
            $user->phone = $data['phone'] ?? $user->phone;
            $user->rol = $data['rol'] ?? $user->rol;
            $user->is_active = $data['is_active'] ?? $user->is_active;
            $user = User::update($user);
            $this->success([$user], 'User updated', 200);
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }
}
