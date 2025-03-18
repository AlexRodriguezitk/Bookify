<?php

namespace App\models;
//Importar base de datos;
use App\database\Database;

use PDO;
use PDOException;
use Exception;

class WorkLog
{

    public $id;
    public $id_ticket;
    public $id_user;
    public $time_spent;
    public $work_description;
    public $log_date;

    public function __construct($id = null, $id_ticket = null, $id_user = null, $time_spent = null, $work_description = null, $log_date = null){
        $this->id = $id;
        $this->id_ticket = $id_ticket;
        $this->id_user = $id_user;
        $this->time_spent = $time_spent;
        $this->work_description = $work_description;
        $this->log_date = $log_date;
    }

    //Funcion para obtener todos los usuarios
    public static function GetAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM worklog";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            //Remove password from response
            $worklogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $worklogs;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los registros de trabajo: " . $e->getMessage());
        }
    }

    //Funcion para obtener un ticket por id
    public static function Get($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM worklog WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result){
                return new self($result['id'],$result['id_ticket'], $result['id_user'], $result['time_spent'], $result['work_description'], $result['log_date']);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el registro: " . $e->getMessage());
        }
    }

    //Funcion para crear un ticket
    public static function Create($worklog)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "INSERT INTO worklog (id_ticket, id_user, time_spent, work_description) VALUES (:id_ticket, :id_user, :time_spent, :work_description)";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id_ticket', $worklog->id_ticket);
            $stmt->bindParam(':id_user', $worklog->id_user);
            $stmt->bindParam(':time_spent', $worklog->time_spent);
            $stmt->bindParam(':work_description', $worklog->work_description);
            $stmt->execute();
            $worklog->id = $connection->lastInsertId();
            return $worklog;
        } catch (PDOException $e) {
            throw new Exception("Error al crear el registro: " . $e->getMessage());
        }
    }

    //Funcion para actualizar un ticket
    public static function Update($worklog)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "UPDATE worklog SET id_ticket  = :id_ticket, id_user = :id_user, time_spent = :time_spent, work_description = :work_description, log_date = :log_date WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id_ticket', $worklog->id_ticket);
            $stmt->bindParam(':id_user', $worklog->id_user);
            $stmt->bindParam(':time_spent', $worklog->time_spent);
            $stmt->bindParam(':work_description', $worklog->work_description);
            $stmt->bindParam(':log_date', $worklog->log_date);
            $stmt->bindParam(':id', $worklog->id);
            $stmt->execute();
            return $worklog;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el registro: " . $e->getMessage());
        }
    } 

    //Funcion para eliminar un registro
    public static function Delete($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM worklog WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el registro: " . $e->getMessage());
        }
    }

    //Funcion para obtener tickets por asesor
    public static function GetByUser($user)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM worklog WHERE id_user = :user";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':user', $user->id);
            $stmt->execute();
            $worklogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $worklogs;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los registros del usuario: " . $e->getMessage());
        }
    }

    //Funcion para obtener tickets por asesor
    public static function GetByTicket($ticket)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM worklog WHERE id_ticket = :ticket";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':ticket', $ticket->id);
            $stmt->execute();
            $worklogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $worklogs;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los registros del ticket: " . $e->getMessage());
        }
    }
}