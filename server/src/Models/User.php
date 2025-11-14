<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;

class User {
    private static function getDB(): PDO {
        return Database::getConnection();
    }

    public static function create($name, $email, $password) {
        $pdo = self::getDB();
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);

        return self::find((int)$pdo->lastInsertId());
    }

    public static function findByEmail($email) {
        $pdo = self::getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function verifyToken($token) {
        $pdo = self::getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE session_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function find($userId)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id, name, email, status, is_online, last_seen 
            FROM users 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAllUsers(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }

    public static function updateUserStatus($userId, $status)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE users 
            SET status = :status 
            WHERE id = :id
        ");
        return $stmt->execute([':status' => $status, ':id' => $userId]);
    }
}




