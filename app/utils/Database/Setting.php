<?php

namespace App\Utils\Database;

class Setting extends Model
{
    protected string $table = 'settings';

    protected array $attributes = [
        'id',
        'user_id',
        'key',
        'value',
    ];
}
