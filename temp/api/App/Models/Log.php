<?php

namespace App\Models;
//Importar base de datos;
use App\Database\Database;

use PDO;
use PDOException;
use Exception;

class Log
{
    public $id;
    public $action;
    public $details;
    public $id_user;
    public $log_date;

    //Constructor
    public function __construct($id = null, $action = null, $details = null, $id_user = null, $log_date = null)
    {
        $this->id = $id;
        $this->action = $action;
        $this->details = $details;
        $this->id_user = $id_user;
        $this->log_date = $log_date;
    }

    //Funciones
    //Funcion para obtener todos los logs
    public static function GetAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM logs";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            //Remove password from response
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $logs;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los logs: " . $e->getMessage());
        }
    }

    //Funcion para obtener un log por id
    public static function Get($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM logs WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new log($result['id'], $result['action'], $result['details'], $result['id_user'], $result['log_date']);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el log: " . $e->getMessage());
        }
    }

    //Funcion para crear un log
    public static function Create($log)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "INSERT INTO logs (action, details, id_user) VALUES (:action, :details, :id_user)";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':action', $log->action);
            $stmt->bindParam(':details', $log->details);
            $stmt->bindParam(':id_user', $log->id_user);
            $stmt->execute();
            $log->id = $connection->lastInsertId();
            return $log;
        } catch (PDOException $e) {
            throw new Exception("Error al crear el log: " . $e->getMessage());
        }
    }

    //Funcion para eliminar todos los logs
    public static function DeleteAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM logs";
            $stmt = $connection->prepare($query);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar todos los logs: " . $e->getMessage());
        }
    }
}
