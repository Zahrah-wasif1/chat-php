<?php
namespace App\Controllers;

class FilterController {

    // Filters bad words from a message
    public function filterMessage() {
        $data = json_decode(file_get_contents('php://input'), true);
        $content = $data['content'] ?? '';

        if (empty($content)) {
            echo json_encode(["error" => "Missing message content."]);
            return;
        }

        // List of words to censor
        $badWords = ['fuck', 'shit', 'idiot', 'stupid', 'bitch'];

        // Build regex pattern
        $pattern = '/\b(' . implode('|', array_map('preg_quote', $badWords)) . ')\b/i';

        // Replace bad words with asterisks
        $filtered = preg_replace_callback($pattern, function ($matches) {
            return str_repeat('*', strlen($matches[1]));
        }, $content);

        // Return JSON response
        echo json_encode([
            "original" => $content,
            "filtered" => $filtered
        ]);
    }
}
