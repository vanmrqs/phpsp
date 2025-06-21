<?php

namespace Controller;

use App\Constants;
use Model\Pagamento;
use Model\Usuario;

class UsuarioController extends Controller {
    public function index() {
        LoginController::require_login();

        $usuario    = $_SESSION['usuario'];
        $is_admin   = (int)$usuario->tipo_usuario_id === Constants::TIPO_USUARIO_ADMIN;

        $usuario = new Usuario();
        $this->view->render('usuario/index', [
            'is_admin' => $is_admin,
            'usuarios' => $usuario->get_all()
        ]);
    }

    public function view(int $usuario_id) {
        LoginController::require_login();

        $usuarioLogado    = $_SESSION['usuario'];

        $is_admin   = (int)$usuarioLogado->tipo_usuario_id === Constants::TIPO_USUARIO_ADMIN;

        $usuarioModel = new Usuario();

        $pagamentoModel = new \Model\Pagamento();
        $pagamentos     = $pagamentoModel->get_by_user_id($usuario_id);

        //@TODO: Especificar qual a vulnerabilidade pela falta desse IF
        //@TODO: Access Broken Control
        /*if ( ! $is_admin && $pagamento->usuario_id !== $usuario->id ) {
            $this->view->render('erro', [
                'mensagem' => 'Acesso não autorizado'
            ], 401);
        }*/

        $this->view->render('usuario/view', [
            'is_admin'        => $is_admin,
            'exibir_detalhes' => true,
            'pagamentos'      => $pagamentos,
            'usuario'         => $usuarioModel->get_by_id($usuario_id)
        ]);
    }
}