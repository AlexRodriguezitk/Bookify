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

        // Primero, verificar si el usuario tiene el permiso ALL
        $query = $db->prepare("
            SELECT EXISTS (
                SELECT 1 FROM users AS u
                JOIN user_roles AS ur ON ur.id_user = u.id
                JOIN roles AS r ON r.id = ur.id_role
                JOIN role_permissions AS rp ON rp.id_role = r.id
                JOIN permissions AS p ON p.id = rp.id_permission
                WHERE u.id = :userId AND p.name = :permissionName OR u.id = :userId AND p.name = 'global.access'
            ) AS is_allowed
        ");
        $query->execute([
            ':userId' => $userId,
            ':permissionName' => $permissionName
        ]);

        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result ? (bool) $result['is_allowed'] : false;
    }
}
