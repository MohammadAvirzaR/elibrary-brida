<?php

// Test Login API
$url = 'http://127.0.0.1:8000/api/login';

$data = [
    'email' => 'admin@brida.com',
    'password' => 'admin123'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

echo "Testing Login API...\n";
echo "URL: $url\n";
echo "Email: admin@brida.com\n";
echo "Password: admin123\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo $response . "\n";

curl_close($ch);
