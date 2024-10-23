<?php

declare(strict_types=1);

use Amp\Websocket\Client\WebsocketConnection;
use Amp\Websocket\Client\WebsocketHandshake;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Revolt\EventLoop;

use function Amp\Websocket\Client\connect;

require __DIR__ . '/vendor/autoload.php';

const el = PHP_EOL;
const GATEWAY = 'wss://gateway.discord.gg/?v=10&encoding=json';

$logger = new Logger('disco');
$logger->pushHandler(new StreamHandler('php://stdout'));

$handshake = new WebsocketHandshake(GATEWAY);
    
try {
    $conn = connect($handshake);

    $logger->info('Discord gateway connection success!');

    foreach($conn as $msg) {
        $payload = $msg->buffer();
        $parsed = json_decode($payload);

        print_r($parsed);

        switch($parsed->op) {
            case 10: handleHeartbeat($conn, $logger, $parsed); break;
            case 11: $logger->info('Heartbeat acknowledged!'); break;
        }
    }
}
catch(\Throwable | Exception $e) {
    $logger->error('Discord gateway connection failed! ' . $e->getMessage());
}

function handleHeartbeat(WebsocketConnection $conn, Logger $logger, object $parsed): void {
    $interval = $parsed->d->heartbeat_interval;
    $jitter = mt_rand() / mt_getrandmax();
    $initDelay = $interval * $jitter;

    $logger->notice('Heartbeat interval: ' . $interval);

    $keepalive = (object) [
        'op' => 1,
        'd' => $parsed->s ?? null
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

EventLoop::run();