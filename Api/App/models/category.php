<?php

namespace App\Models;
//Importar base de datos;
use App\Database\Database;

use PDO;
use PDOException;
use Exception;

class Category
{
    public $id;
    public $name;

    //Constructor
    public function __construct($id = null, $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    //Funcion para obtener todas las categorias
    public static function GetAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM categories";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            //Remove password from response
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $roles;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los roles: " . $e->getMessage());
        }
    }

    //Funcion para obtener una categoria por id
    public static function Get($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM categories WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new Category($result['id'], $result['name']);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la categoria: " . $e->getMessage());
        }
    }

    //Funcion para crear una categoria
    public static function Create($category)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "INSERT INTO categories (name) VALUES (:name)";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':name', $category->name);
            $stmt->execute();
            $category->id = $connection->lastInsertId();
            return $category;
        } catch (PDOException $e) {
            throw new Exception("Error al crear la categoria: " . $e->getMessage());
        }
    }

    //Funcion para actualizar una categoria
    public static function Update($category)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "UPDATE categories SET name = :name WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':name', $category->name);
            $stmt->bindParam(':id', $category->id);
            $stmt->execute();
            return $category;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la categoria: " . $e->getMessage());
        }
    }

    //Funcion para eliminar una categoria
    public static function Delete($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM categories WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar la categoria: " . $e->getMessage());
        }
    }
}
