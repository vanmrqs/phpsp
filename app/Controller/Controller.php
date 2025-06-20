<?php

namespace Controller;

use View\View;

class Controller {
    protected View $view;

    public function __construct() {
        $this->view = new View();
    }
}