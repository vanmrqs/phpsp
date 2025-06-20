<?php

namespace View;

require_once __DIR__ . '/../../vendor/autoload.php';

class View {
    private $twig;

    public function __construct() {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
        $this->twig = new \Twig\Environment($loader);
    }

    public function render(string $template, array $dados = []) {
        if ( pathinfo($template, PATHINFO_EXTENSION) === '' ) {
            $template .= '.twig';
        }

        echo $this->twig->render($template, $dados);
        exit;
    }
}
