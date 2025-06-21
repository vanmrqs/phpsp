<?php

namespace Controller;

use Model\Usuario;

class LoginController extends Controller {
    public function index() {
        $this->view->render('login');
    }

    public function autenticar() {
        $email = $_POST['email'] ?: '';
        $senha = $_POST['senha'] ?: '';

        if ( ! $email || ! $senha ) {
            $this->view->render('login', [
                'erro' => 'E-mail ou senha inválidos',
                'email' => $email
            ]);
        }

        $usuario = Usuario::validar_credenciais($email, $senha);

        if ( $usuario ) {
            $_SESSION['usuario'] = $usuario;
            header('Location: /');
        }

        $this->view->render('login', [
            'erro' => 'E-mail ou senha inválidos',
            'email' => $email
        ]);
    }

    public static function require_login() {
        if ( session_status() === PHP_SESSION_NONE ) {
            session_start();
        }

        if ( ! isset($_SESSION['usuario']) ) {
            header('Location: /login');
            exit;
        }
    }
}