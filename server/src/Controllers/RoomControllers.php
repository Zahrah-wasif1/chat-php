<?php
namespace App\Controllers;

use App\Models\Room;

class RoomController {
    public function createRoom() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name']) || empty($data['created_by'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing fields: 'name' and 'created_by' required."]);
            return;
        }

        $result = Room::createRoom($data['name'], (int)$data['created_by']);
        echo json_encode($result);
    }

    public function listRooms() {
        echo json_encode(Room::getAllRooms());
    }
}

