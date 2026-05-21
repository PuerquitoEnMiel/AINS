<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$apiKey = $_ENV['GEMINI_API_KEY'] ?? null;
// Also try from config
if (!$apiKey) {
    $kernel->handle(Illuminate\Http\Request::capture());
    $apiKey = env('GEMINI_API_KEY');
}

$ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'contents' => [
        ['role' => 'user', 'parts' => [['text' => 'Di hola en espanol']]]
    ]
]));

$result = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $status\n";
$data = json_decode($result, true);
if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    echo "Reply: " . $data['candidates'][0]['content']['parts'][0]['text'] . "\n";
} else {
    echo "Error or unexpected response:\n";
    echo $result . "\n";
}
