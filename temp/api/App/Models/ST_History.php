<?php

namespace App\Models;
//Importar base de datos;
use App\database\Database;

use PDO;
use PDOException;
use Exception;

class ST_History
{
    public $id;
    public $id_ticket;
    public $last_status;
    public $new_status;
    public $update_date;
    public $id_asesor;

    //Constructor
    public function __construct($id = null, $id_ticket = null, $last_status = null, $new_status = null, $update_date = null, $id_asesor = null)
    {
        $this->id = $id;
        $this->id_ticket = $id_ticket;
        $this->last_status = $last_status;
        $this->new_status = $new_status;
        $this->update_date = $update_date;
        $this->id_asesor = $id_asesor;
    }

    //Funciones
    //Funcion para obtener todo el historial
    public static function GetAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM status_history";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            //Remove password from response
            $ST_History = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $ST_History;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el historial de cambios: " . $e->getMessage());
        }
    }

    //Funcion para obtener un historial por id
    public static function Get($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM status_history WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new ST_History($result['id'], $result['id_ticket'], $result['last_status'], $result['new_status'], $result['update_date'], $result['id_asesor']);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el Historial: " . $e->getMessage());
        }
    }

    //Funcion para crear un historial
    public static function Create($ST_History)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "INSERT INTO status_history (id_ticket, last_status, new_status, id_asesor) VALUES (:id_ticket, :last_status, :new_status, :id_asesor )";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id_ticket', $ST_History->id_ticket);
            $stmt->bindParam(':last_status', $ST_History->last_status);
            $stmt->bindParam(':new_status', $ST_History->new_status);
            $stmt->bindParam(':id_asesor', $ST_History->id_asesor);
            $stmt->execute();
            $ST_History->id = $connection->lastInsertId();
            return $ST_History;
        } catch (PDOException $e) {
            throw new Exception("Error al crear el Historial: " . $e->getMessage());
        }
    }

    //Funcion para eliminar todos el historial
    public static function DeleteAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM status_history";
            $stmt = $connection->prepare($query);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar todo el Historial de Cambios: " . $e->getMessage());
        }
    }

    //Funcion para eliminar un historial
    public static function Delete($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM status_history WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el Historial: " . $e->getMessage());
        }
    }

    public static function GetByTicket($ticket)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM status_history WHERE id_ticket = :id_ticket";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id_ticket', $ticket->id);
            $stmt->execute();
            $ST_History = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $ST_History;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el Historial: " . $e->getMessage());
        }
    }
}
