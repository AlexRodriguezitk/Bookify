<?php

namespace App\Traits;
//Importar base de datos;
use App\database\Database;

use Flight;
use PDO;

trait HasPermissions
{
    public function checkPermission($userId, $permissionName)
    {
        $db = Database::getInstance()->getConnection();
        if (!$db) {
            throw new \Exception('Database connection not found');
        }

        $query = $db->prepare("
            SELECT rp.is_allowed
            FROM users u
            JOIN roles r ON u.rol = r.id
            JOIN role_permissions rp ON r.id = rp.id_rol
            JOIN permissions p ON rp.id_permission = p.id
            WHERE u.id = :userId AND p.name = :permissionName
            LIMIT 1
        ");
        $query->execute([
            ':userId' => $userId,
            ':permissionName' => $permissionName
        ]);

        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result && (bool) $result['is_allowed']) {
            return true;
        }

        // Check if the user has the 'ALL' permission
        $query = $db->prepare("
            SELECT rp.is_allowed
            FROM users u
            JOIN roles r ON u.rol = r.id
            JOIN role_permissions rp ON r.id = rp.id_rol
            JOIN permissions p ON rp.id_permission = p.id
            WHERE u.id = :userId AND p.name = 'ALL'
            LIMIT 1
        ");
        $query->execute([
            ':userId' => $userId
        ]);

        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result ? (bool) $result['is_allowed'] : false;
    }
}
