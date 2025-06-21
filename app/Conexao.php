<?php

namespace App;

use PDO;
use PDOException;

class Conexao {
    private static $pdo;

    public static function conectar() {
        if ( ! isset(self::$pdo) ) {
            try {
                $db_host = $_ENV['DB_HOST'];
                $db_name = $_ENV['DB_NAME'];
                $db_user = $_ENV['DB_USER'];
                $db_pass = $_ENV['DB_PASS'];

                self::$pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8", $db_user, $db_pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch ( PDOException $exception ) {
                die('Erro na conexão: ' . $exception->getMessage());
            }
        }

        return self::$pdo;
    }
}
