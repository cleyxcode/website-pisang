<?php

// Buat file debug_wa.php di root project untuk test
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function testWhatsAppAPI()
{
    $apiUrl = 'https://api.fonnte.com/send';
    $token = $_ENV['FONNTE_TOKEN'] ?? 'VP2327ntjWo58rZyvMmb';
    $adminPhone = $_ENV['FONNTE_ADMIN_PHONE'] ?? '082198579298';
    
    echo "Testing WhatsApp API...\n";
    echo "Token: " . substr($token, 0, 10) . "...\n";
    echo "Admin Phone: " . $adminPhone . "\n";
    echo "API URL: " . $apiUrl . "\n\n";
    
    $data = [
        'target' => $adminPhone,
        'message' => 'Test pesan dari sistem pada ' . date('Y-m-d H:i:s'),
        'countryCode' => '62'
    ];
    
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => [
            'Authorization: ' . $token
        ],
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    curl_close($curl);
    
    echo "HTTP Code: " . $httpCode . "\n";
    
    if ($error) {
        echo "Curl Error: " . $error . "\n";
    } else {
        echo "Response: " . $response . "\n";
    }
    
    $responseData = json_decode($response, true);
    
    if ($httpCode === 200 && isset($responseData['status']) && $responseData['status'] === true) {
        echo "\n✅ Test BERHASIL - Pesan terkirim!\n";
    } else {
        echo "\n❌ Test GAGAL\n";
        if (isset($responseData['reason'])) {
            echo "Reason: " . $responseData['reason'] . "\n";
        }
    }
}

testWhatsAppAPI();