<?php

namespace App\controllers;

use App\traits\ApiResponse;
use App\traits\Log;
use App\models\User;
use Flight;


class UserController
{
    use ApiResponse;
    use Log;

    public function index()
    {
        $users = User::getAll();
        $this->success($users, 'Users list', 200);
    }

    public function show($id)
    {
        $user = User::get($id);
        if ($user) {
            unset($user->password);
            $this->success([$user], 'User found', 200);
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }

    public function store()
    {
        $data = Flight::request()->data->getData();
        
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }

        $requiredFields = ['name', 'username', 'password', 'phone', 'rol', 'is_active'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $user = new User(null, $data['name'], $data['username'], $data['password'], $data['phone'], $data['rol'], $data['is_active']);
        $user = User::create($user);
        $this->saveLog(null, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: ' . $user->name);
        $this->success([$user], 'User created', 201);
    }

    public function update($id)
    {
        $data = Flight::request()->data->getData();
        $user = User::get($id);
        if ($user) {
            $user->name = $data['name'] ?? $user->name;
            $user->username = $data['username'] ?? $user->username;
            $user->password = isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : $user->password;
            $user->phone = $data['phone'] ?? $user->phone;
            $user->rol = $data['rol'] ?? $user->rol;
            $user->is_active = $data['is_active'] ?? $user->is_active;
            $user = User::update($user);
            $this->saveLog(null, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: ' . $user->name);
            $this->success([$user], 'User updated', 200);
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }

    public function destroy($id)
    {
        $user = User::get($id);
        if ($user) {
            User::delete($id);
            $this->saveLog(null, 'USER_DELETED', 'USER WAS DELETED SUCCESSFULLY: ' . $user->name);
            $this->success([null], 'User deleted', 200);
        } else {
            $this->failed([null], 'User not found', 404);
        }
    }

    public function Active($id)
    {
        $user = User::get($id);
        if ($user) {
            $user->is_active = 1;
            $user = User::update($user);
            $this->saveLog(null, 'USER_ACTIVE', 'USER WAS ACTIVATED SUCCESSFULLY: ' . $user->name);
            $this->success([$user], 'User activated', 200);
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }

    public function Inactive($id)
    {
        $user = User::get($id);
        if ($user) {
            $user->is_active = 0;
            $user = User::update($user);
            $this->saveLog(null, 'USER_INACTIVE', 'USER WAS INACTIVATED SUCCESSFULLY: ' . $user->name);
            $this->success([$user], 'User inactivated', 200);
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }
}
