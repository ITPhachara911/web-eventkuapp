<?php

function getAccessToken($jsonKey) {
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

// JSON key data directly in PHP array
$jsonKey = [
    "type" => "service_account",
    "project_id" => "pushnotification-43ae5",
    "private_key_id" => "d49d4dab18ac48d2eec36036beb74191c625fed0",
    "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCqrT/mf8fCY0pV\nd55axSbZQr7mVa9C+K3GU3/YTBHdLDf18Ll6LkO+UQehFImCCAgiG4tqa1QNDTWf\nqpzzdsJ76ln0PQs9ba5KH3lu65FGm3E65NfuL8AUbV/sjfcHI7GFjLSH/HKIuSln\nYzSMaOkFGn0iNyU93FCZnpAoY9ir+ASGZ1u9WScgNYxvorfQYfK26fYQ/fx+lDtt\nfzU0006NM+CjLnO3183smEIxGmgGW4/zc8S+zNhAt1sqb53T1s8E5x9aYGL4EtMc\nq5T+ryDaVTLPpbuZgQlz77YtiGNXEoxvtt95yI2r5WuHyz/HUpEYt0HGWQCKxBvw\nkw7FgDWpAgMBAAECggEADtyVYfoqMCVib4ECGVQNuxEWa5rWLyIVip2SdiiAjTZY\nnJokomyVIkUuwYJW62tM3wBwQjjciaLm8pNiBYqnqm/Ye52VpRiP+r0VHzkUrjXi\nlQ7gdQl0I6s29TOS6Syc3HzJKwaqx/g8kFe5d4j6kmLRNtmfrOxqWcgOdC/mienD\nNesoKvM496wqGwdpMiGS5Iz9x1NbfL5N16yRPvifgh6Y1cTEU5cziW8+NIaRG358\nmUHPvDef278I5ZIVnnChuGpVcUBOAufxn+glz2l8Fwu0TA4aosPgPYwr05VlbN49\n0tim8OhOqHynaIfksd3MOhBNpyW9CGe029UIKQeq8QKBgQDacAwNzJhLYApxaulR\n/RVwibUT8PSd0L1v3blEz0TzYjf7JDqqQhZ98eLCOqskBWSRavUR6qwFeDS8dyz9\nixVd4lVgFa9ffkoRlvW9G0WXD8BUYShdtZlrWDraSySvDBDNODq7S8pOI3Um8q6F\nhK/nMNFKMoo0kIFw8fZSH0BFGQKBgQDIBrH4alcpD4OyjB3ygUEfyX+3uURi2iXV\nuqFArYSCn2tagLbOeVCM/RdBSsx6opSZwVHmgwcCMetmtFZ2bbhpiOSTmUleiEgZ\n7ngOOPViUCOuvT8CGd74dzEFR2Bg7OZLjsiqWooTBLzTkN1xNfIoRCm9tcK1ya1o\nC3EJsaV3EQKBgB6rsOWXHqMmvxChxFUAxivhCg3cvVwTXSYB6euhdrr9xYJ72cji\nMqpIdmBzQGh0YWSRsgtr+e9iq3Ty/twy1TMzfm1ZXiB4aQoDOkntNF47lfPDGJnf\nz8TkxI62ElaJySonhQebYrKKA/8OADc7JD+/+QMECafLyoEDWGS7gpixAoGBAJcV\n54u51wgevd05VM19sBEwhBXkDLGWEQn1JCPUbMi1XcNIgcxHef5klRIuS3E+KHxS\nt2gkBEE2L5auFLjze13Llsud9vs+eSeNJoWnpEGUQr+UFmmh3PdUIGTaWwQbLIBZ\n41w5cx2WcIJlY75FfxnHErhG+EGTijWSntkxo8fhAoGBAMn8PaCgNVCviQcn4dMI\n/TvOlvVWkBPsBlSgUpsmkMbXApkfa07fig74uvIv30KtgcvpTvMr/gdX4CMOXQSE\naTKRnsPp2kSZ9kr/OPok0jOeyFPzWsPuKTZECpiIoJiZKCyJUfSkKh1LJHK/2BoM\ndfcjF29r5zMr3NH05MkYuSGH\n-----END PRIVATE KEY-----\n",
    "client_email" => "firebase-adminsdk-4bipc@pushnotification-43ae5.iam.gserviceaccount.com",
    "client_id" => "115771659792523415637",
    "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
    "token_uri" => "https://oauth2.googleapis.com/token",
    "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
    "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-4bipc@pushnotification-43ae5.iam.gserviceaccount.com",
    "universe_domain" => "googleapis.com"
];

$scheduledDateTime = '2024-06-15 10:00:00';

$currentDateTime = date('Y-m-d H:i:s');

if (shouldSendNotification($currentDateTime, $scheduledDateTime)) {
    try {
        $accessToken = getAccessToken($jsonKey);
        sendFCMNotification($accessToken);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

function base64UrlEncode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}
?>
