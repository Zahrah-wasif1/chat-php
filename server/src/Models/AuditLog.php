<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;

class AuditLog {
    private static function getDB(): PDO {
        return Database::getConnection();
    }

    public static function create($action, $user_id, $details) {
        $pdo = self::getDB();
        $stmt = $pdo->prepare("INSERT INTO audit_logs (action, user_id, details) VALUES (?, ?, ?)");
        $stmt->execute([$action, $user_id, $details]);
    }

    public static function getAll() {
        $pdo = self::getDB();
        $stmt = $pdo->query("SELECT * FROM audit_logs ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
