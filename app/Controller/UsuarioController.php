<?php

namespace Controller;

use App\Constants;
use Model\Pagamento;
use Model\Usuario;

class UsuarioController extends Controller {
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

        $usuario = new Usuario();
        $this->view->render('usuario/index', [
            'is_admin' => $is_admin,
            'usuarios' => $usuario->get_all()
        ]);
    }
}