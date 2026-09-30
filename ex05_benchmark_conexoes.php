<?php

declare(strict_types=1);

require_once 'conexao_banco.php';

const ARQUIVO_CONFIG = 'config/database.ini';

function medirConexoesNovas(array $dados): array
{
    $inicio = microtime(true);
    $memoriaInicial = memory_get_usage();

    for ($i = 0; $i < 50; $i++) {
        $dsn = "pgsql:host={$dados['db_host']};";
        $dsn .= "port={$dados['db_port']};dbname={$dados['db_name']}";

        new PDO($dsn, $dados['db_user'], $dados['db_password']);
    }

    return [
        'tempo' => microtime(true) - $inicio,
        'memoria' => memory_get_usage() - $memoriaInicial
    ];
}

function medirSingleton(): array
{
    $inicio = microtime(true);
    $memoriaInicial = memory_get_usage();

    for ($i = 0; $i < 50; $i++) {
        ConexaoBanco::obterConexao(ARQUIVO_CONFIG);
    }

    return [
        'tempo' => microtime(true) - $inicio,
        'memoria' => memory_get_usage() - $memoriaInicial
    ];
}

function exibirTabela(array $novas, array $singleton): void
{
    echo '<table border="1">';
    echo '<tr><th>Método</th><th>Tempo</th><th>Memória</th></tr>';
    echo '<tr><td>50 novas conexões</td>';
    echo '<td>' . $novas['tempo'] . ' segundos</td>';
    echo '<td>' . $novas['memoria'] . ' bytes</td></tr>';
    echo '<tr><td>Singleton</td>';
    echo '<td>' . $singleton['tempo'] . ' segundos</td>';
    echo '<td>' . $singleton['memoria'] . ' bytes</td></tr>';
    echo '</table>';
}

function executarBenchmark(): void
{
    try {
        $dados = parse_ini_file(ARQUIVO_CONFIG, true)['development'];
        $novas = medirConexoesNovas($dados);
        $singleton = medirSingleton();

        exibirTabela($novas, $singleton);
    } catch (PDOException $erro) {
        echo 'Não foi possível executar o benchmark.';
    }
}

executarBenchmark();