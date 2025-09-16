<?php

namespace App\Models;
//Importar base de datos;
use App\Database\Database;

use PDO;
use PDOException;
use Exception;

class Interaction
{
    public $id;
    public $id_ticket;
    public $user;

    public $message;
    public $interaction_date;
    public $type; // ENUM('internal', 'external')

    //Constructor
    public function __construct($id = null, $id_ticket = null, $user = null, $message = null, $interaction_date = null, $type = null)
    {
        $this->id = $id;
        $this->id_ticket = $id_ticket;
        $this->user = $user;
        $this->message = $message;
        $this->interaction_date = $interaction_date;
        $this->type = $type;
    }

    //Funciones
    //Funcion para obtener todas las interacciones
    public static function GetAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM interactions";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            $interactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $interactions;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todas las interacciones: " . $e->getMessage());
        }
    }

    //Funcion para obtener una interaccion por id
    public static function Get($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM interactions WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new Interaction($result['id'], $result['id_ticket'], $result['id_user'], $result['message'], $result['interaction_date'], $result['type']);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la interaccion: " . $e->getMessage());
        }
    }

    //Funcion para crear una interaccion
    public static function Create($interactions)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "INSERT INTO interactions (id_ticket, id_user, message, type) VALUES (:id_ticket, :id_user, :message, :type)";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id_ticket', $interactions->id_ticket);
            $stmt->bindParam(':id_user', $interactions->user);
            $stmt->bindParam(':message', $interactions->message);
            $stmt->bindParam(':type', $interactions->type);
            $stmt->execute();
            $interactions->id = $connection->lastInsertId();
            return $interactions;
        } catch (PDOException $e) {
            throw new Exception("Error al crear la interaccion: " . $e->getMessage());
        }
    }

    //Funcion para actualizar una interaccion
    public static function Update($interactions)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "UPDATE interactions SET id_ticket = :id_ticket, id_user = :id_user, message = :message, interaction_date = :interaction_date, type = :type WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $interactions->id);
            $stmt->bindParam(':id_ticket', $interactions->id_ticket);
            $stmt->bindParam(':id_user', $interactions->id_user);
            $stmt->bindParam(':message', $interactions->message);
            $stmt->bindParam(':interaction_date', $interactions->interaction_date);
            $stmt->bindParam(':type', $interactions->type);
            $stmt->execute();
            return $interactions;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la interaccion: " . $e->getMessage());
        }
    }

    //Funcion para eliminar una interaccion
    public static function Delete($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM interactions WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar la interaccion: " . $e->getMessage());
        }
    }

    //Funcion para obtener todas las interacciones de un ticket
    public static function GetIntByTicket($id_ticket)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM interactions WHERE id_ticket = :id_ticket ORDER BY interaction_date ASC";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id_ticket', $id_ticket);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $interactions = array();
            foreach ($result as $row) {
                $interactions[] = new Interaction($row['id'], $row['id_ticket'], $row['id_user'], $row['message'], $row['interaction_date'], $row['type']);
            }
            return $interactions;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las interacciones del ticket: " . $e->getMessage());
        }
    }
}
