<?php

declare(strict_types=1);

class ConexaoBanco
{
    private static ?PDO $conexao = null;

    private function __construct()
    {
    }

    public static function obterConexao(string $arquivo): PDO
    {
        if (self::$conexao === null) {
            $dados = parse_ini_file($arquivo, true);

            if ($dados === false) {
                throw new RuntimeException('Arquivo de configuração não encontrado.');
            }

            self::$conexao = self::criarConexao($dados['development']);
        }

        return self::$conexao;
    }

    private static function criarConexao(array $dados): PDO
    {
        $dsn = "pgsql:host={$dados['db_host']};";
        $dsn .= "port={$dados['db_port']};dbname={$dados['db_name']}";

        $opcoes = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        return new PDO(
            $dsn,
            $dados['db_user'],
            $dados['db_password'],
            $opcoes
        );
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
        throw new Exception('Não é permitido reconstruir a conexão.');
    }
}