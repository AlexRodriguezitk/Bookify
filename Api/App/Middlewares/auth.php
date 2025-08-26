<?php

namespace App\Middlewares;


use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\models\User;
use Exception;
use GrahamCampbell\ResultType\Success;

class Auth
{
    public static function generateToken($userId, $role)
    {
        $payload = [
            'iat' => time(),
            'exp' => time() + $_ENV['JWT_EXPIRATION'],
            'sub' => $userId,
            'role' => $role
        ];
        return JWT::encode($payload, $_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']);
    }

    public static function GenPassword()
    {
        $specials = '!@#$%';
        $pass = array(); //remember to declare $pass as an array
        $pass = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9), str_split($specials));
        shuffle($pass);
        $pass = array_slice($pass, 0, 12);
        return implode($pass);
    }
    public static function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']));
            $decoded->id = $decoded->sub;
            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function requireRole($role)
    {
        $user = \Flight::get('user');
        if (!$user || $user->role !== $role) {
            \Flight::halt(403, json_encode(['error' => 'Acceso denegado']));
        }
    }

    public static function requireAuth()
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            \Flight::halt(401, json_encode(['error' => 'Token no proporcionado']));
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $user = self::validateToken($token);

        if (!$user) {
            \Flight::halt(401, json_encode(['error' => 'Token inválido']));
        }

        $DB_User = User::Get($user->id);
        if (!$DB_User->is_active) {
            \Flight::halt(401, json_encode(['error' => 'Token inválido']));
        }
        \Flight::set('user', $user);
    }
}
