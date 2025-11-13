<?php
namespace App\Controllers;

use App\Models\User;
use App\Helpers\Database;

class AuthController {

    public function registerUser() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['name']) || empty($input['email']) || empty($input['password'])) {
            echo json_encode(["error" => "Missing fields"]);
            return;
        }

        if (User::findByEmail($input['email'])) {
            echo json_encode(["error" => "Email already registered"]);
            return;
        }

        $user = User::create($input['name'], $input['email'], $input['password']);

        // Generate API token
        $token = bin2hex(random_bytes(32));
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE users SET api_token = ? WHERE id = ?");
        $stmt->execute([$token, $user['id']]);

        echo json_encode([
            "status" => "success",
            "message" => "User registered successfully",
            "api_token" => $token,
            "user" => [
                "id" => $user['id'],
                "name" => $user['name'],
                "email" => $user['email']
            ]
        ]);
    }

    public function verifyUser() {
        $input = json_decode(file_get_contents('php://input'), true);
        $user = User::findByEmail($input['email']);

        if (!$user || !password_verify($input['password'], $user['password'])) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid credentials"]);
            return;
        }

        // Generate new API token
        $token = bin2hex(random_bytes(32));
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE users SET api_token = ? WHERE id = ?");
        $stmt->execute([$token, $user['id']]);

        echo json_encode([
            "api_token" => $token,
            "user" => [
                "id" => $user['id'],
                "name" => $user['name'],
                "email" => $user['email']
            ]
        ]);
    }
}
