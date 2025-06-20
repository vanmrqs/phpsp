<?php

namespace Model;

use App\Conexao;
use PDO;

class Usuario {
    private $conexao;

    public static function validar_credenciais(string $email, string $senha) {
        $pdo = Conexao::conectar();

        $sql = "SELECT
                    usuario.id,
                    usuario.nome,
                    usuario.senha,
                    usuario.tipo_usuario_id
                FROM usuario
                WHERE email = :email
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        $usuario = $stmt->fetch(PDO::FETCH_OBJ);

        if ( $usuario && $usuario->senha === md5($senha) ) {
            return $usuario;
        }

        return null;
    }
}