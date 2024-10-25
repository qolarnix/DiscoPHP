<?php

declare(strict_types=1);

enum GATEWAY: string {
    case JSON = 'wss://gateway.discord.gg/?v=10&encoding=json';
}

enum OPCODE: int {
    case DISPATCH = 0;
    case HEARTBEAT = 1;
    case IDENTIFY = 2;
    case PRESENCE_UPDATE = 3;
    case VOICE_STATE_UPDATE = 4;
    case RESUME = 6;
    case RECONNECT = 7;
    case REQUEST_GUILD_MEMBERS = 8;
    case INVALID_SESSION = 9;
    case HELLO = 10;
    case HEARTBEAT_ACK = 11;
    case REQUEST_SOUNDBOARD_SOUNDS = 31;
}