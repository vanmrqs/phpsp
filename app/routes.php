<?php

use Model\Pagamento;

require_once __DIR__ . '/../vendor/autoload.php';

$router = new AltoRouter();

//
// Login
//
$router->map('GET', '/', function () {
    header("Location: /pagamentos");
});

$router->map('GET|POST', '/login', function() {
    require_once __DIR__ . '/../app/Controller/LoginController.php';

    $login = new \Controller\LoginController();
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        $login->login();
    } else {
        $login->index();
    }
});

$router->map('GET', '/logout', function() {
    require_once __DIR__ . '/../app/Controller/LoginController.php';

    $login = new \Controller\LoginController();
    $login->logout();
});

//
// Pagamento
//
$router->map('GET', '/pagamentos', function () {
    require_once __DIR__ . '/../app/Controller/PagamentoController.php';

    $pagamento = new Controller\PagamentoController();
    $pagamento->index();
});

$router->map('GET', '/pagamentos/[i:id]', function(int $pagamento_id) {
    require_once __DIR__ . '/../app/Controller/PagamentoController.php';

    $pagamento = new \Controller\PagamentoController();
    $pagamento->view($pagamento_id);
});

$router->map('GET|POST', '/pagamentos/edit/[i:id]', function(int $pagamento_id) {
    require_once __DIR__ . '/../app/Controller/PagamentoController.php';

    $pagamento = new \Controller\PagamentoController();
    $pagamento->edit($pagamento_id);
});

$router->map('POST', '/pagamentos/comentar', function() {
    require_once __DIR__ . '/../app/Controller/PagamentoController.php';

    $pagamento = new \Controller\PagamentoController();
    $pagamento->set_comentario();
});

//
// Usuário
//
$router->map('GET', '/usuarios', function () {
    require_once __DIR__ . '/../app/Controller/UsuarioController.php';

    $usuarioController = new Controller\UsuarioController();
    $usuarioController->index();
});

$router->map('GET', '/usuarios/[i:id]', function(int $usuario_id) {
    require_once __DIR__ . '/../app/Controller/UsuarioController.php';

    $usuarioController = new \Controller\UsuarioController();
    $usuarioController->view($usuario_id);
});

// core

$match = $router->match();

if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo 'Página não encontrada';
}
