<?php

use Model\Pagamento;

require_once __DIR__ . '/../vendor/autoload.php';

$router = new AltoRouter();

//
// Login
//
$router->map('GET|POST', '/login', function() {
    require_once __DIR__ . '/../app/Controller/LoginController.php';

    $login = new \Controller\LoginController();
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        $login->autenticar();
    } else {
        $login->index();
    }
});

//
// Pagamento
//
$router->map('GET', '/pagamento', function () {
    require_once __DIR__ . '/../app/Controller/PagamentoController.php';

    $pagamento = new Controller\PagamentoController();
    $pagamento->index();
});

$router->map('GET', '/pagamento/view/[i:id]', function(int $pagamento_id) {
    require_once __DIR__ . '/../app/Controller/PagamentoController.php';

    $pagamento = new \Controller\PagamentoController();
    $pagamento->view($pagamento_id);
});

$router->map('GET|POST', '/pagamento/edit/[i:id]', function(int $pagamento_id) {
    require_once __DIR__ . '/../app/Controller/PagamentoController.php';

    $pagamento = new \Controller\PagamentoController();
    $pagamento->edit($pagamento_id);
});

$router->map('POST', '/pagamento/comentar', function() {
    require_once __DIR__ . '/../app/Controller/PagamentoController.php';

    $pagamento = new \Controller\PagamentoController();
    $pagamento->set_comentario();
});

$match = $router->match();

if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo 'Página não encontrada';
}
