<?php

namespace Controller;

use App\Constants;
use Model\Pagamento;

class PagamentoController extends Controller {
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
        $this->view->render('pagamento/index', [
            'is_admin'   => $is_admin,
            'pagamentos' => ( $is_admin ) ? $pagamento->get_all() : $pagamento->get_by_user_id($usuario->id)
        ]);
    }

    public function view(int $pagamento_id) {
        LoginController::require_login();

        $usuario    = $_SESSION['usuario'];

        //@TODO: Especificar qual a vulnerabilidade pela falta desse IF
        // ou trocar esse if para ver de outras pessoas
        /*$is_admin   = (int)$usuario->tipo_usuario_id === Constants::TIPO_USUARIO_ADMIN;

        if ( ! $is_admin ) {
            $this->view->render('erro', [
                'mensagem' => 'Acesso não autorizado'
            ], 401);
        }*/

        $pagamentoModel = new \Model\Pagamento();
        $pagamento      = (object)$pagamentoModel->get($pagamento_id);
        $detalhes       = $pagamentoModel->get_detalhes($pagamento->id);
        $this->view->render('pagamento/view', [
            'pagamento'   => $pagamento,
            'detalhes'    => $detalhes,
            'comentarios' => $pagamentoModel->get_comentarios($pagamento->id)
        ]);
    }

    public function set_comentario() {
        LoginController::require_login();

        $pagamento_id = $_POST['pagamento_id'];
        $comentario   = trim($_POST['comentario']);

        if ( ! $pagamento_id || ! $comentario ) {
            header('Location: /pagamento/view/' . $pagamento_id);
        }

        $pagamentoModel = new \Model\Pagamento();
        $pagamentoModel->set_comentario($pagamento_id, $comentario);

        header('Location: /pagamento/view/' . $pagamento_id);
    }
}