<?php

namespace App\Modelsv2;

use App\Database\Database;
use App\Repositories\UsersRep;
use PDO;
use PDOException;

class UserModel
{
    private PDO $pdo;

    public function __construct()
    {
        // Obtener conexión desde Database
        $db = Database::getInstance();

        if ($db->hasError()) {
            throw new \RuntimeException("Error en la conexión: " . $db->getError());
        }

        $this->pdo = $db->getConnection();
    }

    /**
     * Devuelve un UsersRep a partir de un ID.
     */
    public function get(int $id): ?UsersRep
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                return null;
            }

            // Pasar la data al repositorio
            return new UsersRep($data);
        } catch (PDOException $e) {
            // Aquí puedes loguear el error
            return null;
        }
    }
}
