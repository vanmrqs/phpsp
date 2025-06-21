<?php

namespace Model;

use App\Conexao;

class Model {
    protected $pdo;

    public function __construct() {
        $this->pdo = Conexao::conectar();
    }
}