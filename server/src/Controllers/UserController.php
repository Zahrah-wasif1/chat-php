<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Models\User;
use PDO;

class UserController {

    public function getUserStatus($user_id) {
        header('Content-Type: application/json');
        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT id, name, email, status, is_online, last_seen 
            FROM users 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $user_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }

        echo json_encode($data);
    }

    public function getMessages($room_id = null, $user_id = null) {
    $messages = \App\Models\Message::getMessages($room_id, $user_id);

    foreach ($messages as &$msg) {
        // Fetch the sender info from DB
        $user = null;
        if (!empty($msg['sender_id'])) {
            $user = \App\Models\User::find($msg['sender_id']);
        }

        if ($user) {
            // Always attach user details, even if banned
            $msg['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'status' => $user['status'] // active / muted / banned
            ];

            // Modify content only if muted/banned
            if ($user['status'] === 'banned') {
                $msg['content'] = '[User is banned]';
            } elseif ($user['status'] === 'muted') {
                $msg['content'] .= ' [Muted]';
            }
        } else {
            // Fallback if user not found
            $msg['user'] = [
                'id' => $msg['sender_id'],
                'name' => 'Unknown',
                'email' => null,
                'status' => 'unknown'
            ];
        }

        // Keep track of replies
        $msg['reply_to'] = $msg['parent_message_id'] ?? null;
    }

    echo json_encode($messages);
}
    public function muteUser($user_id) {
        $userId = (int)$user_id;
        User::updateUserStatus($userId, 'muted');
        echo json_encode(['success' => true]);
    }

    public function banUser($user_id)
{
    header('Content-Type: application/json');
    $userId = (int)$user_id;

    if (User::updateUserStatus($userId, 'banned')) {
        $user = User::find($userId);

        if ($user) {
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'status' => $user['status'],
                    'is_online' => $user['is_online'],
                    'last_seen' => $user['last_seen']
                ]
            ]);
        } else {
            echo json_encode(['success' => true, 'message' => 'User banned but details not found']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Failed to ban user']);
    }
}

    public function unbanUser($user_id) {
         header('Content-Type: application/json');
    $userId = (int)$user_id;

    if (User::updateUserStatus($userId, 'unbanned')) {
        $user = User::find($userId);

        if ($user) {
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'status' => $user['status'],
                    'is_online' => $user['is_online'],
                    'last_seen' => $user['last_seen']
                ]
            ]);
        } else {
            echo json_encode(['success' => true, 'message' => 'User unbanned but details not found']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Failed to unban user']);
    }
    }
}
