<?php

declare(strict_types=1);

require_once 'conexao_banco.php';

const ARQUIVO_CONFIG = 'config/database.ini';

function testarIdentidade(): void
{
    try {
        $conexao1 = ConexaoBanco::obterConexao(ARQUIVO_CONFIG);
        $conexao2 = ConexaoBanco::obterConexao(ARQUIVO_CONFIG);

        if ($conexao1 === $conexao2) {
            echo "As conexões são o mesmo objeto.\n";
            echo 'SPL Object ID: ' . spl_object_id($conexao1) . "\n";
        } else {
            echo "As conexões são objetos diferentes.\n";
        }
    } catch (PDOException $erro) {
        echo "Não foi possível testar o Singleton.\n";
    }
}

testarIdentidade();