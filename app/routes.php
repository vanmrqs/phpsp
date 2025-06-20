<?php

require_once __DIR__ . '/../vendor/autoload.php';

$router = new AltoRouter();

$router->map('GET', '/', function () {
    require_once __DIR__ . '/../app/Controller/Home.php';

    $home = new Controller\Home();
    $home->index();
});

$router->map('GET|POST', '/login', function() {
    require_once __DIR__ . '/../app/Controller/Login.php';

    $login = new \Controller\Login();
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        $login->autenticar();
    } else {
        $login->index();
    }
});

$match = $router->match();

if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo 'Página não encontrada';
}
