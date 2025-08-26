<?php

namespace App\Modelsv2;

use App\Database\Database;
use App\models\User;
use App\Repositories\UsersRep;
use PDO;
use PDOException;
use Exception;

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
    public function getAll(?int $limit = 50, ?int $offset = 0, ?string $search = null): array
    {
        $query = "SELECT * FROM users";

        if ($search !== null) {
            $query .= " WHERE name LIKE :search OR username LIKE :search OR phone LIKE :search";
        }

        $query .= " LIMIT $limit OFFSET $offset";

        try {
            $stmt = $this->pdo->prepare($query);
            if ($search !== null) {
                $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            }
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($result)) {
                return [];
            }
            //array_map(fn($u) => $u->jsonSerialize(), $user);
            $users = array_map(function ($user) {
                return new UsersRep($user);
            }, $result);
            return $users;
        } catch (PDOException $e) {
            // Aquí puedes loguear el error
            return [];
        }
    }

    public function CountQuery(?string $search = null): int
    {
        $query = "SELECT COUNT(*) as total FROM users";

        if ($search !== null) {
            $query .= " WHERE name LIKE :search OR username LIKE :search OR phone LIKE :search";
        }

        try {
            $stmt = $this->pdo->prepare($query);
            if ($search !== null) {
                $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            }
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            // Aquí puedes loguear el error
            return 0;
        }
    }

    public function Create(UsersRep $user): UsersRep
    {
        try {
            $reflection = new \ReflectionClass($user);
            $props = $reflection->getProperties();

            $params = [];
            foreach ($props as $prop) {
                $name = $prop->getName();

                // ignoramos los que no van en INSERT
                if (in_array($name, ['id', 'created_at', 'updated_at', 'deleted_at', 'last_login_at'])) {
                    continue;
                }

                $getter = "get" . ucfirst($name);
                if (method_exists($user, $getter)) {
                    $params[$name] = $user->$getter();
                } else {
                    $prop->setAccessible(true);
                    $params[$name] = $prop->getValue($user);
                }
            }

            $columns = implode(", ", array_keys($params));
            $placeholders = ":" . implode(", :", array_keys($params));

            $stmt = $this->pdo->prepare("INSERT INTO users ($columns) VALUES ($placeholders)");
            $stmt->execute($params);

            $user->id = (int) $this->pdo->lastInsertId();
            return $user;
        } catch (\PDOException $e) {
            throw new \Exception("Error al crear el usuario: " . $e->getMessage());
        }
    }

    public function Update(UsersRep $user): UsersRep
    {
        try {
            $user->updated_at = new \DateTime();
            $reflection = new \ReflectionClass($user);
            $props = $reflection->getProperties();

            $params = [];
            foreach ($props as $prop) {
                $name = $prop->getName();

                // ignoramos los que no deben actualizarse
                if (in_array($name, ['id', 'created_at'])) {
                    continue;
                }

                $getter = "get" . ucfirst($name);
                if (method_exists($user, $getter)) {
                    $value = $user->$getter();
                    if ($value instanceof \DateTime) {
                        $value = $value->format('Y-m-d H:i:s');
                    }
                    $params[$name] = $value;
                } else {
                    $prop->setAccessible(true);
                    $value = $prop->getValue($user);
                    if ($value instanceof \DateTime) {
                        $value = $value->format('Y-m-d H:i:s');
                    }
                    $params[$name] = $value;
                }
            }

            $set = implode(", ", array_map(fn($field) => "$field = :$field", array_keys($params)));

            // importante: WHERE id obligatorio
            $params['id'] = $user->id;



            $stmt = $this->pdo->prepare("UPDATE users SET $set WHERE id = :id");
            $stmt->execute($params);

            return $user;
        } catch (\PDOException $e) {
            throw new \Exception("Error al actualizar el usuario: " . $e->getMessage());
        }
    }
}
