<?php

namespace App\controllers;

use App\traits\ApiResponse;
use App\traits\Log;
use App\traits\HasPermissions;
use App\models\User;
use App\models\Rol;
use Flight;


class UserController
{
    use ApiResponse;
    use Log;
    use HasPermissions;

    public function index()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $has_pagination = isset($_GET['page']) && isset($_GET['limit']);
        $has_search = isset($_GET['search']);


        if ($has_pagination) {
            $page = $_GET['page'];
            $limit = $_GET['limit'];

            $offset = ($page - 1) * $limit;

            if ($has_search) {
                $search = $_GET['search'];
                $total = User::Count($search);
                $pages = ceil($total / $limit);
                $users = User::getSearch($search, $limit, $offset);
            } else {
                $total = User::Count();
                $pages = ceil($total / $limit);
                $users = User::GetAll($limit, $offset);
            }
            if (empty($users)) {
                $this->failed(null, 'Users not found', 404);
                return;
            }

            foreach ($users as $key => $user) {
                $userArray = (array) $user;
                unset($userArray['password']);
                $userArray['rol'] = Rol::get($userArray['rol']);
                $users[$key] = (object) $userArray;
            }

            $this->success(['users' => $users, 'pagination' => ['page' => $page, 'limit' => $limit, 'total' => $total, 'total_pages' => $pages]], 'Users found', 200);
        } else {
            $users = User::GetAll();
        }
    }

    public function show($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $user = User::get($id);
        if ($user) {
            unset($user->password);
            unset($user->totp_secret);
            $this->success([$user], 'User found', 200);
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }

    public function store()
    {
        //ChekPermissions
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.CREATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $data = Flight::request()->data->getData();

        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }

        $requiredFields = ['name', 'username', 'password', 'rol', 'is_active'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $user = new User(null, $data['name'], $data['username'], $data['password'], $data['phone'] ?? "0", $data['rol'], null, null, $data['is_active']);
        $user = User::create($user);
        $this->saveLog($AuthUser->id, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: ' . $user->name);
        $this->success([$user], 'User created', 201);
    }

    public function update($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.UPDATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        $user = User::get($id);

        if ($user->id == $AuthUser->id && $data['rol'] != $user->rol) {
            $this->failed(null, 'You cannot change your own role', 403);
            return;
        }
        if ($user) {
            $user->name = $data['name'] ?? $user->name;
            $user->username = $data['username'] ?? $user->username;
            $user->password = isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : $user->password;
            $user->phone = $data['phone'] ?? $user->phone;
            $user->rol = $data['rol'] ?? $user->rol;
            $user = User::update($user);
            $this->saveLog($AuthUser->id, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: ' . $user->name);
            $this->success([$user], 'User updated', 200);
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }

    public function destroy($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.DELETE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        if ($AuthUser->id == $id) {
            $this->failed(null, 'You cannot delete yourself', 403);
            return;
        }
        $user = User::get($id);
        if ($user) {
            User::delete($id);
            $this->saveLog($AuthUser->id, 'USER_DELETED', 'USER WAS DELETED SUCCESSFULLY: ' . $user->name);
            $this->success([null], 'User deleted', 200);
        } else {
            $this->failed([null], 'User not found', 404);
        }
    }

    public function Active($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.ACTIVATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $user = User::get($id);
        if ($user) {
            $user->is_active = 1;
            $user = User::update($user);
            $this->saveLog($AuthUser->id, 'USER_ACTIVE', 'USER WAS ACTIVATED SUCCESSFULLY: ' . $user->name);
            $this->success([$user], 'User activated', 200);
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }

    public function Inactive($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.ACTIVATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $user = User::get($id);
        if ($user) {
            if ($user->id == $AuthUser->id) {
                $this->failed(null, "You can't inactive your own user", 403);
            } else {
                $user->is_active = 0;
                $user = User::update($user);
                $this->saveLog($AuthUser->id, 'USER_INACTIVE', 'USER WAS INACTIVATED SUCCESSFULLY: ' . $user->name);
                $this->success([$user], 'User inactivated', 200);
            }
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }

    public function loginPassword()
    {
        $data = Flight::request()->data->getData();
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }

        $requiredFields = ['username', 'password'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }

        $user = User::getByUsername($data['username']);

        if ($user && password_verify($data['password'], $user->password) && $user->is_active == 1) {

            // ✅ Corregido: Si el usuario tiene 2FA, se anida la respuesta para que el frontend la reciba correctamente.
            if ($user->totp_secret) {
                $this->success(['2fa_required' => true, 'username' => $user->username], '2FA required', 200);
                return;
            }

            // Si no tiene 2FA, el login continúa como antes
            $token = \App\Auth::generateToken($user->id, $user->rol);
            Flight::set('user', $user);
            $this->saveLog($user->id, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: ' . $user->name);
            $this->setJwtBearerToken($token);
            $rol = Rol::Get($user->rol);
            $user->rol = $rol;
            $this->success(['token' => $token, 'user' => $user], 'Login successful', 200);
        } else {
            $this->failed(null, 'Invalid credentials', 401);
        }
    }

    public function loginVerify2fa()
    {
        $data = Flight::request()->data->getData();

        if (empty($data['username']) || empty($data['otp_code'])) {
            $this->failed(null, "Username and OTP code are required", 400);
            return;
        }

        $user = User::getByUsername($data['username']);

        if (!$user || $user->is_active == 0) {
            $this->failed(null, 'Invalid credentials', 401);
            return;
        }

        // Verificar si el usuario tiene un secreto 2FA
        if (empty($user->totp_secret)) {
            $this->failed(null, '2FA not enabled for this user', 401);
            return;
        }

        // ✅ Implementación de 2FA: Verificar el código OTP
        $totp = \OTPHP\TOTP::create($user->totp_secret);
        $isValid = $totp->verify($data['otp_code']);

        if ($isValid) {
            // Si el código es válido, genera el token JWT y completa el login
            $token = \App\Auth::generateToken($user->id, $user->rol);
            Flight::set('user', $user);
            $this->saveLog($user->id, 'USER_LOGIN_2FA', 'USER WAS LOGGED IN WITH 2FA: ' . $user->name);
            $this->setJwtBearerToken($token);
            $rol = Rol::Get($user->rol);
            $user->rol = $rol;
            unset($user->totp_secret);
            $this->success(['token' => $token, 'user' => $user], 'Login successful', 200);
        } else {
            $this->failed(null, 'Invalid OTP code', 401);
        }
    }

    public function renew()
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            $this->failed(null, 'Authorization header missing', 401);
            return;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $decoded = \App\Auth::validateToken($token);
        if (!$decoded) {
            $this->failed(null, 'Invalid or expired token', 401);
            return;
        }

        $user = User::Get($decoded->id);
        if (!$user) {
            $this->failed(null, 'User not found', 404);
            return;
        }
        if ($user->is_active == 0) {
            $this->failed(null, 'User is inactive', 401);
            return;
        }

        $newToken = \App\Auth::generateToken($user->id, $user->rol);
        $rol = Rol::Get($user->rol);
        $user->rol = $rol;
        $this->setJwtBearerToken($newToken);
        $this->success(['token' => $newToken, 'user' => $user], 'Token renewed successfully', 200);
    }


    public function register()
    {
        $data = Flight::request()->data->getData();
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }

        $requiredFields = ['name', 'username', 'password', 'phone'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }

        $user = User::getByUsername($data['username']);
        if ($user) {
            $this->failed(null, 'Username already exists', 400);
            return;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // ✅ NO se genera el TOTP secret aquí. Se deja como NULL.
        $user = new User(null, $data['name'], $data['username'], $data['password'], $data['phone'], 2, null, null, 1, null);
        $user = User::create($user);

        $token = \App\Auth::generateToken($user->id, $user->rol);
        $this->setJwtBearerToken($token);

        $rol = Rol::Get($user->rol);
        $user->rol = $rol;

        $this->saveLog($user->id, 'USER_REGISTERED', 'USER WAS REGISTERED SUCCESSFULLY: ' . $user->name);

        // ✅ La respuesta de registro ya no incluye el URI de TOTP.
        $this->success(['user' => $user, 'token' => $token], 'User registered', 201);
    }

    public function generateTotpSecret()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.ACTIVATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        // Solo se permite a usuarios autenticados acceder a esta función
        $user = User::Get($AuthUser->id);
        if (!$user) {
            $this->failed(null, 'Unauthorized', 401);
            return;
        }

        // Corregido: Validar que el nombre de usuario no esté vacío
        if (empty($user->username)) {
            $this->failed(null, 'Username is not set for the authenticated user.', 500);
            return;
        }

        $totp = \OTPHP\TOTP::create();
        $totpSecret = $totp->getSecret();

        // ✅ CORREGIDO: Construir el URI manualmente para evitar el error de la librería.
        $issuer = 'Bookify';
        $label = urlencode($user->username);
        $totpUri = "otpauth://totp/{$issuer}:{$label}?secret={$totpSecret}&issuer={$issuer}";

        // Devolver el URI para el código QR y el secreto para validación posterior
        $this->success(['totp_uri' => $totpUri, 'totp_secret' => $totpSecret], 'New TOTP secret generated', 200);
    }

    public function enable2fa()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser) {
            $this->failed(null, 'Unauthorized', 401);
            return;
        }

        $data = Flight::request()->data->getData();
        if (empty($data['otp_code']) || empty($data['totp_secret'])) {
            $this->failed(null, 'OTP code and TOTP secret are required', 400);
            return;
        }

        // ✅ CORREGIDO: Obtener el objeto User completo desde la base de datos
        $user = User::Get($AuthUser->id);
        if (!$user) {
            $this->failed(null, 'User not found', 404);
            return;
        }

        // Validar el código TOTP proporcionado con el secreto temporal
        $totp = \OTPHP\TOTP::create($data['totp_secret']);
        if (!$totp->verify($data['otp_code'])) {
            $this->failed(null, 'Invalid OTP code', 401);
            return;
        }

        // ✅ La validación fue exitosa, ahora se puede guardar el secreto en el perfil del usuario
        $user->totp_secret = $data['totp_secret'];
        User::Update($user);

        $this->saveLog($user->id, '2FA_ENABLED', '2FA WAS ACTIVATED SUCCESSFULLY');

        $this->success([null], '2FA has been successfully enabled', 200);
    }


    public function Profile()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PROFILE.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $user = User::get($AuthUser->id);
        $user->rol = Rol::Get($user->rol);
        if ($user) {
            unset($user->password);
            $user->totp_secret = $user->totp_secret ? true : false;
            $this->success([$user], 'User found', 200);
        } else {
            $this->failed(null, 'User not found', 404);
        }
    }

    //Get users by rol
    public function GetByRol($id)
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'USER.READ')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }

        $rol = Rol::Get($id);
        if (!$rol) {
            $this->failed(null, 'Rol not found', 404);
            return;
        }
        $users = User::GetByRol($rol->id);
        if ($users) {
            $this->success($users, 'Users list', 200);
        } else {
            $this->failed(null, 'Users not found', 404);
        }
    }

    public function GenPassword()
    {
        $password = \App\Auth::GenPassword();
        $this->success([$password], 'Password generated', 200);
    }

    public function change_image()
    {
        $AuthUser = Flight::get('user');
        if (!$AuthUser || !isset($AuthUser->id) || !method_exists($this, 'checkPermission') || !$this->checkPermission($AuthUser->id, 'PROFILE.UPDATE')) {
            $this->failed(null, 'Unauthorized or permission denied', 403);
            return;
        }
        $data = Flight::request()->data->getData();
        $user = User::get($AuthUser->id);
        if (!$user) {
            $this->failed(null, 'User not found', 404);
            return;
        }
        if (empty($data['image'])) {
            $this->failed(null, "Field 'image' is required", 400);
            return;
        }

        $user->profile_image = $data['image'];
        $user = User::update($user);

        $this->success([$user], 'User updated', 200);
    }
}
