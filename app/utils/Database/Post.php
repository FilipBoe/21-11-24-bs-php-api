<?php

namespace App\Utils\Database;

class Post extends Model
{
    protected string $table = 'posts';

    protected array $attributes = [
        'id',
        'title',
        'description',
        'image',
        'user_id'
    ];

    public function link(): string
    {
        return "/post/{$this->get('id')}";
    }
}
