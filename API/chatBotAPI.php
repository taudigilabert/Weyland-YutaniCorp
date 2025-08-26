<?php
// archivo: API/chatbotAPI.php

$apiKey = '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';

$input = json_decode(file_get_contents('php://input'), true);
$question = $input['question'] ?? '';

if (!$question) {
    echo json_encode(['error' => 'No hay pregunta']);
    exit;
}

$data = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "system", "content" => "Eres la IA MU-TH-UR 6000 de la nave USCSS Paladio. Responde de forma profesional y futurista."],
        ["role" => "user", "content" => $question]
    ],
    "temperature" => 0.7,
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

// Verificar si hay errores en la solicitud CURL
if (curl_errno($ch)) {
    echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    exit;
}

// Cerrar la sesión CURL
curl_close($ch);

// Decodificar la respuesta JSON
$result = json_decode($response, true);

// Verificar si el resultado contiene las respuestas esperadas
if (!isset($result['choices']) || empty($result['choices'])) {
    echo json_encode(['error' => 'Respuesta inesperada o vacía: ' . $response]);
    exit;
}

// Obtener la respuesta y devolverla
echo json_encode([
    'answer' => $result['choices'][0]['message']['content'] ?? 'No se pudo obtener una respuesta en este momento.',
]);
?>
