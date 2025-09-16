<?php

namespace App\Models;
//Importar base de datos;
use App\database\Database;

use PDO;
use PDOException;
use Exception;

class Permission
{
    public $id;
    public $name;
    public $description;

    public function __construct($id = null, $name = null, $description = null)
    {
        $this->id = $id;
        $this->name = $name;
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
            if ($result) {
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

    //Funcion para obtener asignaciones de terminales
    public static function GetAssignments($rol)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT permissions.id, permissions.name FROM permissions JOIN role_permissions ON permissions.id = role_permissions.id_permission WHERE role_permissions.id_rol = :id_rol and role_permissions.is_allowed = 1";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id_rol', $rol->id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las asignaciones de permisos: " . $e->getMessage());
        }
    }


    public static function Assign($permission, $rol)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();

            // Verificar si ya existe la relación
            $checkQuery = "SELECT * FROM role_permissions WHERE id_permission = :id_permission AND id_rol = :id_rol";
            $checkStmt = $connection->prepare($checkQuery);
            $checkStmt->bindParam(':id_permission', $permission->id);
            $checkStmt->bindParam(':id_rol', $rol->id);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                // Ya existe: actualizamos is_allowed a true
                $updateQuery = "UPDATE role_permissions SET is_allowed = 1 WHERE id_permission = :id_permission AND id_rol = :id_rol";
                $updateStmt = $connection->prepare($updateQuery);
                $updateStmt->bindParam(':id_permission', $permission->id);
                $updateStmt->bindParam(':id_rol', $rol->id);
                $updateStmt->execute();
                $message = "Permiso $permission->name actualizado (is_allowed = true) para el rol $rol->name";
            } else {
                // No existe: insertamos nuevo
                $insertQuery = "INSERT INTO role_permissions (id_permission, id_rol, is_allowed) VALUES (:id_permission, :id_rol, 1)";
                $insertStmt = $connection->prepare($insertQuery);
                $insertStmt->bindParam(':id_permission', $permission->id);
                $insertStmt->bindParam(':id_rol', $rol->id);
                $insertStmt->execute();
                $message = "Permiso $permission->name asignado al rol $rol->name";
            }

            return $message;
        } catch (PDOException $e) {
            throw new Exception("Error al asignar el permiso: " . $e->getMessage());
        }
    }

    //desasignar terminal
    public static function Unassign($permission, $rol)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM role_permissions WHERE id_rol = :id_rol AND id_permission = :id_permission";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id_rol', $rol->id);
            $stmt->bindParam(':id_permission', $permission->id);
            $stmt->execute();
            $message = "Permission $permission->name desasignada al rol $rol->name";
            return $message;
        } catch (PDOException $e) {
            throw new Exception("Error al desasignar el permiso: " . $e->getMessage());
        }
    }
}
