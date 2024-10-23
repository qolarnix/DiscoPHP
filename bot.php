<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/disco.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

disco(
    gatewayIntents: 513, 
    botToken: $_ENV['DISCORD_TOKEN'],
    botName: 'DiscoPHP'
);