<?php

namespace Controller;

use App\Constants;
use Model\Pagamento;

class HomeController extends Controller {
    public function index() {
        if ( session_status() === PHP_SESSION_NONE ) {
            session_start();
        }

        if ( ! isset($_SESSION['usuario']) ) {
            header('Location: /login');
            exit;
        }

        $usuario    = $_SESSION['usuario'];
        $is_admin   = (int)$usuario->tipo_usuario_id === Constants::TIPO_USUARIO_ADMIN;

        $pagamento = new Pagamento();
        $this->view->render('home/index', [
            'is_admin'   => $is_admin,
            'pagamentos' => ( $is_admin ) ? $pagamento->get_all() : $pagamento->get_by_user_id($usuario->id)
        ]);
    }
}