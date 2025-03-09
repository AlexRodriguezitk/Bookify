<?php

namespace App\controllers;

use App\traits\ApiResponse;
use App\traits\HasPermissions;
use App\traits\Log;
use App\models\Rol;
use Flight;

class RolController
{
    use ApiResponse;
    use HasPermissions;
    use Log;

    public function index()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'ROL.INDEX')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $roles = Rol::getAll();
        $this->success($roles, 'Roles list', 200);
    }

    public function show($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'ROL.SHOW')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $rol = Rol::get($id);
        if ($rol) {
            $this->success([$rol], 'Rol found', 200);
        } else {
            $this->failed(null, 'Rol not found', 404);
        }
    }

    public function store()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'ROL.STORE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();

        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }

        $requiredFields = ['name'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }

        $rol = new Rol(null, $data['name']);
        $rol = Rol::create($rol);
        $this->saveLog($AuthUser->id, 'ROL_CREATED', 'ROL WAS CREATED SUCCESSFULLY: ' . $rol->name);
        $this->success([$rol], 'Rol created', 201);
    }

    //update function
    public function update($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'ROL.UPDATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        $rol = Rol::get($id);
        if ($rol) {
            $rol->name = $data['name'] ?? $rol->name;
            $rol = Rol::update($rol);
            $this->saveLog($AuthUser->id, 'ROL_UPDATED', 'ROL WAS UPDATED SUCCESSFULLY: ' . $rol->name);
            $this->success([$rol], 'Rol updated', 200);
        } else {
            $this->failed(null, 'Rol not found', 404);
        }
    }

    //delete function
    public function destroy($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'ROL.DESTROY')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $rol = Rol::get($id);
        if ($rol) {
            Rol::delete($id);
            $this->saveLog($AuthUser->id, 'ROL_DELETED', 'ROL WAS DELETED SUCCESSFULLY: ' . $rol->name);
            $this->success([null], 'Rol deleted', 200);
        } else {
            $this->failed([null], 'Rol not found', 404);
        }
    }
}
