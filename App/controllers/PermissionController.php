<?php

namespace App\controllers;

use App\traits\ApiResponse;
use App\traits\Log;
use App\traits\HasPermissions;
use App\models\Permission;
use App\models\Rol;
use Flight;

use Exception;
class PermissionController
{
    use ApiResponse;
    use Log;
    use HasPermissions;

    public function index()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISIONS.INDEX')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $permission = Permission::getAll();
        $this->success($permission, 'Permission list', 200);
    }

    public function show($id)
    {   
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISIONS.SHOW')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $permission = Permission::Get($id);
        if ($permission) {
            $this->success([$permission], 'Permission found', 200);
        } else {
            $this->failed(null, 'Permission not found', 404);
        }
    }

    public function store()
    {
        //ChekPermissions
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISSIONS.STORE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $data = Flight::request()->data->getData();
        
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }

        $requiredFields = ['name', 'description'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }

        $permission = new Permission(null, $data['name'], $data['description']);
        $permission = Permission::create($permission);
        $this->saveLog(null, 'PERMISSION_CREATED', 'PERMISSION WAS CREATED SUCCESSFULLY: ' . $permission->name);
        $this->success([$permission], 'PERMISSION created', 201);
    }    

    public function update($id)
    {   
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISSIONS.UPDATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        $permission = Permission::get($id);
        if ($permission) {
            $permission->name = $data['name'] ?? $permission->name;
            $permission->description = $data['description'] ?? $permission->description;
            $permission = Permission::update($permission);
            $this->saveLog(null, 'PERMISSION_UPDATED', 'PERMISSION WAS UPDATED SUCCESSFULLY: ' . $permission->name);
            $this->success([$permission], 'Permission updated', 200);
        } else {
            $this->failed(null, 'Permission not found', 404);
        }
    }

    public function destroy($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISSIONS.DESTROY')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $permission = Permission::get($id);
        if ($permission) {
            Permission::delete($id);
            $this->saveLog(null, 'PERMISSION_DELETED', 'PERMISSION WAS DELETED SUCCESSFULLY: ' . $permission->name);
            $this->success([null], 'Permission deleted', 200);
        } else {
            $this->failed([null], 'Permission not found', 404);
        }
    }
    public function getAssignments($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISSIONS.GETASSIGNEMENTS')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $rol = Rol::get($id);
        if ($rol) {
            $assignements = Permission::GetAssignments($rol);
            if($assignements){
                $this->success($assignements, 'Permission assignements', 200);
            }
            else{
                $this->failed(null, 'This rol doesnt have permissions assigned', 404);
            }
        } else {
            $this->failed(null, 'Rol not found', 404);
        }
    }

    //Func Asing Terminal $terminal::Assing($terminal, $asesor) 
    public function assing($id)
    {   
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISSIONS.ASSING')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }
        try {
            $requiredFields = ['rol'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $this->failed(null, "Field '$field' is required", 400);
                    return;
                }
            }
        } catch (Exception $e) {
            $this->failed(null, "Field '$field' is required", 400);
            return;
        }
        $permission = Permission::get($id);
        $rol = Rol::get($data['rol']);
        if ($permission && $rol) {
            try {
                $message = Permission::Assing($permission, $rol);
                $this->saveLog(null, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{' . $permission->name .'} ROL{' . $rol->name . '}');
                $this->success([$message, $permission], 'Permission assigned', 200);
            } catch (Exception $e) {
                $this->failed(null, 'Permission already assigned', 400);
            }
        } else {
            $this->failed(null, 'Permission or rol not found', 404);
        }
    }

    //FUncion para desasignar terminal
    public function unassing($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISSIONS.UNASSING')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }
        try {
            $requiredFields = ['rol'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $this->failed(null, "Field '$field' is required", 400);
                    return;
                }
            }
        } catch (Exception $e) {
            $this->failed(null, "Field '$field' is required", 400);
            return;
        }
        $permission = Permission::get($id);
        $rol = Rol::get($data['rol']);
        if ($permission && $rol) {
            $message = Permission::Unassign($permission, $rol);
            $this->saveLog(null, 'PERMISSION_UNASSIGNED', 'PERMISSION WAS UNASSIGNED SUCCESSFULLY: PERMISSION{' . $permission->name .'} ROL{' . $rol->name . '}');
            $this->success([$message, $permission], 'Permission unassing', 200);
        } else {
            $this->failed(null, 'Permission or Rol not found', 404);
        }
    }

}