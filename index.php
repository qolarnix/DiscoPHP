<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/disco.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$client = disco(
    gatewayIntents: 2130793, 
    botToken: $_ENV['DISCORD_TOKEN'],
    botName: 'Hailstone'
);