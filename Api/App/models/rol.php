<?php

namespace App\models;
//Importar base de datos;
use App\database\Database;

use PDO;
use PDOException;
use Exception;

class Rol {
    public $id;
    public $name;

    //Constructor
    public function __construct($id = null, $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    //Funciones
    //Funcion para obtener todos los roles
    public static function GetAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM roles";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            //Remove password from response
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $roles;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los roles: " . $e->getMessage());
        }
    }

    //Funcion para obtener un rol por id
    public static function Get($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM roles WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result){
                return new Rol($result['id'], $result['name']);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el rol: " . $e->getMessage());
        }
    }

    //Funcion para crear un rol
    public static function Create($rol)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "INSERT INTO roles (name) VALUES (:name)";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':name', $rol->name);
            $stmt->execute();
            $rol->id = $connection->lastInsertId();
            return $rol;
        } catch (PDOException $e) {
            throw new Exception("Error al crear el rol: " . $e->getMessage());
        }
    }

    //Funcion para actualizar un rol
    public static function Update($rol)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "UPDATE roles SET name = :name WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $rol->id);
            $stmt->bindParam(':name', $rol->name);
            $stmt->execute();
            return $rol;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el rol: " . $e->getMessage());
        }
    }

    //Funcion para eliminar un rol
    public static function Delete($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM roles WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el rol: " . $e->getMessage());
        }
    }
}

