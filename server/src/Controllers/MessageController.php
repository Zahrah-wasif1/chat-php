<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Models\Message;
use App\Models\User;
use App\Models\AuditLog;

class MessageController {

    // Get messages with optional filtering by room or user
    public function getMessages($room_id = null, $user_id = null) {
        $messages = Message::getMessages($room_id, $user_id);

        foreach ($messages as &$msg) {
            $user = !empty($msg['sender_id']) ? User::find($msg['sender_id']) : null;

            if ($user) {
                $msg['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'status' => $user['status'] // active / muted / banned
                ];

                // Modify content for muted or banned users
                if ($user['status'] === 'banned') {
                    $msg['content'] = '[User is banned]';
                } elseif ($user['status'] === 'muted') {
                    $msg['content'] .= ' [Muted]';
                }
            } else {
                $msg['user'] = [
                    'id' => $msg['sender_id'],
                    'name' => 'Unknown',
                    'email' => null,
                    'status' => 'unknown'
                ];
            }

            $msg['reply_to'] = $msg['parent_message_id'] ?? null;
        }

        echo json_encode($messages);
    }

    // Save a new message
    public function saveMessage($auth_user) {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['room_id']) || empty($data['content'])) {
            echo json_encode(["error" => "Missing fields."]);
            return;
        }

        $user = User::find($auth_user['id']);
        if ($user['status'] === 'banned') {
            echo json_encode(["error" => "Banned users cannot send messages."]);
            return;
        }

        $data['sender_id'] = $auth_user['id'];

        // Censor bad words
        $badWords = ['ass','asshole','bastard','bitch','bollocks','brotherfucker','bullshit',
            'child-fucker','cocksucker','crap','cunt','damn','dick','dickhead',
            'dyke','fag','fuck','fucker','fucking','hell','idiot','jackass',
            'motherfucker','nigger','piss','prick','pussy','shit','shithead',
            'slut','twat','wanker'];

        $pattern = '/\b(' . implode('|', array_map('preg_quote', $badWords)) . ')\b/i';
        $data['content'] = preg_replace_callback($pattern, function ($matches) {
            return str_repeat('*', strlen($matches[1]));
        }, $data['content']);

        $message = Message::create($data);

        echo json_encode([
            "status" => "success",
            "id" => $message['id'],
            "room_id" => $data['room_id'],
            "sender_id" => $data['sender_id'],
            "content" => $data['content']
        ]);
    }

    // Report a message
    public function reportMessage($message_id, $auth_user) {
        $data = json_decode(file_get_contents('php://input'), true);
        $reason = $data['reason'] ?? 'No reason provided';

        $message = Message::find($message_id);
        if (!$message) {
            http_response_code(404);
            echo json_encode(["error" => "Message not found."]);
            return;
        }

        AuditLog::create(
            'message_reported',
            $auth_user['id'],
            json_encode([
                'message_id' => $message_id,
                'reason' => $reason
            ])
        );

        $reporter_info = [
            'id' => $auth_user['id'],
            'name' => $auth_user['name'] ?? 'Unknown',
            'email' => $auth_user['email'] ?? null
        ];

        echo json_encode([
            "status" => "success",
            "message" => "Report submitted successfully.",
            "reported_by" => $reporter_info
        ]);
    }
}
