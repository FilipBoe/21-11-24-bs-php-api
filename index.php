<?php

use App\Router;
use App\Utils\Database\Comment;
use App\Utils\Database\Post;
use App\Utils\Database\Setting;
use App\Utils\Database\User;

require_once __DIR__ . '/app/bootstrap.php';

/**
 * @var Router $router
 */
$router = app(Router::class);

$router->get('/', function () {
    Router::view('pages/index', [
        'posts' => (new Post)->all()
    ]);
}, middleware: ['auth']);

$router->get('/settings', function (array $request) {
    $settings = (new Setting)->all();

    Router::view('pages/settings', [
        'user' => $request['user'],
        'settings' => $settings
    ]);
}, middleware: ['auth']);

$router->get('/tic-tac-toe', function () {
    Router::view('pages/tic-tac-toe', []);
}, middleware: ['auth', 'tic-tac-toe--opening-times']);

$router->get('/login', function () {
    Router::view('login');
});

$router->post('/api/login', function (array $request) {
    $email = $request['data']['email'];
    $password = $request['data']['password'];
    $salt = 'my-salt';

    $passwordHash = base64_encode(hash("sha256", "$password.$salt", true));

    $user = (new User)->queryOne(
        'WHERE email = :email AND password = :password',
        ['email' => $email, 'password' => $passwordHash]
    );

    if (!$user) {
        Router::jsonResponse(['error' => 'Invalid credentials'], 401);
    }

    $_SESSION['userId'] = $user->get('id');
    $_SESSION['loggedInAt'] = time();

    Router::jsonResponse($user->unset('password')->toArray());
});

$router->get('/post/new', function (array $request) {
    Router::view('new-post', [
        'user' => $request['user']
    ]);
}, middleware: ['auth']);

$router->get('/post/{id}', function (array $request, int $id) {
    $post = (new Post)->find($id);

    if (!$post) {
        abort('Post not found', 404);
    }

    Router::view('post', ['post' => $post, 'user' => $request['user']]);
}, middleware: ['auth']);

$router->get('/api/me', function (array $request) {
    Router::jsonResponse($request['user']
        ->unset('api_key')
        ->unset('password')
        ->toArray());
}, middleware: ['api-auth']);

$router->get('/api/posts', function () {
    $posts = (new Post)->all();

    Router::jsonResponse(['results' => modelsToArray($posts)]);
}, middleware: ['api-auth']);

$router->post('/api/posts', function (array $request) {
    $data = $request['data'];

    $data['user_id'] = $request['user']->get('id');

    $newId = (new Post)->create($data);

    Router::jsonResponse(['id' => $newId]);
}, middleware: ['api-auth']);

$router->delete('/api/posts/{id}', function (array $request, int $id) {
    $post = (new Post)->find($id);

    if (!$post) {
        abort('Post not found', 404);
    }

    if ($post->get('user_id') !== $request['user']->get('id')) {
        abort('Unauthorized', 401);
    }

    $post->delete($id);

    Router::jsonResponse(['message' => 'Post deleted']);
}, middleware: ['api-auth']);

$router->get('/api/posts/{id}', function (array $request) {
    $post = (new Post)->find($request['params']['id']);

    if (!$post) {
        abort('Post not found', 404);
    }

    Router::jsonResponse($post->toArray());
}, middleware: ['api-auth']);

$router->get('/api/posts/{id}/comments', function (array $request, int $id) {
    $comments = (new Comment)->all('WHERE post_id = :id', ['id' => $id]);

    Router::jsonResponse(['results' => modelsToArray($comments)]);
}, middleware: ['api-auth']);

$router->post('/api/posts/{id}/comments', function (array $request, int $id) {
    $data = $request['data'];

    $data['post_id'] = $id;
    $data['user_id'] = $request['user']->get('id');

    $newId = (new Comment)->create($data);

    Router::jsonResponse(['id' => $newId]);
}, middleware: ['api-auth']);

$router->post('/api/settings', function (array $request) {
    $data = $request['data'];

    $tttFrom = $data['ttt_from'];
    $tttTo = $data['ttt_to'];


    $ids = (new Setting)->updateOrNew(
        [
            [
                'user_id' => $request['user']->get('id'),
                'key' => 'tic-tac-toe-from',
                'value' => $tttFrom
            ],
            [
                'user_id' => $request['user']->get('id'),
                'key' => 'tic-tac-toe-to',
                'value' => $tttTo
            ]
        ],
        [
            'user_id',
            'key'
        ]
    );

    Router::jsonResponse([
        'data' => $data,
        'ids' => $ids
    ]);
}, middleware: ['api-auth']);

$router->match($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
