<?php

namespace App\Utils\Database;

class User extends Model
{
    protected string $table = 'users';

    protected array $attributes = [
        'id',
        'first_name',
        'last_name',
        'age',
        'email',
        'api_key'
    ];
}
