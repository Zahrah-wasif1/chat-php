<?php
namespace App\Controllers;

class FilterController {
    public function filterMessage() {
        $data = json_decode(file_get_contents('php://input'), true);
        $content = $data['content'] ?? '';

        if (empty($content)) {
            echo json_encode(["error" => "Missing message content."]);
            return;
        }

        $badWords = ['fuck', 'shit', 'idiot', 'stupid', 'bitch'];
        $pattern = '/\b(' . implode('|', array_map('preg_quote', $badWords)) . ')\b/i';
        $filtered = preg_replace_callback($pattern, function ($matches) {
            return str_repeat('*', strlen($matches[1]));
        }, $content);

        echo json_encode([
            "original" => $content,
            "filtered" => $filtered
        ]);
    }
}
