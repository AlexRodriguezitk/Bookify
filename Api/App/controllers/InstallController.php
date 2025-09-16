<?php

namespace App\Controllers;

use App\traits\ApiResponse;
use Flight;
use Exception;
use PDO;

class InstallController
{
    use ApiResponse;

    public function handle($error = null)
    {
        return $this->failed(
            ["DATABASE_ERROR", $error],
            'La base de datos no está configurada correctamente.',
            500
        );
    }

    public function status()
    {
        $this->setHtaccess();
        $envPath = __DIR__ . '/../../.env';
        if (!file_exists($envPath)) {
            return $this->success(['installed' => false], 'La aplicación no está instalada.');
        }

        $env = file_get_contents($envPath);
        $env = explode("\n", $env);

        $installed = false;
        foreach ($env as $line) {
            if (strpos($line, 'IS_INSTALLED=TRUE') !== false) {
                $installed = true;
                break;
            }
        }

        return $this->success(['installed' => $installed], 'La aplicación está instalada.');
    }

    public function install()
    {
        $dbCreated = false; // ✅ Para saber si se creó la DB
        try {
            $data = Flight::request()->data->getData();

            $dbHost = $data['DB_HOST'] ?? null;
            $dbName = $data['DB_NAME'] ?? 'bookify';
            $dbUser = $data['DB_USER'] ?? null;
            $dbPassword = $data['DB_PASSWORD'] ?? '';
            $jwtSecret = $data['JWT_SECRET'] ?? bin2hex(random_bytes(32));
            $jwtAlgorithm = $data['JWT_ALGORITHM'] ?? 'HS256';
            $jwtExpiration = $data['JWT_EXPIRATION'] ?? 3600;

            $adminName = $data['admin_name'] ?? null;
            $adminUsername = $data['admin_username'] ?? null;
            $adminPassword = $data['admin_password'] ?? null;
            $adminPhone = $data['admin_phone'] ?? null;

            if (empty($data)) {
                return $this->failed(null, "No data provided", 400);
            }
            $requiredFields = ['DB_HOST', 'DB_USER', 'admin_name', 'admin_username', 'admin_password', 'admin_phone'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return $this->failed(null, "Field '$field' is required", 400);
                }
            }

            $request = Flight::request();
            $scheme = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $basePath = $request->base;
            $baseUrl = "{$scheme}://{$host}{$basePath}";

            $envPath = __DIR__ . '/../../.env';
            if (!file_exists($envPath)) {
                file_put_contents($envPath, "");
            }

            $date = date('Y-m-d H:i:s');

            // ✅ Conexión al servidor (sin DB aún)
            $dsn = "mysql:host=$dbHost;";
            $pdo = new PDO($dsn, $dbUser, $dbPassword, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $stmt = $pdo->query("SHOW DATABASES LIKE '$dbName'");
            $dbExists = $stmt->fetch();

            if (!$dbExists) {
                // ✅ Crear DB
                $pdo->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                $dbCreated = true; // Marcar que se creó

                // Ejecutar script SQL
                $sqlFile = __DIR__ . "/../database/DEMO.sql";
                if (!file_exists($sqlFile)) {
                    throw new Exception('Archivo DB.SQL no encontrado.');
                }

                $pdo->exec("USE `$dbName`;");
                $sql = file_get_contents($sqlFile);
                $sql = str_replace('{DB_NAME}', $dbName, $sql);
                $pdo->exec($sql);
            } else {
                // ✅ Si ya existe, solo seleccionarla
                $pdo->exec("USE `$dbName`;");
                $dbCreated = true;
            }

            // // Insertar admin
            // $hashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT);
            // $query = "INSERT INTO users (name, username, password, phone, rol, is_active) 
            //       VALUES (:name, :username, :password, :phone, :rol, :is_active)";
            // $stmt = $pdo->prepare($query);
            // $stmt->bindValue(':name', $adminName);
            // $stmt->bindValue(':username', $adminUsername);
            // $stmt->bindValue(':password', $hashedPassword);
            // $stmt->bindValue(':phone', $adminPhone);
            // $stmt->bindValue(':rol', 1);
            // $stmt->bindValue(':is_active', 1);
            // $stmt->execute();

            // Crear htaccess
            $this->setHtaccess();

            // Guardar .env
            $envContent = "
# ==========================================
# CONFIGURACIÓN DE LA BASE DE DATOS
# ==========================================
DB_HOST=$dbHost
DB_NAME=$dbName
DB_USER=$dbUser
DB_PASSWORD=$dbPassword

# ==========================================
# CONFIGURACIÓN JWT
# ==========================================
JWT_SECRET=$jwtSecret
JWT_ALGORITHM=$jwtAlgorithm
JWT_EXPIRATION=$jwtExpiration

# ==========================================
# OTRAS CONFIGURACIONES
# ==========================================
BASE_URL=$baseUrl
IS_INSTALLED=TRUE

# ==========================================
# METADATOS DE INSTALACIÓN
# ==========================================
# Fecha de instalación: $date
";
            file_put_contents($envPath, trim($envContent));

            return $this->success([null], 'Instalación completada con éxito.');
        } catch (Exception $e) {
            // ✅ Si se creó la DB pero falló otra cosa → eliminarla
            if ($dbCreated && isset($pdo)) {
                try {
                    $pdo->exec("DROP DATABASE `$dbName`;");
                } catch (Exception $ex) {
                    // Silenciar si falla el drop
                }
            }
            return $this->failed(['error' => $e->getMessage()], 'Error en la instalación.', 500);
        }
    }

    public function setHtaccess()
    {
        $htaccessPath = __DIR__ . '/../../.htaccess';
        $baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $baseDir = strtolower(rtrim($baseDir, '/') . '/');

        $htaccessContent = <<<HTACCESS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase {$baseDir}
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
HTACCESS;

        if (!file_exists($htaccessPath) || strpos(file_get_contents($htaccessPath), "RewriteBase {$baseDir}") === false) {
            file_put_contents($htaccessPath, $htaccessContent);
        }
    }
}
