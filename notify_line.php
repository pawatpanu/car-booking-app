<?php
require_once 'notify_config.php';

function notify_moph($msg, $client_id = null, $secret = null) {
    if (!$client_id) $client_id = MOPH_NOTIFY_CLIENT_ID;
    if (!$secret) $secret = MOPH_NOTIFY_SECRET;

    $url = 'https://morpromt2f.moph.go.th/api/notify/send';

    $headers = [
        'Content-Type: application/json',
        'client-key: ' . $client_id,
        'secret-key: ' . $secret
    ];

    $payload = json_encode([
        'messages' => [
            [
                'type' => 'text',
                'text' => $msg
            ]
        ]
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false || $http_code !== 200) {
        error_log("MOPH Notify Failed: $error (HTTP $http_code)");
        return false;
    }

    return $response;
}
?>
