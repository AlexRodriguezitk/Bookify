<?php

namespace App\controllers;


use App\traits\ApiResponse;
use App\traits\HasPermissions;
use App\models\ticket;
use App\models\ST_History as ST_HistoryModel;
use Flight;

class STHistoryController
{
    use ApiResponse;
    use HasPermissions;

    public function index()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'STHISTORY.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $ST_History = ST_HistoryModel::getAll();
        $this->success($ST_History, 'ST_History list', 200);
    }

    public function show($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'STHISTORY.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $ST_History = ST_HistoryModel::get($id);
        if ($ST_History) {
            $this->success([$ST_History], 'ST_History found', 200);
        } else {
            $this->failed(null, 'Log ST_History found', 404);
        }
    }

    public function store()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'STHISTORY.CREATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }
        $requiredFields = ['id_ticket', 'last_status', 'new_status', 'id_asesor'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }
        $ST_History = new ST_HistoryModel(null, $data['type'], $data['message']);
        $ST_History = ST_HistoryModel::create($ST_History);
        $this->success([$ST_History], 'ST_History created', 201);
    }


    public function clear()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'STHISTORY.CLEAR')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        // $this->saveLog($AuthUser->id, 'ST_HISTORY_CLEARED', 'ST_HISTORY WAS CLEARED SUCCESSFULLY: ' . $ST_History->id);
        ST_HistoryModel::deleteAll();

        $this->success([null], 'ST_History cleared', 200);
    }

    public function GetByTicket($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'STHISTORY.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $ticket = Ticket::Get($id);
        $ST_History = ST_HistoryModel::GetByTicket($ticket);
        if ($ST_History) {
            $this->success([$ST_History], 'ST_History found', 200);
        } else {
            $this->failed(null, 'Log ST_History found', 404);
        }
    }
}
