<?php

namespace App\Controllers;

use App\Traits\ApiResponse;

use PDO;
use PDOException;
use Exception;

use Flight;

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

    public function install()
    {
        try {
            // Obtener datos desde la petición
            $data = Flight::request()->data->getData();

            // Definir valores con fallback
            $dbHost = $data['DB_HOST'] ?? null;
            $dbName = $data['DB_NAME'] ?? 'bookify';
            $dbUser = $data['DB_USER'] ?? null;
            $dbPassword = $data['DB_PASSWORD'] ?? ''; // Ahora puede estar vacío
            $jwtSecret = $data['JWT_SECRET'] ?? 'GERALDINE';
            $jwtAlgorithm = $data['JWT_ALGORITHM'] ?? 'HS256';
            $jwtExpiration = $data['JWT_EXPIRATION'] ?? 3600;

                
            if (empty($data)) {
                $this->failed(null, "No data provided", 400);
                return;
            }
            $requiredFields = ['DB_HOST', 'DB_USER'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $this->failed(null, "Field '$field' is required", 400);
                    return;
                }
            }

            // Obtener BASE_URL dinámicamente
            $request = Flight::request();
            $scheme = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST']; // Obtiene el dominio con el puerto si es necesario
            $basePath = $request->base; // Obtiene el subdirectorio donde está Flight

            $baseUrl = "{$scheme}://{$host}{$basePath}";

            // Ruta del archivo .env
            $envPath = __DIR__ . '/../../.env';

            // Si el archivo .env no existe, crearlo
            if (!file_exists($envPath)) {
                file_put_contents($envPath, ""); // Crear archivo vacío
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
                // Crear la base de datos si no existe
                $pdo->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                // Leer y ejecutar el script de instalación
                $sqlFile = __DIR__ . "/../database/DB.sql";
                if (!file_exists($sqlFile)) {
                    return $this->failed(null, 'Archivo DB.SQL no encontrado.', 500);
                }

                // Conectar a la nueva base de datos
                $pdo->exec("USE $dbName;");
                // Leer el contenido del SQL y reemplazar el nombre de la base de datos
                $sql = file_get_contents($sqlFile);
                $sql = str_replace('{DB_NAME}', $dbName, $sql); // Reemplaza marcador de la base de datos
                $pdo->exec($sql);
            }

            // Formatear el contenido del .env
            $envContent = "
# ==========================================
# CONFIGURACIÓN DE LA BASE DE DATOS
# ==========================================
DB_HOST=$dbHost        # Servidor de la base de datos
DB_NAME=$dbName        # Nombre de la base de datos
DB_USER=$dbUser        # Usuario de la base de datos
DB_PASSWORD=$dbPassword # Contraseña de la base de datos

# ==========================================
# CONFIGURACIÓN JWT (JSON Web Token)
# ==========================================
JWT_SECRET=$jwtSecret      # Clave secreta para JWT
JWT_ALGORITHM=$jwtAlgorithm # Algoritmo de encriptación (HS256 recomendado)
JWT_EXPIRATION=$jwtExpiration # Tiempo de expiración del token en segundos

# ==========================================
# OTRAS CONFIGURACIONES
# ==========================================
BASE_URL=$baseUrl    # URL base de la aplicación
IS_INSTALLED=TRUE

# ==========================================
# METADATOS DE INSTALACIÓN
# ==========================================
# Fecha de instalación: $date
# Instalado por: Alex Rodriguez | 2025
";

            file_put_contents($envPath, trim($envContent));

            return $this->success([null], 'Instalación completada con éxito.');
        } catch (Exception $e) {
            return $this->failed(['error' => $e->getMessage()], 'Error en la instalación.', 500);
        }
    }
}
