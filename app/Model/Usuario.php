<?php

namespace Model;

use App\Conexao;
use App\Constants;
use PDO;

class Usuario extends Model {

    public static function validar_credenciais(string $email, string $senha) {
        $pdo = Conexao::conectar();

        $sql = "SELECT
                    usuario.id,
                    usuario.nome,
                    usuario.senha,
                    ( usuario.tipo_usuario_id = :tipo_admin ) AS is_admin
                FROM usuario
                WHERE email = :email
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'email'      => $email,
            'tipo_admin' => Constants::TIPO_USUARIO_ADMIN
        ]);

        $usuario = $stmt->fetch(PDO::FETCH_OBJ);

        if ( $usuario && $usuario->senha === md5($senha) ) {
            return $usuario;
        }

        return null;
    }

    public function get_all(): array {
        $sql = "SELECT
                    usuario.id,
                    usuario.nome,
                    usuario.email,
                    usuario.cpf,
                    usuario.telefone,
                    usuario.cargo,
                    usuario.tipo_usuario_id
                FROM usuario
                WHERE usuario.tipo_usuario_id = :tipo_usuario_comum
                ORDER BY usuario.nome";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tipo_usuario_comum' => Constants::TIPO_USUARIO_COMUM]);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usuarios as &$usuario) {
            // Formata CPF: 000.000.000-00
            $usuario['cpf'] = preg_replace(
                "/^(\d{3})(\d{3})(\d{3})(\d{2})$/",
                "$1.$2.$3-$4",
                preg_replace("/\D/", "", $usuario['cpf'])
            );

            // Formata telefone: (00) 00000-0000 ou (00) 0000-0000
            $usuario['telefone'] = preg_replace_callback(
                "/^(\d{2})(\d{4,5})(\d{4})$/",
                fn($m) => "($m[1]) $m[2]-$m[3]",
                preg_replace("/\D/", "", $usuario['telefone'])
            );
        }

        return $usuarios;
    }

    public static function get_by_id(int $usuario_id) {
        $pdo = Conexao::conectar();

        $sql = "SELECT
                    usuario.id,
                    usuario.nome,
                    usuario.email,
                    usuario.cpf,
                    usuario.telefone,
                    usuario.cargo,
                    usuario.tipo_usuario_id
                FROM usuario
                WHERE usuario.id = :usuario_id
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuario_id]);

        $usuario = $stmt->fetch(PDO::FETCH_OBJ);

        if ( $usuario ) {
            // Formata telefone: (00) 00000-0000 ou (00) 0000-0000
            $usuario->telefone = preg_replace_callback(
                "/^(\d{2})(\d{4,5})(\d{4})$/",
                fn($m) => "($m[1]) $m[2]-$m[3]",
                preg_replace("/\D/", "", $usuario->telefone)
            );

            return $usuario;
        }

        return null;
    }

    public function set_admin(int $usuario_id): bool {
        $pdo = \App\Conexao::conectar();

        $sql = "UPDATE usuario
                SET tipo_usuario_id = :tipo_usuario_admin
                WHERE id = :usuario_id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':tipo_usuario_admin', Constants::TIPO_USUARIO_ADMIN, PDO::PARAM_INT);

        return $stmt->execute();
    }
}