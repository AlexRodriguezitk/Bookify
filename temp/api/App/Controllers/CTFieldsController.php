<?php

namespace App\Controllers;

use App\Traits\Log;
use App\Traits\ApiResponse;
use App\Traits\HasPermissions;
use App\Models\CT_Fields;
use Flight;

class CTFieldsController
{
    use ApiResponse;
    use HasPermissions;
    use Log;

    public function index()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'CTFIELDS.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $fields = CT_Fields::getAll();
        $this->success($fields, 'Fields list', 200);
    }

    public function show($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'CTFIELDS.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $field = CT_Fields::get($id);
        if ($field) {
            $this->success([$field], 'Field found', 200);
        } else {
            $this->failed(null, 'Field not found', 404);
        }
    }

    //store function CT_Fields[category, name, type]
    public function store()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'CTFIELDS.CREATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();

        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }

        $requiredFields = ['category', 'name', 'type'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }

        $field = new CT_Fields(null, $data['category'], $data['name'], $data['type']);
        $field = CT_Fields::create($field);
        $this->saveLog($AuthUser->id, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: ' . $field->name);
        $this->success([$field], 'Field created', 201);
    }

    //update function
    public function update($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'CTFIELDS.UPDATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        $field = CT_Fields::get($id);
        if ($field) {
            $field->category = $data['category'] ?? $field->category;
            $field->name = $data['name'] ?? $field->name;
            $field->type = $data['type'] ?? $field->type;
            $field = CT_Fields::update($field);
            $this->saveLog($AuthUser->id, 'FIELD_UPDATED', 'FIELD WAS UPDATED SUCCESSFULLY: ' . $field->name);
            $this->success([$field], 'Field updated', 200);
        } else {
            $this->failed(null, 'Field not found', 404);
        }
    }

    //delete function
    public function destroy($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'CTFIELDS.DELETE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $field = CT_Fields::get($id);
        if ($field) {
            CT_Fields::delete($id);
            $this->saveLog($AuthUser->id, 'FIELD_DELETED', 'FIELD WAS DELETED SUCCESSFULLY: ' . $field->name);
            $this->success([null], 'Field deleted', 200);
        } else {
            $this->failed(null, 'Field not found', 404);
        }
    }

    public function getFieldsByCategory($category)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'CTFIELDS.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $fields = CT_Fields::GetByCategory($category);
        $this->success($fields, 'Fields list', 200);
    }
}
