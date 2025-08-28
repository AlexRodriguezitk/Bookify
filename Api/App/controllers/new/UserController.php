<?php

namespace App\Controllers\New;

use App\traits\ApiResponse;
use App\Langs\LangManager;
use App\Services\UserService;
use App\Services\PermissionException;
use Flight;

class UserController
{
    use ApiResponse;
    public $userService;

    public $DefaultLimit = 10;
    protected $langManager;
    public function __construct()
    {
        $this->userService = new UserService();
        $this->langManager = new LangManager();
    }
    public function index()
    {
        $params = Flight::request()->query->getData();
        $limit = (int)($params['limit'] ?? $this->DefaultLimit);
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;
        $search = $params['search'] ?? null;

        try {
            $result = $this->userService->getAll($limit, $offset, $search);
            $users = $result['users'];
            $pagination = $result['pagination'];
            if (isset($pagination['page'])) {
                $pagination['page'] = $page;
            }
            $this->success(data: $users, message: $this->langManager->getMessage('users_found'), code: 200, pagination: $pagination);
        } catch (PermissionException $e) {
            $this->failed(null, $e->getMessage(), 403);
        } catch (\Exception $e) {
            if ($e->getCode() == 404) {
                $this->failed(null, $e->getMessage(), 404);
                return;
            }
            $this->failed(null, $e->getMessage());
        }
    }
}
