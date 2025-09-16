<?php

namespace App\Models;

use App\database\Database;
use PDO;
use PDOException;
use Exception;

class Setting
{
    public $id;
    public $key;
    public $value;
    public $type;
    public $description;
    public $created_at;
    public $updated_at;

    // Constructor
    public function __construct(
        $id = null,
        $key = null,
        $value = null,
        $type = null,
        $description = null,
        $created_at = null,
        $updated_at = null
    ) {
        $this->id = $id;
        $this->key = $key;
        $this->value = $value;
        $this->type = $type;
        $this->description = $description;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // Obtener todas las configuraciones
    public static function GetAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM settings";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todas las configuraciones: " . $e->getMessage());
        }
    }

    // Obtener configuración por clave
    public static function GetByKey($key)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM settings WHERE `key` = :key LIMIT 1";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':key', $key);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new Setting(
                    $result['id'],
                    $result['key'],
                    $result['value'],
                    $result['type'],
                    $result['description'],
                    $result['created_at'],
                    $result['updated_at']
                );
            }
            return null;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la configuración: " . $e->getMessage());
        }
    }

    // Actualizar valor de una configuración
    public static function UpdateValue($key, $value)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "UPDATE settings SET `value` = :value WHERE `key` = :key";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':key', $key);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la configuración: " . $e->getMessage());
        }
    }
}
