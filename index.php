<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/disco.php';
require __DIR__ . '/util/command.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$token = $_ENV['DISCORD_TOKEN'];

$client = disco(
    gatewayIntents: 2130793, 
    botToken: $token,
    botName: 'Hailstone',
    commands: []
);