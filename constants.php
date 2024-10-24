<?php

declare(strict_types=1);

enum GATEWAY: string {
    case JSON = 'wss://gateway.discord.gg/?v=10&encoding=json';
}

enum OPCODE: int {
    case zero = 0;
    case one = 1;
    case two = 2;
    case three = 3;
    case four = 4;
    case six = 6;
    case seven = 7;
    case eight = 8;
    case nine = 9;
    case ten = 10;
    case eleven = 11;
}