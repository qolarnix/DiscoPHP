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
        curl_exec($ch);

        if(curl_errno($ch)) {
            print_r(curl_error($ch));
            $logger->notice('Failed to set application command: ' . $cmd->name);
        }
        else {
            $logger->notice('Set application command: ' . $cmd->name);
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

registerCommand(
    name: 'coinflip',
    desc: 'flip a coin!',
    type: 1,
    callback: function(): string {
        $result = mt_rand(0, 1);
        return $result ? 'heads' : 'tails';
    }
);

registerCommand(
    name: 'aura',
    desc: 'how much aura do you have?',
    type: 1,
    callback: function(): string {
        do { $result = mt_rand(-100000, 100000); } while($result === 0);
        return $result > 0
            ? "Plus " . $result . " Aura!"
            : "Minus " . abs($result) . " Aura";
    }
);