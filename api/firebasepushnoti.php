<?php

function getAccessToken($serviceAccPath) {
    $jsonKey = json_decode(file_get_contents($serviceAccPath), true);

    $header = [
        'alg' => 'RS256',
        'typ' => 'JWT'
    ];

    $now = time();
    $expires = $now + 3600;

    $claims = [
        'iss' => $jsonKey['client_email'],
        'scope' => 'https://www.googleapis.com/auth/cloud-platform',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $expires
    ];

    $jwtHeader = base64UrlEncode(json_encode($header));
    $jwtClaims = base64UrlEncode(json_encode($claims));
    $signatureInput = $jwtHeader . '.' . $jwtClaims;

    openssl_sign($signatureInput, $signature, $jsonKey['private_key'], 'sha256');

    $jwtSignature = base64UrlEncode($signature);
    $jwt = $jwtHeader . '.' . $jwtClaims . '.' . $jwtSignature;

    $postData = [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    if (isset($responseData['access_token'])) {
        return $responseData['access_token'];
    } else {
        throw new Exception('Error fetching access token: ' . json_encode($responseData));
    }
}

function sendFCMNotification($accessToken) {
    $url = 'https://fcm.googleapis.com/v1/projects/pushnotification-43ae5/messages:send';

    $body = json_encode([
        "message" => [
            "topic" => "news",
            "notification" => [
                "title" => "Notification Title",
                "body" => "Notification body ...",
            ],
            "android" => [
                "priority" => "high"
            ],
            "data" => [
                "story_id" => "story_12345"
            ]
        ]
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        throw new Exception('Error sending FCM notification: ' . curl_error($ch));
    }

    echo 'Response: ' . $response;
}

function shouldSendNotification($currentDateTime, $scheduledDateTime) {
    $dateTimeScheduled = new DateTime($scheduledDateTime);
    $dateTimeCurrent = new DateTime($currentDateTime);

    $dateTimeScheduled->modify('-1 hour');
    return $dateTimeCurrent >= $dateTimeScheduled && $dateTimeCurrent < (new DateTime($scheduledDateTime));
}

$serviceAccPath = './pushnotification-43ae5-firebase-adminsdk-4bipc-d49d4dab18.json';

$scheduledDateTime = '2024-06-15 10:00:00';

$currentDateTime = date('Y-m-d H:i:s');

if (shouldSendNotification($currentDateTime, $scheduledDateTime)) {
    try {
        $accessToken = getAccessToken($serviceAccPath);
        sendFCMNotification($accessToken);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

function base64UrlEncode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}
?>
