<?php

namespace App\models;
//Importar base de datos;
use App\database\Database;

use PDO;
use PDOException;
use Exception;

class CT_Fields
{
    //Custom Ticket Fields
    public $id;
    public $category;
    public $name;
    public $type; //ENUM['text', 'textarea', 'select', 'checkbox', 'radio', 'date', 'time', 'datetime', 'email', 'phone', 'url', 'number', 'decimal', 'percent', 'currency', 'hidden']

    //Constructor
    public function __construct($id = null, $category = null, $name = null, $type = null)
    {
        $this->id = $id;
        $this->category = $category;
        $this->name = $name;
        $this->type = $type;
    }

    //Funciones
    //Funcion para obtener todos los campos personalizados
    public static function GetAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM custom_ticket_fields";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            $custom_ticket_fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $custom_ticket_fields;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los campos personalizados: " . $e->getMessage());
        }
    }

    //Funcion para obtener un campo personalizado por id

    public static function Get($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM custom_ticket_fields WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new CT_Fields($result['id'], $result['category_id'], $result['custom_field'], $result['enum_type']);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el campo personalizado: " . $e->getMessage());
        }
    }

    //Funcion para crear un campo personalizado
    public static function Create($custom_ticket_fields)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "INSERT INTO custom_ticket_fields (category_id, custom_field, enum_type) VALUES (:category, :name, :type)";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':category', $custom_ticket_fields->category);
            $stmt->bindParam(':name', $custom_ticket_fields->name);
            $stmt->bindParam(':type', $custom_ticket_fields->type);
            $stmt->execute();
            $custom_ticket_fields->id = $connection->lastInsertId();
            return $custom_ticket_fields;
        } catch (PDOException $e) {
            throw new Exception("Error al crear el campo personalizado: " . $e->getMessage());
        }
    }

    //Funcion para actualizar un campo personalizado
    public static function Update($custom_ticket_fields)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "UPDATE custom_ticket_fields SET category_id = :category, custom_field = :name, enum_type = :type WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':category', $custom_ticket_fields->category);
            $stmt->bindParam(':name', $custom_ticket_fields->name);
            $stmt->bindParam(':type', $custom_ticket_fields->type);
            $stmt->bindParam(':id', $custom_ticket_fields->id);
            $stmt->execute();
            return $custom_ticket_fields;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el campo personalizado: " . $e->getMessage());
        }
    }

    //Funcion para eliminar un campo personalizado
    public static function Delete($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM custom_ticket_fields WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el campo personalizado: " . $e->getMessage());
        }
    }

    //Funcion para obtener los campos personalizados de una categoria
    public static function GetByCategory($category)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM custom_ticket_fields WHERE category_id = :category";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':category', $category);
            $stmt->execute();
            $custom_ticket_fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $custom_ticket_fields;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los campos personalizados de la categoria: " . $e->getMessage());
        }
    }
}