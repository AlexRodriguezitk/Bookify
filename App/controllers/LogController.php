<?php

//LogController.php
namespace App\controllers;
use App\traits\ApiResponse;
use App\traits\Log;
use App\models\Log as LogModel;
use Flight;

class LogController
{
    use ApiResponse;
    use Log;

    public function index() {
        $logs = LogModel::getAll();
        $this->success($logs, 'Logs list', 200);
    }

    public function show($id) {
        $log = LogModel::get($id);
        if ($log) {
            $this->success([$log], 'Log found', 200);
        } else {
            $this->failed(null, 'Log not found', 404);
        }
    }

    public function store() {
        $data = Flight::request()->data->getData();
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }
        $requiredFields = ['type', 'message'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }
        $log = new LogModel(null, $data['type'], $data['message']);
        $log = LogModel::create($log);
        $this->success([$log], 'Log created', 201);
    }

    //Clear logs with Log::DeleteAll()
    public function clear() {
        LogModel::deleteAll();
        $this->success(null, 'Logs cleared', 200);
    }
}