<?php

namespace App\Controllers;

use App\Traits\ApiResponse;
use App\Traits\Log;
use App\Traits\HasPermissions;
use App\Models\Permission;
use App\Models\Rol;
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
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISSION.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $permission = Permission::getAll();
        $this->success($permission, 'Permission list', 200);
    }

    public function show($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISION.READ')) {
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
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISSION.CREATE')) {
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
        $this->saveLog($AuthUser->id, 'PERMISSION_CREATED', 'PERMISSION WAS CREATED SUCCESSFULLY: ' . $permission->name);
        $this->success([$permission], 'PERMISSION created', 201);
    }

    public function update($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISSION.UPDATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        $permission = Permission::get($id);
        if ($permission) {
            $permission->name = $data['name'] ?? $permission->name;
            $permission->description = $data['description'] ?? $permission->description;
            $permission = Permission::update($permission);
            $this->saveLog($AuthUser->id, 'PERMISSION_UPDATED', 'PERMISSION WAS UPDATED SUCCESSFULLY: ' . $permission->name);
            $this->success([$permission], 'Permission updated', 200);
        } else {
            $this->failed(null, 'Permission not found', 404);
        }
    }

    public function destroy($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISSION.DELETE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $permission = Permission::get($id);
        if ($permission) {
            Permission::delete($id);
            $this->saveLog($AuthUser->id, 'PERMISSION_DELETED', 'PERMISSION WAS DELETED SUCCESSFULLY: ' . $permission->name);
            $this->success([null], 'Permission deleted', 200);
        } else {
            $this->failed([null], 'Permission not found', 404);
        }
    }
    public function getAssignments($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISSION.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $rol = Rol::get($id);
        if ($rol) {
            $assignements = Permission::GetAssignments($rol);
            if ($assignements) {
                $this->success($assignements, 'Permission assignements', 200);
            } else {
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
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISSION.ASSING')) {
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
                $message = Permission::Assign($permission, $rol);

                $this->saveLog(
                    $AuthUser->id,
                    'PERMISSION_ASSIGNED',
                    'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{' . $permission->name . '} ROL{' . $rol->name . '}'
                );

                $this->success([$message, $permission], 'Permission assigned', 200);
            } catch (Exception $e) {
                $this->failed(null, 'Error assigning permission: ' . $e->getMessage(), 500);
            }
        } else {
            $this->failed(null, 'Permission or role not found', 404);
        }
    }

    //FUncion para desasignar terminal
    public function unassing($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PERMISSION.ASSING')) {
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
            $this->saveLog($AuthUser->id, 'PERMISSION_UNASSIGNED', 'PERMISSION WAS UNASSIGNED SUCCESSFULLY: PERMISSION{' . $permission->name . '} ROL{' . $rol->name . '}');
            $this->success([$message, $permission], 'Permission unassing', 200);
        } else {
            $this->failed(null, 'Permission or Rol not found', 404);
        }
    }


    public function check()
    {
        $data = Flight::request()->data->getData();

        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }

        $requiredFields = ['permissions'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }

        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id)) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $permissions = $data['permissions'];
        $results = [];

        foreach ($permissions as $permission) {
            $access = $this->checkPermission($AuthUser->id, $permission);
            $results[] = [
                'permission' => $permission,
                'access' => $access
            ];
        }

        $this->success($results, 'Permission check results', 200);
    }
}
