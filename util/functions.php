<?php

declare(strict_types=1);

/**
 * @desc Perform a custom request with cURL
 */
function endpointRequest(array $headers, string $endpoint, string $request): object {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    $error = curl_errno($ch);
    curl_close($ch);

    return (object)[
        'result' => $result,
        'error' => $error
    ];
}

function endpointPost(array $headers, string $endpoint, array $payload) {
    $ch = curl_init($endpoint);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    $error = curl_errno($ch);
    curl_close($ch);

    return (object)[
        'result' => $result,
        'error' => $error
    ];
}