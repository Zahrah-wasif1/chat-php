<?php
namespace App\Helpers;

use App\Models\User;
use PDO;

class AuthHelper {

    private static function getDB() {
        $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'];
        return new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

   public static function getAuthenticatedUser() {
    $headers = getallheaders();
    if (empty($headers['Authorization'])) {
        return null;
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    if (empty($token)) {
        return null;
    }

    $pdo = new \PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->prepare("SELECT * FROM users WHERE api_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$user || $user['is_banned']) {
        return null;
    }

    return $user;
}

    // Optional: force authentication and exit if not valid
    public static function authenticate() {
        $user = self::getAuthenticatedUser();
        if (!$user) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized"]);
            exit;
        }
        return $user;
    }
}
