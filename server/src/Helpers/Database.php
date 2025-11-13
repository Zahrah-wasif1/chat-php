<?php
namespace App\Helpers;

use PDO;

class Database {
    private static ?PDO $connection = null;

    public static function getConnection(): PDO {
        if (self::$connection === null) {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $db   = $_ENV['DB_DATABASE'] ?? ($_ENV['DB_NAME'] ?? null);
            $user = $_ENV['DB_USERNAME'] ?? ($_ENV['DB_USER'] ?? null);
            $pass = $_ENV['DB_PASSWORD'] ?? ($_ENV['DB_PASS'] ?? '');
            $port = $_ENV['DB_PORT'] ?? null;

            if ($db === null || $user === null) {
                throw new \RuntimeException('Database configuration is missing. Please check your environment variables.');
            }

            $dsn  = "mysql:host=$host" . ($port ? ";port=$port" : "") . ";dbname=$db;charset=utf8mb4";

            self::$connection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }
        return self::$connection;
    }
    
}
