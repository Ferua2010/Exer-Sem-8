# LISTA DE EXERCÍCIOS DE FIXAÇÃO E PRÁTICA
---
## CONEXÃO BANCO DE DADOS PDO

**1. O que é o PDO no PHP?**
PDO significa *PHP Data Objects*. Ele é uma extensão do PHP usada para conectar e trabalhar com bancos de dados.
O PDO é preferível porque possui uma interface padrão para vários bancos, como:
- PostgreSQL;
- MySQL;
- SQLite;
- SQL Server.

Assim, o código fica mais organizado e fácil de mudar para outro banco. Ele também oferece recursos de segurança, como *prepared statements*, que ajudam a evitar SQL Injection.

**2. O que é a string DSN?**
DSN significa *Data Source Name*. É uma string que informa ao PHP os dados necessários para encontrar e acessar o banco de dados.

Exemplo:
```php
$dsn = "pgsql:host=localhost;port=5432;dbname=escola";
```
Os parâmetros são:
- **host**: informa o endereço do servidor do banco de dados. `localhost` significa que o banco está no mesmo computador.
- **port**: informa a porta usada pelo PostgreSQL.
- **dbname**: informa o nome do banco de dados que será acessado.

**3. Qual é a porta padrão do PostgreSQL?**
A porta padrão do PostgreSQL é a `5432`. Ela aparece dentro da string DSN usando o parâmetro `port`.

Exemplo:
```php
$dsn = "pgsql:host=localhost;port=5432;dbname=escola";
```
Nesse exemplo, o PHP tentará acessar o PostgreSQL na porta 5432.

**4. O que acontece com PDO::ERRMODE_EXCEPTION?**
Quando usamos:
```php
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
```
o PDO lança uma exceção sempre que ocorre um erro na conexão ou em uma consulta. Isso permite tratar o erro usando `try...catch`:

```php
try {    
    $conexao = new PDO($dsn, $usuario, $senha);
} catch (PDOException $e) {    
    echo "Erro ao conectar ao banco.";
}
```
Em versões atuais do PHP, o modo de exceções é o padrão do PDO. Mesmo assim, é comum configurá-lo explicitamente para deixar o código mais claro. Sem esse modo, erros antigos do PDO poderiam apenas retornar `false` ou emitir avisos, dificultando a identificação do problema.

**5. Qual é a vantagem de usar PDO::FETCH_ASSOC?**
A configuração:
```php
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
```
faz com que os resultados sejam retornados como arrays associativos.

Exemplo:
```php
$aluno["nome"];
```
Em vez de retornar também índices numéricos duplicados, como:
```php
$aluno[0];
$aluno["nome"];
```
Isso evita dados repetidos no array e pode diminuir o uso de memória RAM, principalmente quando muitas linhas são consultadas. Também deixa o código mais fácil de entender.

**6. Por que não abrir uma conexão a cada consulta?**
Cada uso de:
```php
new PDO(...)
```
pode criar uma nova conexão com o banco de dados.
Se o sistema fizer isso muitas vezes, várias conexões ficarão abertas ao mesmo tempo. O PostgreSQL possui um limite definido por `max_connections`, que controla quantas conexões simultâneas o servidor aceita.

Quando esse limite é atingido:
- novas conexões podem ser recusadas;
- o sistema pode ficar lento;
- usuários podem receber erros.

Por isso, o padrão *Singleton* pode ser usado para reutilizar uma única conexão durante a execução da aplicação.

**7. Por que o construtor do Singleton é private?**
O construtor deve ser `private` para impedir que outras partes do programa criem objetos diretamente usando:
```php
new ConexaoBanco();
```
A classe passa a controlar a criação da única instância.

Exemplo:
```php
class ConexaoBanco {    
    private static ?PDO $instancia = null;    
    private function __construct() {}
}
```
Também é recomendado bloquear os métodos mágicos:
```php
private function __clone(){}

public function __wakeup(){    
    throw new Exception("Não é permitido desserializar esta classe.");
}
```
Esses métodos impedem:
- `__clone()`: criação de uma cópia do objeto;
- `__wakeup()`: criação de outra instância por desserialização.

Assim, a classe mantém a ideia de possuir apenas uma conexão.

**8. Por que não deixar usuário e senha no código?**
Não devemos salvar as credenciais diretamente nos scripts PHP porque o código pode:
- ser enviado acidentalmente para o GitHub;
- ser visto por outros desenvolvedores;
- aparecer em backups;
- ser exposto caso o servidor seja invadido.

Exemplo inseguro:
```php
$usuario = "postgres";
$senha = "123456";
```
O mais seguro é usar variáveis de ambiente ou um arquivo de configuração protegido e fora do controle de versão. Também é importante adicionar arquivos com informações secretas ao `.gitignore`:
```text
.env
config.php
```

**9. Por que não exibir $e->getMessage() diretamente?**
O código abaixo pode revelar informações importantes:
```php
catch (PDOException $e) {    
    echo $e->getMessage();
}
```
A mensagem pode mostrar:
- nome do banco de dados;
- endereço do servidor;
- nome de tabelas;
- comandos SQL;
- caminhos internos do servidor;
- informações sobre usuários ou permissões.

Isso é chamado de *Information Disclosure* (divulgação de informações). Essas informações podem ajudar uma pessoa mal-intencionada a atacar o sistema. Além disso, a aplicação deve evitar expor dados pessoais ou informações desnecessárias, respeitando princípios de segurança e privacidade da LGPD.

O ideal é mostrar uma mensagem simples para o usuário e registrar o erro em um arquivo seguro:
```php
try {    
    $conexao = new PDO($dsn, $usuario, $senha);
} catch (PDOException $e) {    
    error_log($e->getMessage());    
    echo "Não foi possível conectar ao banco de dados.";
}
```
Dessa forma, o usuário não vê detalhes técnicos, mas o responsável pelo sistema ainda pode analisar o erro.