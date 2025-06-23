<?php

namespace Model;

use App\Conexao;
use PDO;

class Pagamento extends Model {
    public function get(int $pagamento_id): ?array {
        $sql = "SELECT 
                    pagamento.id,
                    pagamento.usuario_id,
                    MONTHNAME(pagamento.timepagamento) AS mes,
                    YEAR(pagamento.timepagamento) AS ano,
                    pagamento.valor,
                    usuario.nome,
                    usuario.cargo,
                    pagamento_detalhe.salario_base,
                    pagamento_detalhe.descontos,
                    pagamento_detalhe.bonus,
                    pagamento_detalhe.total
                FROM pagamento
                INNER JOIN usuario
                    ON usuario.id = pagamento.usuario_id
                INNER JOIN pagamento_detalhe
                    ON pagamento.id = pagamento_detalhe.pagamento_id
                WHERE pagamento.id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $pagamento_id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    // Retorna todos os pagamentos com nome e cargo do usuário
    public function get_all(): array {
        $sql = "
            SELECT 
                pagamento.id,
                pagamento.usuario_id,
                MONTHNAME(pagamento.timepagamento) AS mes,
                YEAR(pagamento.timepagamento) AS ano,
                pagamento.valor,
                usuario.nome AS usuario_nome,
                usuario.cargo AS usuario_cargo,
                (
                    SELECT 1
                    FROM pagamento_comentario
                    WHERE pagamento.id = pagamento_comentario.pagamento_id
                        AND pagamento_comentario.lido = 0
                    LIMIT 1
                ) AS has_comentarios_nao_lidos
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
        $sql = "SELECT 
                    pagamento.id,
                    pagamento.usuario_id,
                    MONTHNAME(pagamento.timepagamento) AS mes,
                    YEAR(pagamento.timepagamento) AS ano,
                    pagamento.valor,
                    pagamento_detalhe.salario_base,
                    pagamento_detalhe.descontos,
                    pagamento_detalhe.bonus,
                    pagamento_detalhe.total
                FROM pagamento
                INNER JOIN pagamento_detalhe
                    ON pagamento.id = pagamento_detalhe.pagamento_id
                WHERE usuario_id = :usuario_id
                ORDER BY
                    timepagamento DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_comentarios(int $pagamento_id): ?array {
        $sql = "SELECT 
                    pagamento_comentario.id,
                    pagamento_comentario.pagamento_id,
                    pagamento_comentario.texto,
                    DATE_FORMAT(pagamento_comentario.data_criacao, '%d/%m/%Y às %H:%i:%s') AS data_criacao
                FROM pagamento_comentario
                WHERE pagamento_id = :pagamento_id
                ORDER BY data_criacao DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':pagamento_id', $pagamento_id, PDO::PARAM_INT);
        $stmt->execute();

        $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $comentarios ?: null;
    }


    public function set_comentario(int $pagamento_id, string $comentario): bool {
        $sql = "INSERT INTO pagamento_comentario (pagamento_id, texto)
                VALUES (:pagamento_id, :texto)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':pagamento_id', $pagamento_id, PDO::PARAM_INT);
        $stmt->bindParam(':texto', $comentario, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function edit(int $pagamento_id, float $bonus, float $descontos): bool {
        $sql = "UPDATE pagamento_detalhe
                SET
                    pagamento_detalhe.bonus = :bonus,
                    pagamento_detalhe.descontos = :descontos,
                    pagamento_detalhe.total = ( pagamento_detalhe.salario_base + :bonus - :descontos )
                WHERE pagamento_id = :pagamento_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':bonus', $bonus);
        $stmt->bindValue(':descontos', $descontos);
        $stmt->bindValue(':pagamento_id', $pagamento_id, PDO::PARAM_INT);

        $sucesso = $stmt->execute();

        if ( $sucesso ) {
            $sql = "UPDATE pagamento
                    SET pagamento.valor = ( pagamento.valor + :bonus - :descontos )
                    WHERE pagamento.id = :pagamento_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':bonus', $bonus);
            $stmt->bindValue(':descontos', $descontos);
            $stmt->bindValue(':pagamento_id', $pagamento_id, PDO::PARAM_INT);

            return $stmt->execute();
        }

        return false;
    }
}