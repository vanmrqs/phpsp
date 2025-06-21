<?php

namespace Controller;

use App\Constants;

class PagamentoController extends Controller {
    public function index() {}

    public function editar(int $pagamento_id) {
        LoginController::require_login();

        $usuario    = $_SESSION['usuario'];
        $is_admin   = (int)$usuario->tipo_usuario_id === Constants::TIPO_USUARIO_ADMIN;

        //@TODO: Especificar qual a vulnerabilidade pela falta desse IF
        // ou trocar esse if para ver de outras pessoas
        /*if ( ! $is_admin ) {
            $this->view->render('erro', [
                'mensagem' => 'Acesso não autorizado'
            ], 401);
        }*/

        $pagamentoModel = new \Model\Pagamento();
        $pagamento      = (object)$pagamentoModel->get($pagamento_id);
        $detalhes       = $pagamentoModel->get_detalhes($pagamento->id);
        $this->view->render('pagamento/edit', [
            'pagamento' => $pagamento,
            'detalhes' => $detalhes
        ]);
    }
}