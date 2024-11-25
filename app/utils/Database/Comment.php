<?php

namespace App\Utils\Database;

class Comment extends Model
{
    protected string $table = 'comments';

    protected array $attributes = [
        'id',
        'user_id',
        'post_id',
        'message',
    ];
}
