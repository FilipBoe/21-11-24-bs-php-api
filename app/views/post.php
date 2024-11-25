<?php

use App\Utils\Database\Post;
use App\Utils\Database\User;

/** @var Post $post */
$post ??= new Post;

/** @var User $user */
$user ??= new User;

$canDelete = $user->get('id') === $post->get('user_id');
?>

<a href="/" class="text-blue-500 hover:underline">Back</a>

<?php if ($canDelete) : ?>
    <div class="w-full my-2">
        <button class="p-2 text-white bg-red-600 rounded-sm" type="button">Löschen</button>
    </div>
<?php endif; ?>

<h2 class="mt-3 mb-2 text-5xl">
    <?php echo $post->get('title'); ?>
    <span class="text-gray-600">#<?php echo $post->get('id'); ?></span>
</h2>

<p class="mt-10 text-lg"><?php echo $post->get('description'); ?></p>

<div id="comments" class="mt-16">
    <h2 class="text-3xl">Comments</h2>
</div>

<input type="text" id="comment" class="w-1/2 p-5 mt-5 rounded-lg bg-slate-600" placeholder="Comment">

<script>
    const commentUrl = '/api/posts/<?php echo $post->get('id'); ?>/comments';

    document.addEventListener('DOMContentLoaded', () => {
        const commentInput = document.getElementById('comment');
        const deleteButton = document.querySelector('button');

        commentInput.addEventListener('keypress', event => {
            if (event.key !== 'Enter') {
                return;
            }

            const comment = commentInput.value;
            commentInput.value = '';

            fetch(commentUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'auth': "<?php echo $user->get('api_key'); ?>"
                    },
                    body: JSON.stringify({
                        message: comment
                    })
                })
                .then(response => response.json())
                .then(comment => {
                    fetchComments();
                });
        });

        deleteButton.addEventListener('click', () => {
            deletePost();
        });

        const fetchComments = () => {
            fetch(commentUrl, {
                    headers: {
                        'auth': "<?php echo $user->get('api_key'); ?>"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    const commentsContainer = document.getElementById('comments');
                    commentsContainer.innerHTML = '';

                    data.results.forEach(comment => {
                        const commentElement = document.createElement('div');
                        commentElement.className = 'w-1/2 p-5 mt-2 rounded-lg bg-slate-600';

                        const commentMessage = document.createElement('p');
                        commentMessage.className = 'text-lg';
                        commentMessage.innerText = comment.message;

                        const commentUser = document.createElement('span');
                        commentUser.className = 'text-sm text-gray-400';
                        commentUser.innerText = `User ID: ${comment.user_id}`;

                        commentElement.appendChild(commentMessage);
                        commentElement.appendChild(commentUser);
                        commentsContainer.appendChild(commentElement);
                    });
                });
        }

        const deletePost = () => {
            fetch('/api/posts/<?php echo $post->get('id'); ?>', {
                    method: 'DELETE',
                    headers: {
                        'auth': "<?php echo $user->get('api_key'); ?>"
                    }
                })
                .then(response => response.json())
                .then(() => {
                    window.location.href = '/';
                });
        }

        fetchComments();
    });
</script>