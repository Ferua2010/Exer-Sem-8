<?php

declare(strict_types=1);

require_once 'conexao_banco.php';

const ARQUIVO_CONFIG = 'config/database.ini';

function testarConexao(): void
{
    try {
        $conexao = ConexaoBanco::obterConexao(ARQUIVO_CONFIG);
        $resultado = $conexao->query('SELECT version()');
        $versao = $resultado->fetchColumn();

        echo "Porta 5432 e banco acessíveis.\n";
        echo "Versão do PostgreSQL: {$versao}\n";
    } catch (PDOException $erro) {
        echo "Não foi possível conectar ao banco de dados.\n";
    } catch (RuntimeException $erro) {
        echo "{$erro->getMessage()}\n";
    }
}

testarConexao();