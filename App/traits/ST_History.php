<?php
//Trait to save Logs on Database

namespace App\traits;
use App\models\ST_History as ST_HistoryModel;

trait ST_History

{
    //$id = null, $id_ticket = null, $last_status = null, $new_status = null, $update_date = null, $id_asesor = null)
    public function saveHistory($id_ticket, $last_status, $new_status, $id_asesor)
    {
        $ST_History = new ST_HistoryModel($id_ticket, $last_status, $new_status,date('Y-m-d H:i:s'), $id_asesor);
        ST_HistoryModel::create($ST_History);
    }
}