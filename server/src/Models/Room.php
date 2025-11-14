<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;

class Room {
    public static function createRoom(string $name, int $created_by): array {
        $db = Database::getConnection();

        $stmt = $db->prepare("INSERT INTO rooms (name, created_by, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$name, $created_by]);

        $room_id = $db->lastInsertId();

        return [
            "status" => "success",
            "room_id" => $room_id,
            "name" => $name,
            "created_by" => $created_by
        ];
    }

    public static function getAllRooms(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM rooms ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
