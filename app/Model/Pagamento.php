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
                LPAD(MONTH(pagamento.timepagamento), 2, '0') AS mes,
                YEAR(pagamento.timepagamento) AS ano,
                pagamento.valor,
                usuario.nome AS colaborador_nome,
                usuario.cargo AS colaborador_cargo
            FROM pagamento
            INNER JOIN usuario
                ON usuario.id = pagamento.usuario_id
            ORDER BY
                pagamento.timepagamento DESC,
                usuario.nome";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retorna os pagamentos de um usuário específico
    public function get_by_user_id(int $usuario_id): array {
        $sql = "
            SELECT 
                pagamento.id,
                pagamento.usuario_id,
                LPAD(MONTH(pagamento.timepagamento), 2, '0') AS mes,
                YEAR(pagamento.timepagamento) AS ano,
                valor
            FROM pagamento
            WHERE usuario_id = :usuario_id
            ORDER BY
                timepagamento DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}