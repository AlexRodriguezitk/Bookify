<?php

namespace App\controllers;

use App\traits\ApiResponse;
use App\models\Rol;
use Flight;

class RolController {
    use ApiResponse;

    public function index() {
        $roles = Rol::getAll();
        $this->success($roles, 'Roles list', 200);
    }

    public function show($id) {
        $rol = Rol::get($id);
        if ($rol) {
            $this->success([$rol], 'Rol found', 200);
        } else {
            $this->failed(null, 'Rol not found', 404);
        }
    }

    public function store() {
        $data = Flight::request()->data->getData();
        
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }

        $requiredFields = ['name', 'permissions'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }

        $rol = new Rol(null, $data['name'], $data['permissions']);
        $rol = Rol::create($rol);
        $this->success([$rol], 'Rol created', 201);
    }

    //update function
    public function update($id) {
        $data = Flight::request()->data->getData();
        $rol = Rol::get($id);
        if ($rol) {
            $rol->name = $data['name'] ?? $rol->name;
            $rol->permissions = $data['permissions'] ?? $rol->permissions;
            $rol = Rol::update($rol);
            $this->success([$rol], 'Rol updated', 200);
        } else {
            $this->failed(null, 'Rol not found', 404);
        }
    }

    //delete function
    public function destroy($id) {
        $rol = Rol::get($id);
        if ($rol) {
            Rol::delete($id);
            $this->success([null], 'Rol deleted', 200);
        } else {
            $this->failed([null], 'Rol not found', 404);
        }
    }
}