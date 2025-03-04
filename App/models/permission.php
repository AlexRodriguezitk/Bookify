<?php

namespace App\models;
//Importar base de datos;
use App\database\Database;

use PDO;
use PDOException;
use Exception;

class permission
{
    public $id;
    public $name;
    public $description;

    public function __construct($id = null, $name = null, $description = null){
        $this->id = $id;
        $this->name= $name;
        $this->description = $description;
    } 

    //Funcion para obtener todos los logs
    public static function GetAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM permissions";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $permissions;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los permisos: " . $e->getMessage());
        }
    }

    //Funcion para obtener un log por id
    public static function Get($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM permissions WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result){
                return new permission($result['id'], $result['name'], $result['description']);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el permiso: " . $e->getMessage());
        }
    }

    //Funcion para crear un usuario
    public static function Create($permission)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = 'INSERT INTO permissions (name, description) VALUES (:name, :description)';
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':name', $permission->name);
            $stmt->bindParam(':description', $permission->description);
            $stmt->execute();
            $permission->id = $connection->lastInsertId();
            return $permission;
        } catch (PDOException $e) {
            throw new Exception("Error al crear el permiso: " . $e->getMessage());
        }
    }

    //Funcion para actualizar un usuario
    public static function Update($permission)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = 'UPDATE permissions SET name = :name, description = :description WHERE id = :id';
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $permission->id);
            $stmt->bindParam(':name', $permission->name);
            $stmt->bindParam(':description', $permission->description);
            $stmt->execute();
            return $permission;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el permiso: " . $e->getMessage());
        }
    }

    //Funcion para eliminar un usuario
    public static function Delete($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = 'DELETE FROM permissions WHERE id = :id';
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el permiso: " . $e->getMessage());
            return null;
        }
    }
}