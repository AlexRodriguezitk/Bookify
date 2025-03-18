<?php

namespace App\controllers;

use App\traits\ApiResponse;
use App\traits\Log;
use App\traits\HasPermissions;
use App\models\User;
use App\models\Rol;
use Flight;


class UserController
{
    use ApiResponse;
    use Log;
    use HasPermissions;

    public function index()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.INDEX')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $users = User::getAll();
        $this->success($users, 'Users list', 200);
    }

    public function show($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.SHOW')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
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
        //ChekPermissions
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.STORE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $data = Flight::request()->data->getData();

        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }

        $requiredFields = ['name', 'username', 'password', 'rol', 'is_active'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $user = new User(null, $data['name'], $data['username'], $data['password'], $data['phone'] ?? "0", $data['rol'], $data['is_active']);
        $user = User::create($user);
        $this->saveLog($AuthUser->id, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: ' . $user->name);
        $this->success([$user], 'User created', 201);
    }

    public function update($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.UPDATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        $user = User::get($id);
        if ($user) {
            $user->name = $data['name'] ?? $user->name;
            $user->username = $data['username'] ?? $user->username;
            $user->password = isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : $user->password;
            $user->phone = $data['phone'] ?? $user->phone;
            $user->rol = $data['rol'] ?? $user->rol;
            $user = User::update($user);
            $this->saveLog($AuthUser->id, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: ' . $user->name);
            $this->success([$user], 'User updated', 200);
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }

    public function destroy($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.DESTROY')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $user = User::get($id);
        if ($user) {
            User::delete($id);
            $this->saveLog($AuthUser->id, 'USER_DELETED', 'USER WAS DELETED SUCCESSFULLY: ' . $user->name);
            $this->success([null], 'User deleted', 200);
        } else {
            $this->failed([null], 'User not found', 404);
        }
    }

    public function Active($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.ACTIVE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $user = User::get($id);
        if ($user) {
            $user->is_active = 1;
            $user = User::update($user);
            $this->saveLog($AuthUser->id, 'USER_ACTIVE', 'USER WAS ACTIVATED SUCCESSFULLY: ' . $user->name);
            $this->success([$user], 'User activated', 200);
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }

    public function Inactive($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.INACTIVE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $user = User::get($id);
        if ($user) {
            if ($user->id == $AuthUser->id) {
                $this->failed(null, 'You can inactive your own user', 200);
            } else {
                $user->is_active = 0;
                $user = User::update($user);
                $this->saveLog($AuthUser->id, 'USER_INACTIVE', 'USER WAS INACTIVATED SUCCESSFULLY: ' . $user->name);
                $this->success([$user], 'User inactivated', 200);
            }
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }

    //Auth
    public function login()
    {
        $data = Flight::request()->data->getData();
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }

        $requiredFields = ['username', 'password'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }
        $user = User::getByUsername($data['username']);
        if ($user && password_verify($data['password'], $user->password) && $user->is_active) {
            $token = \App\Auth::generateToken($user->id, $user->rol);
            //Set Flight::set('user', $user);
            Flight::set('user', $user);
            $this->saveLog($user->id, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: ' . $user->name);
            $this->setJwtBearerToken($token);
            $this->success(['token' => $token], 'Login successful', 200);
        } else {
            $this->failed(null, 'Invalid credentials', 401);
        }
    }

    public function renew()
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            $this->failed(null, 'Authorization header missing', 401);
            return;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $decoded = \App\Auth::validateToken($token);
        if (!$decoded) {
            $this->failed(null, 'Invalid or expired token', 401);
            return;
        }

        $user = User::Get($decoded->id);
        if (!$user) {
            $this->failed(null, 'User not found', 404);
            return;
        }

        $newToken = \App\Auth::generateToken($user->id, $user->rol);
        $this->setJwtBearerToken($newToken);
        $this->success(['token' => $newToken], 'Token renewed successfully', 200);
    }


    public function register()
    {

        $data = Flight::request()->data->getData();
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }

        $requiredFields = ['name', 'username', 'password', 'phone'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }
        $user = User::getByUsername($data['username']);
        if ($user) {
            $this->failed(null, 'Username already exists', 400);
            return;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $user = new User(null, $data['name'], $data['username'], $data['password'], $data['phone'], 2, 1);
        $user = User::create($user);
        //Loging and return JWT
        $token = \App\Auth::generateToken($user->id, $user->rol);
        $this->setJwtBearerToken($token);
        $this->saveLog($user->id, 'USER_REGISTERED', 'USER WAS REGISTERED SUCCESSFULLY: ' . $user->name);
        $this->success([$user], 'User registered', 201);
    }

    public function Profile(){
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $user = User::get($AuthUser->id);
        $user->rol = Rol::Get($user->rol);
        if ($user) {
            unset($user->password);
            $this->success([$user], 'User found', 200);
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }
}
