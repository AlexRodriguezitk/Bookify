<?php

namespace App\Controllers;

use App\Traits\ApiResponse;
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

    //Status() return the status of the installation
    public function status()
    {
        //update or create htaacces file
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
        try {
            // Obtener datos desde la petición
            $data = Flight::request()->data->getData();

            // Definir valores con fallback
            $dbHost = $data['DB_HOST'] ?? null;
            $dbName = $data['DB_NAME'] ?? 'bookify';
            $dbUser = $data['DB_USER'] ?? null;
            $dbPassword = $data['DB_PASSWORD'] ?? ''; // Puede estar vacío
            $jwtSecret = $data['JWT_SECRET'] ?? bin2hex(random_bytes(32));
            $jwtAlgorithm = $data['JWT_ALGORITHM'] ?? 'HS256';
            $jwtExpiration = $data['JWT_EXPIRATION'] ?? 3600;

            if (empty($data)) {
                return $this->failed(null, "No data provided", 400);
            }
            $requiredFields = ['DB_HOST', 'DB_USER'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return $this->failed(null, "Field '$field' is required", 400);
                }
            }

            // Obtener BASE_URL dinámicamente
            $request = Flight::request();
            $scheme = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $basePath = $request->base;

            $baseUrl = "{$scheme}://{$host}{$basePath}";

            // Ruta del archivo .env
            $envPath = __DIR__ . '/../../.env';

            if (!file_exists($envPath)) {
                file_put_contents($envPath, "");
            }

            // Obtener la fecha y hora actual
            $date = date('Y-m-d H:i:s');

            // Conectar al servidor MySQL (sin especificar la base de datos aún)
            $dsn = "mysql:host=$dbHost;";
            $pdo = new PDO($dsn, $dbUser, $dbPassword, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Verificar si la base de datos ya existe
            $stmt = $pdo->query("SHOW DATABASES LIKE '$dbName'");
            $dbExists = $stmt->fetch();

            if (!$dbExists) {
                $pdo->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

                // Leer y ejecutar el script de instalación
                $sqlFile = __DIR__ . "/../database/DB.sql";
                if (!file_exists($sqlFile)) {
                    return $this->failed(null, 'Archivo DB.SQL no encontrado.', 500);
                }

                $pdo->exec("USE $dbName;");
                $sql = file_get_contents($sqlFile);
                $sql = str_replace('{DB_NAME}', $dbName, $sql);
                $pdo->exec($sql);
            }

            // Configurar y escribir el .htaccess
            $this->setHtaccess();

            // Formatear el contenido del .env
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
            return $this->failed(['error' => $e->getMessage()], 'Error en la instalación.', 500);
        }
    }

    /**
     * Crea o actualiza el archivo .htaccess con la configuración adecuada.
     */
    public function setHtaccess()
    {
        $htaccessPath = __DIR__ . '/../../.htaccess';

        // Obtener la ruta base del proyecto dinámicamente
        $baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $baseDir = strtolower(rtrim($baseDir, '/') . '/');

        // Contenido esperado del .htaccess
        $htaccessContent = <<<HTACCESS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase {$baseDir}
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
HTACCESS;

        // Verificar si es necesario actualizar el archivo
        if (!file_exists($htaccessPath) || strpos(file_get_contents($htaccessPath), "RewriteBase {$baseDir}") === false) {
            file_put_contents($htaccessPath, $htaccessContent);
        }
    }
}
