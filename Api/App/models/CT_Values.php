<?php

namespace App\models;
//Importar base de datos;
use App\database\Database;

use PDO;
use PDOException;
use Exception;


class CT_Values
{
    public $id;
    public $custom_field_id;
    public $ticket_id;
    public $value;

    //Constructor
    public function __construct($id = null, $custom_field_id = null, $ticket_id = null, $value = null)
    {
        $this->id = $id;
        $this->custom_field_id = $custom_field_id;
        $this->ticket_id = $ticket_id;
        $this->value = $value;
    }

    //Funciones
    //Funcion para obtener todos los valores personalizados
    public static function GetAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM ticket_custom_values";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            $ticket_custom_values = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $ticket_custom_values;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los valores personalizados: " . $e->getMessage());
        }
    }

    //Funcion para obtener un valor personalizado por id
    public static function Get($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM ticket_custom_values WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result){
                return new CT_Values($result['id'], $result['custom_field_id'], $result['ticket_id'], $result['value']);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el valor personalizado: " . $e->getMessage());
        }
    }

    //Funcion para crear un valor personalizado
    public static function Create($ticket_custom_values)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "INSERT INTO ticket_custom_values (custom_field_id, ticket_id, value) VALUES (:custom_field_id, :ticket_id, :value)";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':custom_field_id', $ticket_custom_values->custom_field_id);
            $stmt->bindParam(':ticket_id', $ticket_custom_values->ticket_id);
            $stmt->bindParam(':value', $ticket_custom_values->value);
            $stmt->execute();
            $ticket_custom_values->id = $connection->lastInsertId();
            return $ticket_custom_values;
        } catch (PDOException $e) {
            throw new Exception("Error al crear el valor personalizado: " . $e->getMessage());
        }
    }

    //Funcion para actualizar un valor personalizado
    public static function Update($ticket_custom_values)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "UPDATE ticket_custom_values SET custom_field_id = :custom_field_id, ticket_id = :ticket_id, value = :value WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':custom_field_id', $ticket_custom_values->custom_field_id);
            $stmt->bindParam(':ticket_id', $ticket_custom_values->ticket_id);
            $stmt->bindParam(':value', $ticket_custom_values->value);
            $stmt->bindParam(':id', $ticket_custom_values->id);
            $stmt->execute();
            return $ticket_custom_values;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el valor personalizado: " . $e->getMessage());
        }
    }

    //Funcion para eliminar un valor personalizado
    public static function Delete($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM ticket_custom_values WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el valor personalizado: " . $e->getMessage());
        }
    }

    //Funcion para obtener los valores personalizados de un ticket
    public static function GetByTicket($ticket)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM ticket_custom_values WHERE ticket_id = :ticket_id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':ticket_id', $ticket);
            $stmt->execute();
            $ticket_custom_values = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $ticket_custom_values;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los valores personalizados del ticket: " . $e->getMessage());
        }
    }

    public static function DeleteByTicket($ticket)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM ticket_custom_values WHERE ticket_id = :ticket_id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':ticket_id', $ticket);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar los valores personalizados del ticket: " . $e->getMessage());
        }
    }
}