### Pesquisa – PDO

## O que é o PDO:

PDO (PHP Data Objects) é uma extensão do PHP que permite ao programador acessar diferentes bancos de dados através de uma única interface. Isso significa que mesmo que em sua aplicação você tenha que lidar com mais de um banco de dados, como MySQL/SQL Server, poderá fazer consultas e disparar comandos utilizando as mesmas classes e métodos. Além diso, ele é um módulo de PHP montado sob o paradigma Orientado a Objetos, com o objetivo de prover uma padronização da forma com que PHP se comunica com um banco de dados relacional.

## Para que ele é utilizado no PHP:

O PDO (PHP Data Objects) é uma extensão do PHP usada para conectar aplicações a bancos de dados e executar operações SQL. Ele fornece uma interface padronizada, permitindo trabalhar com diferentes bancos de dados usando métodos semelhantes.
Antes do PDO, o PHP utilizava diferentes extensões e funções específicas para cada SGBD. Isso fazia com que a forma de realizar operações variasse entre os bancos, dificultando o desenvolvimento e a manutenção do código.
Com o PDO, o acesso ao banco fica mais organizado e consistente. Além disso, ele permite utilizar instruções preparadas, que separam os dados do comando SQL e ajudam a proteger a aplicação contra SQL Injection.

## Como funciona uma conexão utilizando PDO:

As conexões são estabelecidas criando instâncias da classe base PDO. Não importa qual driver você deseja usar; você sempre usa o nome da classe PDO. O construtor aceita parâmetros para especificar a fonte do banco de dados (conhecida como DSN) e opcionalmente para o nome de usuário e senha (se houver).Exemplo:
Conectando-se ao MySQL:

<?php
$dbh = new PDO('mysql:host=localhost;dbname=test', $user, $pass);
?>

## Quais são suas principais características:
-Suporte a vários bancos de dados.
-Prepared statements (declarações preparadas) que aumentam a segurança contra ataques de SQL Injection.
-Interface uniforme, o que significa que o código é facilmente adaptável para outro banco de dados.

## Diferenças entre PDO e MySQLi:
PDO e MySQLi permitem conectar o PHP ao MySQL, executar comandos SQL e utilizar instruções preparadas. A principal diferença é que o MySQLi é exclusivo para MySQL, enquanto o PDO possui uma interface que permite trabalhar com diferentes bancos de dados. Por isso, o PDO oferece maior portabilidade, enquanto o MySQLi pode ser interessante quando o projeto será utilizado especificamente com MySQL.


## Vantagens e desvantagens de utilizar PDO

O **PDO (PHP Data Objects)** é uma extensão do PHP que fornece uma interface padronizada para acessar bancos de dados. Ele permite trabalhar com diferentes SGBDs por meio de drivers específicos, utilizando uma estrutura de código semelhante.

### Vantagens

- **Portabilidade:** permite trabalhar com diferentes bancos de dados, como MySQL, PostgreSQL e SQLite.
- **Segurança:** possui suporte a Prepared Statements, que ajudam a proteger as aplicações contra ataques de SQL Injection.
- **Código organizado:** utiliza uma estrutura consistente para realizar conexões e consultas ao banco.
- **Tratamento de erros:** permite utilizar exceções, como `PDOException`, para identificar e tratar problemas.
- **Transações:** possui recursos para confirmar ou desfazer alterações realizadas no banco de dados.

### Desvantagens

- **Não possui todos os recursos específicos de cada banco:** alguns recursos exclusivos de determinados SGBDs podem não estar disponíveis diretamente.
- **Necessidade de drivers:** é necessário utilizar o driver correspondente ao banco de dados escolhido.
- **Aprendizado inicial:** alguns conceitos, como Prepared Statements, parâmetros e exceções, podem ser mais difíceis para iniciantes.
- **Menor acesso a recursos específicos do MySQL:** em comparação ao MySQLi, alguns recursos avançados específicos do MySQL podem exigir outras soluções.

---

## O que são Prepared Statements e por que são importantes?

**Prepared Statements**, ou **instruções preparadas**, são uma forma mais segura de executar comandos SQL utilizando parâmetros separados dos dados enviados pelo usuário.

Em vez de colocar diretamente uma informação recebida pelo usuário dentro da consulta SQL, utilizamos marcadores, como `:nome` ou `?`, e depois enviamos os valores separadamente.

### Exemplo:


$sql = "SELECT * FROM usuarios WHERE email = :email";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    'email' => $email
]);


## Links da Pesquisa

[PHP PDO: Como criar sua primeira conexão]("https://www.devmedia.com.br/php-pdo-como-criar-sua-primeira-conexao/39007")

[Tudo sobre o PHP Data Object PDO – Hospedagem de Sites]("https://www.locaweb.com.br/ajuda/wiki/tudo-sobre-o-php-data-object-pdo-hospedagem-de-sites/")

[Chamadas_de_Abstração/PDO]("https://www.php.net/manual/pt_BR/book.pdo.php")

[PHP – PDO vs MySQLi]("https://blog.grancursosonline.com.br/php-pdo-vs-mysqli/")

[MySQLi vs PDO - qual o mais recomendado para usar?]("https://pt.stackoverflow.com/questions/8302/mysqli-vs-pdo-qual-o-mais-recomendado-para-usar")