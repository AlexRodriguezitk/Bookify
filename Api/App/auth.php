<?php
namespace App;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class Auth {
    public static function generateToken($userId, $role) {
        $payload = [
            'iat' => time(),
            'exp' => time() + $_ENV['JWT_EXPIRATION'],
            'sub' => $userId,
            'role' => $role
        ];
        return JWT::encode($payload, $_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']);
    }

    public static function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']));
            $decoded->id = $decoded->sub;
            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function requireRole($role) {
        $user = \Flight::get('user');
        if (!$user || $user->role !== $role) {
            \Flight::halt(403, json_encode(['error' => 'Acceso denegado']));
        }
    }

    public static function requireAuth() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            \Flight::halt(401, json_encode(['error' => 'Token no proporcionado']));
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $user = self::validateToken($token);

        if (!$user) {
            \Flight::halt(401, json_encode(['error' => 'Token inválido']));
        }

        \Flight::set('user', $user);
    }
}