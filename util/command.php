<?php

declare(strict_types=1);

function registerCommand(
    string $token, string $appId, 
    string $name, int $type, string $desc
) {
    $command = (object) [
        'name' => $name,
        'type' => $type ?? 1,
        'description' => $desc,
    ];
    json_encode($command);

    $endpoint = 'https://discord.com/api/v10/applications/'.$appId.'/commands';
    $headers = ["Authorization: Bot " . $token];

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    print_r($response);
}