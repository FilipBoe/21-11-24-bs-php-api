<?php

use App\Utils\Database\Post;
use App\Utils\Database\User;

/** @var Post $post */
$post ??= new Post;

/** @var User $user */
$user ??= new User;
?>

<a href="/" class="text-blue-500 hover:underline">Back</a>

<form id="form">
    <input type="text" id="title" class="w-1/2 p-5 mt-5 rounded-lg bg-slate-600" placeholder="Title">
    <input type="text" id="description" class="w-1/2 p-5 mt-5 rounded-lg bg-slate-600" placeholder="Description">
    <button type="submit" class="p-4 mt-5 text-white bg-blue-500 rounded-lg hover:cursor-pointer">Create Post</button>
</form>

<script>
    const form = document.getElementById('form');
    const title = document.getElementById('title');
    const description = document.getElementById('description');

    form.addEventListener('submit', event => {
        event.preventDefault();

        fetch('/api/posts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'auth': "<?php echo $user->get('api_key'); ?>"
                },
                body: JSON.stringify({
                    title: title.value,
                    description: description.value
                })
            })
            .then(response => response.json())
            .then(data => {
                window.location.href = `/post/${data.id}`;
            });
    });
</script>