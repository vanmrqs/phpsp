<?php

namespace Controller;

use App\Constants;
use Model\Pagamento;
use Model\Usuario;

class UsuarioController extends Controller {
    public function index() {
        LoginController::require_login();

        $usuario     = $_SESSION['usuario'];
        $usuariModel = new Usuario();
        $this->view->render('usuario/index', [
            'is_admin' => $usuario->is_admin,
            'usuario'  => $usuario,
            'usuarios' => $usuariModel->get_all()
        ]);
    }

    public function view(int $usuario_id) {
        LoginController::require_login();

        $usuario        = $_SESSION['usuario'];
        $usuarioModel   = new Usuario();

        $pagamentoModel = new \Model\Pagamento();
        $pagamentos     = $pagamentoModel->get_by_user_id($usuario_id);

        //@TODO: Especificar qual a vulnerabilidade pela falta desse IF
        //@TODO: Access Broken Control
        /*if ( ! $usuario->is_admin && $pagamento->usuario_id !== $usuario->id ) {
            $this->view->render('erro', [
                'mensagem' => 'Acesso não autorizado'
            ], 401);
        }*/

        $this->view->render('usuario/view', [
            'is_admin'                        => $usuario->is_admin,
            'exibir_detalhes'                 => true,
            'pagamentos'                      => $pagamentos,
            'usuario'                         => $usuarioModel->get_by_id($usuario_id),
            'exibir_mensagem_vulnerabilidade' => ( ! $usuario->is_admin && $usuario_id !== (int)$usuario->id )
        ]);
    }

    public function set_admin(int $usuario_id) {
        LoginController::require_login();

        $usuario = $_SESSION['usuario'];
        if ( ! $usuario->is_admin ) {
            $this->view->render('base/erro', [
                'mensagem' => 'Acesso não autorizado'
            ], 401);
        }

        $usuarioModel = new Usuario();
        $usuarioModel->set_admin($usuario_id);
    }
}