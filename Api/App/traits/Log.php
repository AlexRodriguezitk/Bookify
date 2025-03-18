<?php
//Trait to save Logs on Database

namespace App\traits;
use App\models\Log as LogModel;

trait Log
{
    //__construct($id = null, $action = null, $details = null, $id_user = null, $log_date = null)
    public function saveLog($id_user = 1, $action, $details)
    {
        $log = new LogModel($id_user, $action, $details, $id_user, date('Y-m-d H:i:s'));
        LogModel::create($log);
    }
}