<?php

namespace App\models;
//Importar base de datos;
use App\database\Database;

use PDO;
use PDOException;
use Exception;

class ticket
{
    public $id;
    public $client;
    public $title;
    public $description;
    public $creation_date;
    public $status; //ENUM[NEW, PENDING, IN_PROGRESS, WATING_FOR_CLIENT, ON_HOLD, CLOSED]
    public $priority; //ENUM[LOW, MEDIUM, HIGH]
    public $category;
    public $asesor;
    public $custom_values;

    //Constructor
    public function __construct($id = null, $client = null, $title = null, $description = null, $creation_date = null, $status = null, $priority = null, $category = null, $asesor = null)
    {
        $this->id = $id;
        $this->client = $client;
        $this->title = $title;
        $this->description = $description;
        $this->creation_date = $creation_date;
        $this->status = $status;
        $this->priority = $priority;
        $this->category = $category;
        $this->asesor = $asesor;
    }

    //Funciones
    //Funcion para obtener todos los tickets
    public static function GetAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM tickets";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            //Remove password from response
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $tickets;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los tickets: " . $e->getMessage());
        }
    }

    //Funcion para obtener un ticket por id
    public static function Get($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM tickets WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result){
                return new ticket($result['id'], $result['id_cliente'], $result['title'], $result['description'], $result['creation_date'], $result['status'], $result['priority'], $result['id_category'], $result['id_asesor']);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el ticket: " . $e->getMessage());
        }
    }

    //Funcion para crear un ticket
    public static function Create($ticket)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "INSERT INTO tickets (client, title, description, creation_date, status, priority, category, asesor) VALUES (:client, :title, :description, :creation_date, :status, :priority, :category, :asesor)";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':client', $ticket->client);
            $stmt->bindParam(':title', $ticket->title);
            $stmt->bindParam(':description', $ticket->description);
            $stmt->bindParam(':creation_date', $ticket->creation_date);
            $stmt->bindParam(':status', $ticket->status);
            $stmt->bindParam(':priority', $ticket->priority);
            $stmt->bindParam(':category', $ticket->category);
            $stmt->bindParam(':asesor', $ticket->asesor);
            $stmt->execute();
            $ticket->id = $connection->lastInsertId();
            return $ticket;
        } catch (PDOException $e) {
            throw new Exception("Error al crear el ticket: " . $e->getMessage());
        }
    }

    //Funcion para actualizar un ticket
    public static function Update($ticket)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "UPDATE tickets SET id_cliente  = :client, title = :title, description = :description, creation_date = :creation_date, status = :status, priority = :priority, id_category  = :category, 	id_asesor = :asesor WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':client', $ticket->client);
            $stmt->bindParam(':title', $ticket->title);
            $stmt->bindParam(':description', $ticket->description);
            $stmt->bindParam(':creation_date', $ticket->creation_date);
            $stmt->bindParam(':status', $ticket->status);
            $stmt->bindParam(':priority', $ticket->priority);
            $stmt->bindParam(':category', $ticket->category);
            $stmt->bindParam(':asesor', $ticket->asesor);
            $stmt->bindParam(':id', $ticket->id);
            $stmt->execute();
            return $ticket;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el ticket: " . $e->getMessage());
        }
    }

    //Funcion para eliminar un ticket
    public static function Delete($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM tickets WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el ticket: " . $e->getMessage());
        }
    }

    //Funcion para obtener tickets por asesor
    public static function GetByAsesor($asesor)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM tickets WHERE asesor = :asesor";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':asesor', $asesor);
            $stmt->execute();
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $tickets;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los tickets del asesor: " . $e->getMessage());
        }
    }

    //Funcion para obtener tickets por cliente
    public static function GetByClient($client)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM tickets WHERE client = :client";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':client', $client);
            $stmt->execute();
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $tickets;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los tickets del cliente: " . $e->getMessage());
        }
    }

    //Funcion para obtener tickets por categoria
    public static function GetByCategory($category)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM tickets WHERE category = :category";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':category', $category);
            $stmt->execute();
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $tickets;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los tickets de la categoria: " . $e->getMessage());
        }
    }

    //Funcion para obtener tickets por estado
    public static function GetByStatus($status)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM tickets WHERE status = :status";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $tickets;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los tickets del estado: " . $e->getMessage());
        }
    }

    //Funcion para obtener tickets por prioridad
    public static function GetByPriority($priority)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM tickets WHERE priority = :priority";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':priority', $priority);
            $stmt->execute();
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $tickets;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los tickets de la prioridad: " . $e->getMessage());
        }
    }


}