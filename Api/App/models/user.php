<?php

namespace App\models;
//Importar base de datos;
use App\database\Database;

use PDO;
use PDOException;
use Exception;

class User
{
    public $id;
    public $name;
    public $username;
    public $password;
    public $phone;
    public $rol;
    public $is_active;

    //Constructor
    public function __construct($id = null, $name = null, $username = null, $password = null, $phone = null, $rol = null, $is_active = true)
    {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
        $this->phone = $phone;
        $this->rol = $rol;
        $this->is_active = $is_active;
    }

    //Funciones
    //Funcion para obtener todos los usuarios
    public static function GetAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM users";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            //Remove password from response
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($users as $key => $user) {
                unset($users[$key]['password']);
            }
            return $users;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los usuarios: " . $e->getMessage());
        }
    }

    //Funcion para obtener todos los usuarios paginados
    public static function getPaginated($limit, $offset)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM users LIMIT :limit OFFSET :offset";
            $stmt = $connection->prepare($query);
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
            $stmt->execute();
            //Remove password from response
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($users as $key => $user) {
                unset($users[$key]['password']);
            }
            return $users;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los usuarios: " . $e->getMessage());
        }
    }

    //Contar todos los usuarios
    public static function Count()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT COUNT(*) as total FROM users";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            throw new Exception("Error al contar los usuarios: " . $e->getMessage());
        }
    }

    //Funcion para obtener un usuario por id
    public static function Get($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM users WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new self($result['id'], $result['name'], $result['username'], $result['password'], $result['phone'], $result['rol'], $result['is_active']);
            }
            return null;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el usuario: " . $e->getMessage());
        }
    }

    //Funcion para crear un usuario
    public static function Create($user)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = 'INSERT INTO users (name, username, password, phone, rol, is_active) VALUES (:name, :username, :password, :phone, :rol, :is_active)';
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':name', $user->name);
            $stmt->bindParam(':username', $user->username);
            $stmt->bindParam(':password', $user->password);
            $stmt->bindParam(':phone', $user->phone);
            $stmt->bindParam(':rol', $user->rol);
            $stmt->bindParam(':is_active', $user->is_active);
            $stmt->execute();
            $user->id = $connection->lastInsertId();
            return $user;
        } catch (PDOException $e) {
            throw new Exception("Error al crear el usuario: " . $e->getMessage());
        }
    }

    //Funcion para actualizar un usuario
    public static function Update($user)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = 'UPDATE users SET name = :name, username = :username, password = :password, phone = :phone, rol = :rol, is_active = :is_active WHERE id = :id';
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $user->id);
            $stmt->bindParam(':name', $user->name);
            $stmt->bindParam(':username', $user->username);
            $stmt->bindParam(':password', $user->password);
            $stmt->bindParam(':phone', $user->phone);
            $stmt->bindParam(':rol', $user->rol);
            $stmt->bindParam(':is_active', $user->is_active);
            $stmt->execute();
            return $user;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el usuario: " . $e->getMessage());
        }
    }

    //Funcion para eliminar un usuario
    public static function Delete($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = 'DELETE FROM users WHERE id = :id';
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el usuario: " . $e->getMessage());
            return null;
        }
    }

    //Get user by username
    public static function GetByUsername($username)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM users WHERE username = :username";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new self($result['id'], $result['name'], $result['username'], $result['password'], $result['phone'], $result['rol'], $result['is_active']);
            }
            return null;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el usuario: " . $e->getMessage());
        }
    }

    //Get users list by rol
    public static function GetByRol($rol)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM users WHERE rol = :rol";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':rol', $rol);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result = [];
            foreach ($users as $user) {
                $userModel = new self($user['id'], $user['name'], $user['username'], null, $user['phone'], $user['rol'], $user['is_active']);
                unset($userModel->password);
                $result[] = $userModel;
            }
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los usuarios: " . $e->getMessage());
        }
    }
}
