<?php

namespace App\Models;
//Importar base de datos;
use App\database\Database;
use App\traits\ApiResponse;

use PDO;
use PDOException;
use Exception;

class Terminal
{
    public $id;
    public $terminal_ext;

    //Constructor
    public function __construct($id = null, $terminal_ext = null)
    {
        $this->id = $id;
        $this->terminal_ext = $terminal_ext;
    }

    //Funciones
    //Funcion para obtener todos los terminales
    public static function GetAll()
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM terminals";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            //Remove password from response
            $terminal = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $terminal;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todos los terminales: " . $e->getMessage());
        }
    }

    //Funcion para obtener un terminal por id
    public static function Get($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT * FROM terminals WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new terminal($result['id'], $result['terminal_ext']);
            }
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el terminal: " . $e->getMessage());
        }
    }

    //Funcion para crear un terminal
    public static function Create($terminal)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "INSERT INTO terminals (terminal_ext) VALUES (:terminal_ext)";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':terminal_ext', $terminal->terminal_ext);
            $stmt->execute();
            $terminal->id = $connection->lastInsertId();
            return $terminal;
        } catch (PDOException $e) {
            throw new Exception("Error al crear el terminal: " . $e->getMessage());
        }
    }

    //Funcion para actualizar un terminal
    public static function Update($terminal)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "UPDATE terminals SET terminal_ext = :terminal_ext WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $terminal->id);
            $stmt->bindParam(':terminal_ext', $terminal->terminal_ext);
            $stmt->execute();
            return $terminal;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el terminal: " . $e->getMessage());
        }
    }

    //Funcion para eliminar un terminal
    public static function Delete($id)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM terminals WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el terminal: " . $e->getMessage());
        }
    }
    //Funcion para obtener asignaciones de terminales
    public static function GetAssignments($asesor)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "SELECT terminals.id, terminals.terminal_ext FROM terminals JOIN asesor_terminals ON terminals.id = asesor_terminals.id_terminal WHERE asesor_terminals.id_asesor = :id_asesor";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id_asesor', $asesor->id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las asignaciones de terminales: " . $e->getMessage());
        }
    }

    //Funcion para asignar una terminal
    public static function Assign($terminal, $asesor)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "INSERT INTO asesor_terminals (id_asesor, id_terminal) VALUES (:id_asesor, :id_terminal)";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id_asesor', $asesor->id);
            $stmt->bindParam(':id_terminal', $terminal->id);
            $stmt->execute();
            $message = "Terminal $terminal->terminal_ext asignada al asesor $asesor->name";
            return $message;
        } catch (PDOException $e) {
            throw new Exception("Error al asignar la terminal: " . $e->getMessage());
        }
    }

    //desasignar terminal
    public static function Unassign($terminal, $asesor)
    {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            $query = "DELETE FROM asesor_terminals WHERE id_asesor = :id_asesor AND id_terminal = :id_terminal";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id_asesor', $asesor->id);
            $stmt->bindParam(':id_terminal', $terminal->id);
            $stmt->execute();
            $message = "Terminal $terminal->terminal_ext desasignada al asesor $asesor->name";
            return $message;
        } catch (PDOException $e) {
            throw new Exception("Error al desasignar la terminal: " . $e->getMessage());
        }
    }
}
