<?php

namespace Controller;

use Controller\Controller;

class Home extends Controller {
    public function index() {
        if ( session_status() === PHP_SESSION_NONE ) {
            session_start();
        }

        if ( ! isset($_SESSION['usuario']) ) {
            header('Location: /login');
            exit;
        }

        $usuario = $_SESSION['usuario'];
        $this->view->render('home/index', [
            'usuario' => $usuario
        ]);
    }
}