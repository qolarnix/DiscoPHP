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

function getCommands(): array {
    $token = $_ENV['DISCORD_TOKEN'];
    $appId = $_ENV['DISCORD_APP_ID'];

    $headers = ['Authorization: Bot ' . $token];
    $endpoint = GATEWAY::API->value . '/applications/' . $appId . '/commands';

    $response = endpointRequest($headers, $endpoint, 'GET');
    $commands = $response->result;
    $commands = json_decode($commands);

    $list = [];
    $count = 0;
    foreach($commands as $cmd) {
        $list[$count]['name'] = $cmd->name;
        $list[$count]['desc'] = $cmd->description;
        $count++;
    }
    return $list;
}

function deleteCommand(string $commandId) {
    $token = $_ENV['DISCORD_TOKEN'];
    $appId = $_ENV['DISCORD_APP_ID'];

    $headers = ['Authorization: Bot ' . $token];
    $endpoint = GATEWAY::API->value . '/applications/' . $appId . '/commands/' . $commandId;
    
    $response = endpointRequest($headers, $endpoint, 'DELETE');
    print_r($response);
}

function setGlobalCommands(string $appId, string $token) {
    $headers = [
        'Authorization: Bot ' . $token,
        'Content-Type: application/json'
    ];
    $endpoint = GATEWAY::API->value.'/applications/'.$appId.'/commands';

    global $commands;
    foreach($commands as $cmd) {
        unset($cmd['callback']);

        $response = endpointPost($headers, $endpoint, $cmd);
        if($response->error) {
            print_r($response->error);
        } else {
            print_r($response->result);
        }
    }
}

/**
 * @desc Handle command response
 */
function handleCommand(string $userName, string $commandName, string $interactionId, string $interactionToken) {
    global $commands;

    $content = call_user_func($commands[$commandName]['callback'], $userName);
    $endpoint = GATEWAY::API->value.'/interactions/'.$interactionId.'/'.$interactionToken.'/callback';
    $headers = ['Content-Type: application/json'];
    $payload = [
        'type' => 4,
        'data' => ['content' => $content, 'embed' => 'test'],
    ];

    $response = endpointPost($headers, $endpoint, $payload);
    print_r($response);
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
    name: 'list',
    desc: 'list commands',
    type: 1,
    callback: function() {
        $commands = getCommands();
        return json_encode($commands);
    }
);