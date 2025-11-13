<?php
namespace App\Helpers;

use PDO;

class AuthHelper {

    private static function getDB() {
        return Database::getConnection();
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

    $pdo = self::getDB();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE api_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

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
