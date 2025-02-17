<?php

namespace App\database;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection;

    //consturctor
    private function __construct()
    {
        $config = require __DIR__ . '/../config/config.php';

        try {
            $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['name']}";
            $this->connection = new PDO($dsn, $config['db']['user'], $config['db']['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    //Funcion para obtener la instancia de la base de datos
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    //Funcion para obtener la conexion
    public function getConnection()
    {
        return $this->connection;
    }
}
