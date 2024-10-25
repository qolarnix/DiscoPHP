<?php

declare(strict_types=1);

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/disco.php';
require __DIR__ . '/util/command.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$logger = new Logger('disco');
$handler = new StreamHandler('php://stdout');
$handler->setFormatter(new LineFormatter(null, null, false, true));
$logger->pushHandler($handler);

$token = $_ENV['DISCORD_TOKEN'];
$appId = '1298942628498509835';

setGlobalCommands(
    logger: $logger, 
    appId: $appId, 
    token: $token
);

disco(
    logger: $logger,
    gatewayIntents: 2130793, 
    botToken: $token,
    botName: 'Hailstone',
);