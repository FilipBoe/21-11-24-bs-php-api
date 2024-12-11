<?php

use App\Utils\Database\Post;

/** @var Post[] $posts */
$posts ??= [];
?>

<h1 class="mb-4 text-5xl">Posts</h1>

<div class="mt-10">
    <a href="/post/new" class="p-4 text-white bg-blue-500 rounded-lg hover:cursor-pointer">New Post</a>

    <div class="mt-8">
        <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
            <?php foreach ($posts as $post) : ?>
                <a href="<?php echo $post->link(); ?>" class="p-4 bg-gray-800 rounded-lg hover:cursor-pointer">
                    <h2 class="mb-2 text-3xl">
                        <?php echo $post->get('title'); ?>
                        <span class="text-gray-600">#<?php echo $post->get('id'); ?></span>
                    </h2>
                    <p class="text-lg"><?php echo $post->get('description'); ?></p>
                    <span class="text-sm text-gray-400">User ID: <?php echo $post->get('user_id'); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>