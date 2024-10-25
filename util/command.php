<?php

declare(strict_types=1);

use Monolog\Logger;

$commands = [];

function registerCommand(string $name, string $desc, callable $callback, int $type = 1) {
    global $commands;
    $commands[$name] = [
        'name' => $name,
        'type' => $type,
        'description' => $desc,
        'callback' => $callback
    ];
}

function setGlobalCommands(Logger $logger, string $appId, string $token) {
    $headers = [
        'Authorization: Bot ' . $token,
        'Content-Type: application/json'
    ];
    $endpoint = 'https://discord.com/api/v10/applications/'. $appId .'/commands';

    global $commands;
    foreach($commands as $cmd) {
        unset($cmd['callback']);
        $cmd = (object)$cmd;

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cmd));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            print_r(curl_error($ch));
            $logger->notice('Failed to set application command: ' . $cmd->name);
        }
        else {
            $logger->notice('Set application command: ' . $cmd->name);
            print_r($response);
        }
    }
}

function handleCommand(string $userName, string $commandName, string $interactionId, string $interactionToken) {
    global $commands;

    $content = call_user_func($commands[$commandName]['callback'], $userName);
    $endpoint = 'https://discord.com/api/v10/interactions/'.$interactionId.'/'.$interactionToken.'/callback';
    $headers = ['Content-Type: application/json'];

    $payload = [
        'type' => 4,
        'data' => ['content' => $content],
    ];

    print_r($payload);

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if(curl_errno($ch)) {
        print_r(curl_error($ch));
    }
    else {
        print_r($response);
    }
}

registerCommand(
    name: 'bora',
    desc: 'bora',
    type: 1,
    callback: function(string $userName): string {
        return 'bora ' . $userName;
    }
);