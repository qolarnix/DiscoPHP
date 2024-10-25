<?php

declare(strict_types=1);

use Amp\Websocket\Client\WebsocketConnection;
use Amp\Websocket\Client\WebsocketHandshake;
use Monolog\Logger;
use Revolt\EventLoop;

use function Amp\Websocket\Client\connect;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/constants.php';

function disco(Logger $logger, int $gatewayIntents, string $botToken, string $botName) {
    $handshake = new WebsocketHandshake(GATEWAY::JSON->value);

    try {
        $conn = connect($handshake);
        $logger->info('Discord gateway connection success!');

        foreach($conn as $msg) {
            $payload = $msg->buffer();
            $parsed = json_decode($payload);
            $parsed = (object) [
                'opcode' => $parsed->op,
                'data' => $parsed->d,
                'sequence' => $parsed->s,
                'event' => $parsed->t,
            ];
    
            switch($parsed->opcode) {
                case OPCODE::DISPATCH->value:
                    $logger->notice('Event triggered: ' . $parsed->event);
                    if($parsed->event === 'MESSAGE_CREATE') logMessage($logger, $parsed);
                    if($parsed->event === 'INTERACTION_CREATE') handleInteraction($logger, $parsed->data);
                    break;

                case OPCODE::HELLO->value:
                    handleHeartbeat($conn, $logger, $parsed);
                    handleIdentify($conn, $logger, $gatewayIntents, $botToken, $botName);
                    break;

                case OPCODE::HEARTBEAT_ACK->value: 
                    $logger->notice('Heartbeat acknowledged!');
                    break;
            }
        }
    }
    catch(\Throwable | Exception $e) {
        $logger->error('Discord gateway connection failed! ' . $e->getMessage());
        print_r($e);
    }
}

function logMessage(Logger $logger, object $parsed) {
    $sender = $parsed->data->author->global_name;
    $content = $parsed->data->content;
    $logger->notice($sender . ': ' . $content);
}

function handleInteraction(Logger $logger, object $data) {
    print_r($data);

    $userName = $data->member->user->global_name;
    $commandName = $data->data->name;
    $interactionId = $data->id;
    $interactionToken = $data->token;

    handleCommand($userName, $commandName, $interactionId, $interactionToken);
}

function handleHeartbeat(WebsocketConnection $conn, Logger $logger, object $parsed): void {
    $interval = $parsed->data->heartbeat_interval;
    $jitter = mt_rand() / mt_getrandmax();
    $initDelay = $interval * $jitter;

    $logger->notice('Heartbeat interval: ' . $interval);

    $keepalive = (object) [
        'op' => 1,
        'd' => $parsed->sequence ?? null
    ];

    EventLoop::delay($initDelay / 1000, function() use($conn, $keepalive, $logger, $interval): void {
        $conn->sendText(json_encode($keepalive));
        $logger->notice('Initial heartbeat sent!');

        EventLoop::repeat($interval / 1000, function() use($conn, $keepalive, $logger): void {
            $conn->sendText(json_encode($keepalive));
            $logger->notice('Heartbeat sent!');
        });
    });
}

function handleIdentify(WebsocketConnection $conn, Logger $logger, int $intents, string $token, string $name) {
    $identity = (object) [
        'op' => 2,
        'd' => [
            'token' => $token,
            'intents' => $intents,
            'properties' => [
                'os' => 'linux',
                'browser' => $name,
                'device' => $name
            ]
        ]
    ];
    $conn->sendText(json_encode($identity));
    $logger->notice('Identity payload sent!');
}