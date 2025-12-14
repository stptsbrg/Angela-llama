<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');

$input = json_decode(file_get_contents('php://input'), true);
$messages = $input['messages'] ?? [];

$url = "https://api.groq.com/openai/v1/chat/completions";
$apiKey = getenv('GROQ_API_KEY');

$payload = [
    "model" => "llama3-8b-8192",
    "stream" => true,
    "messages" => $messages
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_WRITEFUNCTION => function ($curl, $data) {
        echo $data;
        @ob_flush();
        flush();
        return strlen($data);
    }
]);

curl_exec($ch);
curl_close($ch);
