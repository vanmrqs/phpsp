<?php

namespace Model;

use App\Conexao;
use PDO;

class Pagamento {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Conexao::conectar();
    }

    // Retorna todos os pagamentos com nome e cargo do usuário
    public function get_all(): array {
        $sql = "
            SELECT 
                pagamento.id,
                pagamento.usuario_id,
                FROM_UNIXTIME(pagamento.timepagamento, '%m') AS mes,
                FROM_UNIXTIME(pagamento.timepagamento, '%Y') AS ano,
                pagamento.valor,
                usuario.nome AS colaborador_nome,
                usuario.cargo AS colaborador_cargo
            FROM pagamento
            INNER JOIN usuario
                ON usuario.id = pagamento.usuario_id
            ORDER BY pagamento.timepagamento DESC
        ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retorna os pagamentos de um usuário específico
    public function get_by_user_id(int $usuario_id): array {
        $sql = "
            SELECT 
                id,
                usuario_id,
                timepagamento,
                valor
            FROM pagamento
            WHERE usuario_id = :usuario_id
            ORDER BY timepagamento DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}