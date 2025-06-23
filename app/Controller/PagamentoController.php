<?php

namespace Controller;

use App\Constants;
use Model\Pagamento;

class PagamentoController extends Controller {
    public function index() {
        LoginController::require_login();

        $usuario = $_SESSION['usuario'];
        $pagamento = new Pagamento();
        $this->view->render('pagamento/index', [
            'is_admin'            => $usuario->is_admin,
            'usuario'             => $usuario,
            'exibir_nome_e_cargo' => $usuario->is_admin,
            'pagamentos'          => ( $usuario->is_admin ) ? $pagamento->get_all() : $pagamento->get_by_user_id($usuario->id)
        ]);
    }

    public function view(int $pagamento_id) {
        LoginController::require_login();

        $usuario        = $_SESSION['usuario'];

        $pagamentoModel = new \Model\Pagamento();
        $pagamento      = (object)$pagamentoModel->get($pagamento_id);

        //@TODO: Especificar qual a vulnerabilidade pela falta desse IF
        //@TODO: Access Broken Control
        /*if ( ! $usuario->is_admin && $pagamento->usuario_id !== $usuario->id ) {
            $this->view->render('erro', [
                'mensagem' => 'Acesso não autorizado'
            ], 401);
        }*/

        $this->view->render('pagamento/view', [
            'is_admin'    => $usuario->is_admin,
            'usuario'     => $usuario,
            'pagamento'   => $pagamento,
            'comentarios' => $pagamentoModel->get_comentarios($pagamento->id)
        ]);
    }

    public function edit(int $pagamento_id) {
        LoginController::require_login();

        $usuario    = $_SESSION['usuario'];

        //@TODO: Especificar qual a vulnerabilidade pela falta desse IF
        // ou trocar esse if para ver de outras pessoas
        /*if ( ! $usuario->is_admin ) {
            $this->view->render('erro', [
                'mensagem' => 'Acesso não autorizado'
            ], 401);
        }*/

        $pagamentoModel      = new \Model\Pagamento();

        if ( isset($_POST['bonus'], $_POST['descontos']) ) {
            $pagamentoModel->edit($pagamento_id, $_POST['bonus'], $_POST['descontos']);
            header('Location: /pagamentos/' . $pagamento_id);
            exit;
        }

        $pagamento = (object)$pagamentoModel->get($pagamento_id);
        $this->view->render('pagamento/edit', [
            'is_admin'  => $usuario->is_admin,
            'usuario'   => $usuario,
            'pagamento' => $pagamento
        ]);
    }

    public function set_comentario() {
        LoginController::require_login();

        $pagamento_id = $_POST['pagamento_id'];
        $comentario   = trim($_POST['comentario']);

        if ( ! $pagamento_id || ! $comentario ) {
            header('Location: /pagamentos/' . $pagamento_id);
        }

        $pagamentoModel = new \Model\Pagamento();
        $pagamentoModel->set_comentario($pagamento_id, $comentario);

        header('Location: /pagamentos/' . $pagamento_id);
    }
}