<?php

declare(strict_types=1);

require_once 'conexao_banco.php';

const ARQUIVO_CONFIG = 'config/database.ini';
const ARQUIVO_LOG = 'logs/sistema.log';

function registrarLog(string $nivel, string $mensagem): void
{
    $niveis = ['INFO', 'WARNING', 'ERROR'];

    if (!in_array($nivel, $niveis, true)) {
        return;
    }

    $data = date('Y-m-d H:i:s');
    $linha = "[{$data}] [{$nivel}] {$mensagem}" . PHP_EOL;

    file_put_contents(ARQUIVO_LOG, $linha, FILE_APPEND);
}

function executarTeste(): void
{
    try {
        ConexaoBanco::obterConexao(ARQUIVO_CONFIG);
        registrarLog('INFO', 'Conexão realizada com sucesso.');
        echo "Conexão realizada com sucesso.\n";
    } catch (PDOException $erro) {
        registrarLog('ERROR', $erro->getMessage());
        echo "Falha na conexão. Consulte o arquivo de log.\n";
    }
}

if (!is_dir('logs')) {
    mkdir('logs');
}

executarTeste();