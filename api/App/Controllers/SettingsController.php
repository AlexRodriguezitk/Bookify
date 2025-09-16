<?php

namespace App\Controllers;

use App\Traits\ApiResponse;
use App\Traits\HasPermissions;
use App\Models\Setting as SettingModel;
use Flight;

class SettingsController
{
    use ApiResponse;
    use HasPermissions;

    /**
     * Lista todas las configuraciones
     */
    public function index()
    {
        $AuthUser = Flight::get('user');
        if (
            !$AuthUser || !isset($AuthUser->id) ||
            !method_exists($this, 'checkPermission') ||
            !$this->checkPermission($AuthUser->id, 'SETTINGS.READ')
        ) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $settings = SettingModel::getAll();
        $this->success($settings, 'Settings list', 200);
    }

    /**
     * Muestra una configuración por su clave
     */
    public function show($key)
    {
        $AuthUser = Flight::get('user');
        if (
            !$AuthUser || !isset($AuthUser->id) ||
            !method_exists($this, 'checkPermission') ||
            !$this->checkPermission($AuthUser->id, 'SETTINGS.READ')
        ) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $setting = SettingModel::getByKey($key);
        if ($setting) {
            $this->success([$setting], 'Setting found', 200);
        } else {
            $this->failed(null, 'Setting not found', 404);
        }
    }

    /**
     * Actualiza el valor de una configuración existente
     */
    public function update($key)
    {
        $AuthUser = Flight::get('user');
        if (
            !$AuthUser || !isset($AuthUser->id) ||
            !method_exists($this, 'checkPermission') ||
            !$this->checkPermission($AuthUser->id, 'SETTINGS.UPDATE')
        ) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $data = Flight::request()->data->getData();
        if (!isset($data['value'])) {
            $this->failed(null, "Field 'value' is required", 400);
            return;
        }

        $setting = SettingModel::getByKey($key);
        if (!$setting) {
            $this->failed(null, 'Setting not found', 404);
            return;
        }

        $updated = SettingModel::updateValue($key, $data['value']);
        if ($updated) {
            $this->success([SettingModel::getByKey($key)], 'Setting updated', 200);
        } else {
            $this->failed(null, 'Failed to update setting', 500);
        }
    }
}
