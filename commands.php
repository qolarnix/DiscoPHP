<?php

declare(strict_types=1);

function commandCreate(
    int $id, int $appId, int $guildId, 
    string $name, string $desc, 
    int $permissions = 1, 
    bool $nsfw = false, 
    int $version
) {
    $command = (object) [
        'id' => $id,
        'application_id' => $appId,
        'guid_id' => $guildId,
        'name' => $name,
        'description' => $desc,
        'default_member_permissions' => $permissions,
        'nsfw' => $nsfw,
        'version' => $version,
    ];
    return $command;
}