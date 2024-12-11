<?php

use App\Router;
use App\Utils\Database\Connection;
use App\Utils\Database\User;
use App\Utils\ServiceContainer;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();
date_default_timezone_set('Europe/Vienna');

$servername = "localhost:4000";
$username = "root";
$password = "password";

$sessionPasswordLifetime = 60 * 60 * 12; // 1/2 day

$database = new Connection();
$database->connect($servername, $username, $password);

$middlewares = [
    'api-auth' => function ($request) {
        $token = $request['headers']['auth'] ?? null;

        if (!$token) {
            abort('Unauthorized', 401);
        }

        $user = (new User)
            ->queryOne('WHERE api_key = :token', ['token' => $token])
            ->unset('password');

        if (!$user) {
            abort('Unauthorized', 401);
        }

        return [
            'user' => $user
        ];
    },
    'auth' => function () use ($sessionPasswordLifetime) {
        if (!isset($_SESSION['userId']) || $_SESSION['loggedInAt'] < time() - $sessionPasswordLifetime) {
            header('Location: /login');
            exit;
        }

        return [
            'user' => (new User)
                ->find($_SESSION['userId'])
                ->unset('password')
        ];
    },
    'tic-tac-toe--opening-times' => function () {
        if (!ticTacToeOpen()) {
            redirect('/settings');
        }

        return [];
    }
];

ServiceContainer::instance()
    ->set(Router::class, new Router(middlewares: $middlewares))
    ->set(Connection::class, $database);
