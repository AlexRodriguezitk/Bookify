<?php

namespace App\controllers;

use App\traits\ApiResponse;
use App\traits\Log;
use App\traits\ST_History;
use App\traits\HasPermissions;
use App\models\Ticket;
use App\models\User;
use App\models\worklog;

use Flight;

class WorkLogController
{
    use ApiResponse;
    use HasPermissions;
    use Log;

    public function index()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'WORKLOG.INDEX')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $worklog = worklog::getAll();
        $this->success($worklog, 'Worklog list', 200);
    }

    public function show($id) {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'WORKLOG.SHOW')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $worklog = worklog::get($id);
        if ($worklog) {
            $this->success([$worklog], 'worklog found', 200);
        } else {
            $this->failed(null, 'worklog found', 404);
        }
    }

    public function store() {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'WORKLOG.STORE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }
        $requiredFields = ['ticket', 'user', 'time_spent', 'work_description'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }
        $worklog = new worklog(null, $data['ticket'], $data['user'], $data['time_spent'], $data['work_description'], null);
        $worklog = worklog::create($worklog);
        $this->saveLog($AuthUser->id, 'WORKLOG_CREATED', 'WORKLOG WAS CREATED SUCCESSFULLY: ' . $worklog->time_spent);
        $this->success([$worklog], 'worklog created', 201);
    }

    public function update($id)
    {   
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'WORKLOG.UPDATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        $worklog = worklog::get($id);
        if ($worklog) {
            $worklog->id_ticket = $data['ticket'] ?? $worklog->id_ticket;
            $worklog->id_user = $data['user'] ?? $worklog->id_user;
            $worklog->time_spent = $data['time_spent'] ?? $worklog->time_spent;
            $worklog->work_description = $data['work_description'] ?? $worklog->work_description;
            $worklog->log_date = $data['log_date'] ?? $worklog->log_date;
            $worklog = User::update($worklog);
            $this->saveLog($AuthUser->id, 'WORKLOG_UPDATED', 'WORKLOG WAS UPDATED SUCCESSFULLY: ' . $worklog->time_spent);
            $this->success([$worklog], 'WorkLog updated', 200);
        } else {
            $this->failed(null, 'WorkLog not found', 404);
        }
    }

    public function destroy($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'WORKLOG.DESTROY')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $worklog = worklog::get($id);
        if ($worklog) {
            worklog::delete($id);
            $this->saveLog($AuthUser->id, 'WORKLOG_DELETED', 'WORKLOG WAS DELETED SUCCESSFULLY: ' . $worklog->time_spent);
            $this->success([null], 'WorkLog deleted', 200);
        } else {
            $this->failed([null], 'WorkLog not found', 404);
        }
    }

    public function GetByTicket($id) {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'WORKLOG.GETBYTICKET')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $Ticket = Ticket::Get($id);
        $worklog = worklog::GetByTicket($Ticket);
        if ($worklog) {
            $this->success([$worklog], 'WorkLog found', 200);
        } else {
            $this->failed(null, ' WorkLog found', 404);
        }
    }

    public function GetByUser($id) {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'WORKLOG.GETBYUSER')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $user = User::Get($id);
        $worklog = worklog::GetByTicket($user);
        if ($worklog) {
            $this->success([$worklog], 'WorkLog found', 200);
        } else {
            $this->failed(null, ' WorkLog found', 404);
        }
    }
}