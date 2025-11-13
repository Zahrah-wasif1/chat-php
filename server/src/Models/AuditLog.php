<?php
namespace App\Models;

use PDO;

class AuditLog {
    private static function getDB() {
        $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'];
        return new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
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
