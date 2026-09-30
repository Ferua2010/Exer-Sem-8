<?php

declare(strict_types=1);

const ARQUIVO_CONFIG = 'config/database.ini';

function carregarAmbiente(string $ambiente): array
{
    $dados = parse_ini_file(ARQUIVO_CONFIG, true);

    if ($dados === false || !isset($dados[$ambiente])) {
        throw new RuntimeException('Ambiente não encontrado.');
    }

    return $dados[$ambiente];
}

function conectarAmbiente(array $dados): PDO
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

function executar(): void
{
    $ambiente = $GLOBALS['argv'][1] ?? 'development';

    try {
        $dados = carregarAmbiente($ambiente);
        conectarAmbiente($dados);

        echo "Conectado ao ambiente {$ambiente}.\n";
    } catch (PDOException $erro) {
        echo "Não foi possível conectar ao ambiente {$ambiente}.\n";
    } catch (RuntimeException $erro) {
        echo "{$erro->getMessage()}\n";
    }
}

executar();