<?php

namespace App\database;

use PDO;
use PDOException;
use Exception;

class Database
{
    private static $instance = null;
    private $connection = null;
    private $error = null;

    // Constructor
    private function __construct()
    {
        $config = require __DIR__ . '/../config/config.php';

        try {
            $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['name']}";
            $this->connection = new PDO($dsn, $config['db']['user'], $config['db']['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->error = $e->getMessage(); // Guardar error sin lanzar excepción
        }
    }

    // Obtener la instancia (Singleton)
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Obtener conexión, pero sin lanzar excepción
    public function getConnection()
    {
        return $this->connection;
    }

    // Verificar si hay error de conexión
    public function hasError()
    {
        return $this->error !== null;
    }

    // Obtener mensaje de error
    public function getError()
    {
        return $this->error;
    }
}
