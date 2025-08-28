<?php

namespace App\Services;

use App\Modelsv2\UserModel;
use App\Traits\HasPermissions;
use App\Langs\LangManager; // Elimina el alias LangService
use Flight;

use Exception;

class PermissionException extends Exception {}

class UserService
{
    use HasPermissions;
    public $UserModel;
    protected $langManager; // Propiedad para la instancia de LangManager

    public function __construct()
    {
        $this->UserModel = new UserModel();
        // Inicializa la instancia de LangManager
        $this->langManager = new LangManager();
    }

    public function getAll(?int $limit = 50, ?int $offset = 0, ?string $search = null, ?bool $list_all = false): array
    {
        $AuthUser = $this->UserModel->get(1);

        // Primero, verificar el permiso de lectura general de usuarios.
        // Esto es un requisito básico.
        if (!$this->checkPermission($AuthUser->id, 'users.read.list')) {
            $Message = $this->langManager->getMessage('unauthorized');
            throw new PermissionException($Message);
        }

        // Por defecto, asumimos que no es modo público.
        $publicMode = false;

        // Verificar si el usuario tiene permiso para ver todos los datos.
        if ($this->checkPermission($AuthUser->id, 'users.read.data.all')) {
            $publicMode = false; // No necesitamos modo público, tiene acceso completo.
        } elseif ($this->checkPermission($AuthUser->id, 'users.read.data.public')) {
            $publicMode = true; // Solo tiene permiso de modo público.
        } else {
            // Si no tiene 'all' ni 'public', denegar el acceso a los datos.
            $Message = $this->langManager->getMessage('unauthorized');
            throw new PermissionException($Message);
        }

        $users = $this->UserModel->getAll($limit, $offset, $search, $list_all);
        if (!$users) {
            $Message = $this->langManager->getMessage('not_found');
            throw new Exception($Message, 404);
        }
        // Serializar los usuarios basándose en el modo (público o completo).
        $usersSerialized = array_map(fn($u) => $u->jsonSerialize($publicMode), $users);
        $total = $this->UserModel->CountQuery($search);
        $pagination = [
            'total' => $total,
            'page' => 0,
            'limit' => isset($limit) ? $limit : null,
            'items' => count($usersSerialized),
            'total_pages' => isset($limit) ? ceil($total / $limit) : null
        ];
        return ['users' => $usersSerialized, 'pagination' => isset($limit) ? $pagination : []];
    }
}
