<?php

namespace App\Models;
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
    public $profile_image;
    public $created_at;
    public $is_active;
    public $totp_secret; // ✅ Agregada la propiedad totp_secret

    //Constructor
    public function __construct($id = null, $name = null, $username = null, $password = null, $phone = null, $rol = null, $profile_image = null, $created_at = null, $is_active = null, $totp_secret = null) // ✅ Agregado el parámetro totp_secret
    {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
        $this->phone = $phone;
        $this->rol = $rol;
        $this->profile_image = $profile_image;
        $this->created_at = $created_at;
        $this->is_active = $is_active;
        $this->totp_secret = $totp_secret; // ✅ Asignado el valor a la propiedad
    }

    //Funciones
    //Funcion para obtener todos los usuarios
    public static function GetAll($limit = null, $offset = 0)
    {
        $query = "SELECT * FROM users";
        $query = $limit ? $query . " LIMIT :limit OFFSET :offset" : $query;
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $stmt = $connection->prepare($query);
            if ($limit) {
                $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
            }
            $stmt->execute();
            //Remove password from response
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($users as $key => $user) {
                unset($users[$key]['password']);
                unset($users[$key]['totp_secret']); // ✅ Se elimina el secreto TOTP de la respuesta
            }
            return $users;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los usuarios: " . $e->getMessage());
        }
    }


    public static function getSearch($search, $limit = null, $offset = 0)
    {
        $terms = array_filter(explode(' ', $search)); // Divide y elimina vacíos
        if (empty($terms)) return [];

        // Construye condiciones dinámicas
        $conditions = [];
        foreach ($terms as $i => $term) {
            $conditions[] = "(name LIKE :term$i OR username LIKE :term$i OR phone LIKE :term$i)";
        }
        $whereClause = implode(" AND ", $conditions); // AND para que cumpla todos los términos

        $query = "SELECT * FROM users WHERE $whereClause";
        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $stmt = $connection->prepare($query);

            // Bind de los términos
            foreach ($terms as $i => $term) {
                $stmt->bindValue(":term$i", '%' . $term . '%', PDO::PARAM_STR);
            }

            if ($limit !== null) {
                $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
            }

            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ✅ Se elimina el secreto TOTP de la respuesta
            foreach ($users as $key => $user) {
                unset($users[$key]['totp_secret']);
            }

            return $users;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los usuarios: " . $e->getMessage());
        }
    }

    //Contar todos los usuarios
    public static function Count($search = null)
    {
        if ($search != null) {
            $query = "SELECT COUNT(*) as total FROM users WHERE name LIKE :search OR username LIKE :search OR phone LIKE :search";
        } else {
            $query = "SELECT COUNT(*) as total FROM users";
        }
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $stmt = $connection->prepare($query);
            if ($search != null) {
                $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            }
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
                return new self($result['id'], $result['name'], $result['username'], $result['password'], $result['phone'], $result['rol'], $result['profile_image'], $result['created_at'], $result['is_active'], $result['totp_secret']); // ✅ Se pasa el totp_secret al constructor
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
            $query = 'INSERT INTO users (name, username, password, phone, rol, is_active, profile_image, totp_secret) VALUES (:name, :username, :password, :phone, :rol, :is_active, :profile_image, :totp_secret)'; // ✅ Agregadas las columnas profile_image y totp_secret
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':name', $user->name);
            $stmt->bindParam(':username', $user->username);
            $stmt->bindParam(':password', $user->password);
            $stmt->bindParam(':phone', $user->phone);
            $stmt->bindParam(':rol', $user->rol);
            $stmt->bindParam(':is_active', $user->is_active);
            $stmt->bindParam(':profile_image', $user->profile_image); // ✅ Se agregó el bindParam para profile_image
            $stmt->bindParam(':totp_secret', $user->totp_secret); // ✅ Se agregó el bindParam para totp_secret
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
            $query = 'UPDATE users SET name = :name, username = :username, password = :password, phone = :phone, rol = :rol, profile_image = :profile_image ,is_active = :is_active, totp_secret = :totp_secret WHERE id = :id'; // ✅ Agregada la columna totp_secret en el UPDATE
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $user->id);
            $stmt->bindParam(':name', $user->name);
            $stmt->bindParam(':username', $user->username);
            $stmt->bindParam(':password', $user->password);
            $stmt->bindParam(':phone', $user->phone);
            $stmt->bindParam(':profile_image', $user->profile_image);
            $stmt->bindParam(':rol', $user->rol);
            $stmt->bindParam(':is_active', $user->is_active);
            $stmt->bindParam(':totp_secret', $user->totp_secret); // ✅ Se agregó el bindParam para totp_secret
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

                return new self(
                    $result['id'],
                    $result['name'],
                    $result['username'],
                    $result['password'],
                    $result['phone'],
                    $result['rol'],
                    $result['profile_image'],
                    $result['created_at'],
                    $result['is_active'],
                    $result['totp_secret'] // Asegúrate de que este campo exista en la BD.
                );
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
                // ✅ Se elimina el secreto TOTP de la respuesta para seguridad
                $userModel = new self($user['id'], $user['name'], $user['username'], null, $user['phone'], $user['rol'], $user['profile_image'], $user['created_at'], $user['is_active'], null);
                unset($userModel->password);
                $result[] = $userModel;
            }
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los usuarios: " . $e->getMessage());
        }
    }
}
