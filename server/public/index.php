<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\MessageController;
use App\Controllers\UserController;
use App\Controllers\RoomController;
use App\Controllers\FilterController;
use App\Helpers\AuthHelper;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

if (!isset($_ENV['DB_HOST']) && file_exists(__DIR__ . '/../env.example')) {
    Dotenv::createImmutable(__DIR__ . '/../', 'env.example')->load();
}



header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

class Router {
    private array $routes = [];

    public function add(string $method, string $pattern, callable $callback): void {
        $this->routes[] = compact('method', 'pattern', 'callback');
    }

    public function dispatch(string $method, string $uri): void {
        foreach ($this->routes as $route) {
            $pattern = "@^" . preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $route['pattern']) . "$@D";
            if (strtoupper($method) === strtoupper($route['method']) && preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                call_user_func_array($route['callback'], $params);
                return;
            }
        }
        http_response_code(404);
        echo json_encode(["error" => "Route not found"]);
    }
}

$router = new Router();

// Root route - API info
$router->add('GET', '/', function() {
    echo json_encode([
        "message" => "Chat API is running",
        "version" => "1.0",
        "endpoints" => [
            "POST /auth/register" => "Register new user",
            "POST /auth/verify" => "Verify user",
            "GET /rooms" => "List all rooms",
            "POST /rooms" => "Create new room",
            "GET /users" => "List all users",
            "POST /api/chat/messages" => "Send message",
            "GET /api/chat/{room_id}/messages" => "Get room messages"
        ]
    ]);
});

// AUTH
$router->add('POST', '/auth/register', function() {
    (new AuthController())->registerUser();
});
$router->add('POST', '/auth/verify', function() {
    (new AuthController())->verifyUser();
});

// ROOMS
$router->add('GET', '/api/chat/{room_id}/messages', function($room_id) {
    $auth_user = AuthHelper::getAuthenticatedUser();
    if (!$auth_user) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    (new MessageController())->getMessages($room_id);
});
$router->add('POST', '/rooms', function() {
    (new RoomController())->createRoom();
});
$router->add('GET', '/rooms', function() {
    (new RoomController())->listRooms();
});

// MESSAGES
$router->add('POST', '/api/chat/messages', function() {
    $auth_user = AuthHelper::getAuthenticatedUser();
    if (!$auth_user) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    (new MessageController())->saveMessage($auth_user);
});
$router->add('POST', '/api/chat/messages/{parent_message_id}/reply', function($parent_message_id) {
    $auth_user = AuthHelper::getAuthenticatedUser();
    if (!$auth_user) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $data['parent_message_id'] = $parent_message_id;
    (new MessageController())->saveMessage($auth_user);
});
$router->add('GET', '/api/chat/messages/{message_id}', function($message_id) {
    $auth_user = AuthHelper::getAuthenticatedUser();
    if (!$auth_user) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    $message = \App\Models\Message::find($message_id);
    if (!$message) {
        http_response_code(404);
        echo json_encode(["error" => "Message not found"]);
        return;
    }
    echo json_encode($message);
});
$router->add('GET', '/api/chat/{user_id}/messages', function($user_id) {
    $auth_user = AuthHelper::getAuthenticatedUser();
    if (!$auth_user) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    (new MessageController())->getMessages(null, $user_id);
});

// USERS
$router->add('GET', '/api/users/{user_id}/status', function($user_id) {
    (new UserController())->getUserStatus($user_id);
});
$router->add('GET', '/users', function() {
    echo json_encode(\App\Models\User::getAllUsers());
});
$router->add('POST', '/api/users/{user_id}/mute', function($user_id) {
    (new \App\Controllers\UserController())->muteUser($user_id);
});
$router->add('POST', '/api/chat/{user_id}/ban', function($user_id) {
    (new \App\Controllers\UserController())->banUser($user_id);
});
$router->add('POST', '/api/chat/{user_id}/unban', function($user_id) {
    (new \App\Controllers\UserController())->unbanUser($user_id);
});

// AUDIT LOGS
$router->add('GET', '/api/chat/audit/logs', function() {
    echo json_encode(\App\Models\AuditLog::getAll());
});
$router->add('POST', '/api/chat/messages/{message_id}/report', function($message_id) {
    $auth_user = AuthHelper::getAuthenticatedUser();
    if (!$auth_user) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    (new MessageController())->reportMessage($message_id, $auth_user);
});

$router->add('POST', '/api/chat/filter', function() {
    $auth_user = AuthHelper::getAuthenticatedUser();
    if (!$auth_user) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }
    (new FilterController())->filterMessage();
});
try {
    $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $method = $_SERVER['REQUEST_METHOD'];
    $router->dispatch($method, $uri);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server Error", "message" => $e->getMessage()]);
}
