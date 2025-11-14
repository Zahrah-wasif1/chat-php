<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;

class Message {

    // Get PDO connection
    private static function getDB(): PDO {
        return Database::getConnection();
    }

    // Get all messages with optional filters
    public static function getMessages($room_id = null, $user_id = null)
    {
        $db = self::getDB();
        $query = "SELECT * FROM messages WHERE 1=1";
        $params = [];

        if ($room_id) {
            $query .= " AND room_id = :room_id";
            $params[':room_id'] = $room_id;
        }

        if ($user_id) {
            $query .= " AND sender_id = :user_id";
            $params[':user_id'] = $user_id;
        }

        $query .= " ORDER BY created_at ASC";
        $stmt = $db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get messages by room_id
    public static function getMessagesByRoom($room_id) {
        $pdo = self::getDB();
        $stmt = $pdo->prepare("SELECT * FROM messages WHERE room_id = ? ORDER BY created_at ASC");
        $stmt->execute([$room_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get messages by user_id
    public static function getMessagesByUser($user_id) {
        $pdo = self::getDB();
        $stmt = $pdo->prepare("SELECT * FROM messages WHERE sender_id = ? ORDER BY created_at ASC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find a single message by ID
    public static function find($id) {
        $pdo = self::getDB();
        $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new message or reply
    public static function create($data) {
        $pdo = self::getDB();

        $stmt = $pdo->prepare("
            INSERT INTO messages (room_id, sender_id, content, parent_message_id, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->execute([
            $data['room_id'],
            $data['sender_id'],
            $data['content'],
            $data['parent_message_id'] ?? null
        ]);

        $data['id'] = $pdo->lastInsertId();
        $data['reply_to'] = $data['parent_message_id'] ?? null;

        return $data;
    }
}
