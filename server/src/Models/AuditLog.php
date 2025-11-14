<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;

class AuditLog {

    // Get PDO connection
    private static function getDB(): PDO {
        return Database::getConnection();
    }

    // Create a new audit log entry
    public static function create(string $action, int $user_id, string $details): void {
        $pdo = self::getDB();
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (action, user_id, details, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$action, $user_id, $details]);
    }

    // Get all audit logs
    public static function getAll(): array {
        $pdo = self::getDB();
        $stmt = $pdo->query("SELECT * FROM audit_logs ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
