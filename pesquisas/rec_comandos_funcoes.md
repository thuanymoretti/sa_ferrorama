### Pesquisa – Comandos e funções PHP e SQL

Material de revisão sobre os comandos, funções e estruturas utilizados
no desenvolvimento de sistemas com PHP e MySQL.



## Folha 1 -- Comandos e funções

### 1. Arrays

**Array** é uma estrutura que permite armazenar vários valores dentro de
uma única variável. Cada valor pode ser acessado por uma posição
(índice) ou por uma chave.

Exemplo:

```php
$tipos = ["Temperatura", "Pressão", "Velocidade"];
```

Nesse exemplo, o array `$tipos` armazena três tipos de sensores.

#### Condições e verificações

-   `!$id` → verifica se o valor de `$id` é considerado falso pelo PHP.
    Isso pode acontecer, por exemplo, quando o valor é `0`, `false`,
    `null`, `""` ou `"0"`.
-   `$id < 1` → verifica se o ID é menor que 1. Pode ser utilizado para
    impedir o uso de IDs inválidos.
-   `$nome == ""` → verifica se o nome é igual a uma string vazia.
-   `strlen($nome) > 60` → verifica se o tamanho do texto em `$nome`
    ultrapassa 60 bytes. Em textos com acentos ou caracteres especiais
    codificados em UTF-8, `strlen()` pode contar mais bytes do que
    caracteres; para contar caracteres, pode-se usar `mb_strlen()`.
-   `$identificacao == ""` → verifica se a identificação está vazia.
-   `strlen($identificacao) > 50` → verifica se o tamanho da
    identificação ultrapassa 50 bytes. Se a intenção for contar
    caracteres em UTF-8, pode-se utilizar `mb_strlen()`.
-   `in_array($tipo, $tipos, true)` → verifica se o valor de `$tipo`
    existe dentro do array `$tipos`. O terceiro argumento, `true`, faz
    uma comparação estrita, considerando também o tipo do valor.

### 2. Operadores lógicos

Os operadores lógicos são utilizados para combinar condições ou inverter
o resultado de uma condição.

  -----------------------------------------------------------------------
  Operador                Significado             Explicação
  ----------------------- ----------------------- -----------------------
  `!`                     NÃO (negação)           Inverte o resultado de
                                                  uma condição.

  `||`                    OU                      Retorna verdadeiro
                                                  quando pelo menos uma
                                                  das condições é
                                                  verdadeira.

  `&&`                    E                       Retorna verdadeiro
                                                  somente quando todas as
                                                  condições são
                                                  verdadeiras.
  -----------------------------------------------------------------------

#### Exemplos

```php
!$id
```

Verifica se o ID possui um valor considerado falso.

```php
$id < 1 || $nome == ""
```

Verifica se o ID é menor que 1 **OU** se o nome está vazio.

```php
$id > 0 && $nome != ""
```

Verifica se o ID é maior que zero **E** se o nome não está vazio.

### 3. Funções e comandos PHP

-   `strlen()` → retorna o tamanho de uma string em bytes. É utilizada
    para verificar o tamanho de textos. Para contar caracteres
    corretamente em textos UTF-8, pode-se utilizar `mb_strlen()`.

-   `in_array()` → verifica se determinado valor existe dentro de um
    array. Retorna `true` se encontrar e `false` se não encontrar.

-   `define()` → cria uma constante, ou seja, um valor que não deve ser
    alterado durante a execução do programa.

    Exemplo:

    ```php
    define('MENSAGEM', 'Cadastro realizado!');
    ```

    **Atenção:** `define()` não armazena mensagens temporárias
    automaticamente. Mensagens flash costumam ser implementadas com
    sessões, por exemplo, utilizando `$_SESSION`, e são exibidas
    normalmente após um redirecionamento.

-   `exit()` → interrompe imediatamente a execução do script. Pode ser
    utilizado quando ocorre um erro ou quando uma condição necessária
    não é atendida.

-   `try` → inicia um bloco de código no qual podem ocorrer exceções.
    Essas exceções podem ser tratadas por um bloco `catch`.

-   `if ($stream !== null)` → verifica se a variável `$stream` possui um
    valor diferente de `null`. O significado de `$stream` depende do
    contexto em que a variável foi criada.

-   `prepare()` → prepara uma consulta SQL antes de sua execução. Quando
    utilizada corretamente com parâmetros, ajuda a evitar SQL Injection.

-   `?` → funciona como um espaço reservado (*placeholder*) em uma
    consulta preparada. O valor correspondente é informado
    posteriormente.

    Exemplo:

    ```php
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    ```

-   `bind_param()` → no MySQLi, associa variáveis aos espaços reservados
    de uma consulta preparada.

-   `i`, `d`, `s` e `b` → são identificadores de tipos usados no
    primeiro argumento de `bind_param()`:

    -   `i` → inteiro (*integer*);
    -   `d` → número decimal (*double*);
    -   `s` → texto (*string*);
    -   `b` → dado binário (*blob*).

-   `isset($stream)` → verifica se a variável está definida e se seu
    valor é diferente de `null`.

**Observação:** `isset()` e `$stream !== null` não são exatamente a
mesma verificação. `isset()` também retorna `false` quando a variável
não está definida; a comparação `!== null` compara o valor da variável
com `null`.

### 4. SQL -- comando UPDATE

O comando `UPDATE` é utilizado para modificar informações que já estão
cadastradas em uma tabela do banco de dados.

Exemplo:

``` sql
UPDATE sensores
SET nome = ?, identificacao = ?
WHERE id = ?;
```

#### Significado de cada parte

-   `UPDATE sensores` → indica que os registros da tabela `sensores`
    serão atualizados.
-   `SET` → informa quais campos receberão os novos valores.
-   `WHERE` → define quais registros serão modificados, de acordo com a
    condição informada.

#### Tipos utilizados no `bind_param()`

-   `s` → texto (*string*).
-   `i` → número inteiro (*integer*).
-   `d` → número decimal (*double*).
-   `b` → dado binário (*blob*).

**Importante:** se o `UPDATE` for executado sem `WHERE`, todos os
registros da tabela poderão ser alterados. Por isso, é necessário
conferir a condição antes de executar a consulta.

### 5. Tratamento de erros

O tratamento de erros permite identificar problemas durante a execução
do programa e lidar com eles de maneira controlada.

-   `catch` → captura e trata uma exceção lançada durante a execução do
    bloco `try`.
-   `error_log()` → registra uma mensagem no log de erros do servidor,
    permitindo que o desenvolvedor investigue o problema.
-   `$erro->getMessage()` → retorna a mensagem descritiva da exceção
    ocorrida.

Exemplo:

```php
try {
    // Código que pode apresentar uma exceção.
} catch (Exception $erro) {
    error_log($erro->getMessage());
}
```

Nesse exemplo, se uma exceção do tipo tratado ocorrer no bloco `try`, o
`catch` será responsável por capturá-la e registrar sua mensagem no log.

### 6. Funções e métodos HTTP

-   **Função do arquivo:** depende da finalidade do arquivo PHP. Por
    exemplo, `atualizar_sensor.php` pode receber dados de um formulário,
    validar as informações e atualizar o cadastro no banco de dados.
-   `POST` → método HTTP utilizado para enviar informações ao servidor.
    Os dados são enviados no corpo da requisição e esse método é
    bastante utilizado em formulários de cadastro e atualização.
-   `GET` → método HTTP utilizado principalmente para solicitar
    informações ao servidor. Seus parâmetros geralmente são enviados
    pela URL.

#### Diferença entre POST e GET

  -----------------------------------------------------------------------
  POST                                GET
  ----------------------------------- -----------------------------------
  Envia dados no corpo da requisição. Envia parâmetros geralmente pela
                                      URL.

  É bastante utilizado em cadastros e É bastante utilizado em pesquisas e
  alterações.                         consultas.

  Os dados enviados no corpo não      Os parâmetros ficam visíveis na
  aparecem diretamente na URL.        URL.
  -----------------------------------------------------------------------

**Atenção:** utilizar POST não significa que os dados estão
criptografados ou protegidos. Para proteger a comunicação, é necessário
utilizar HTTPS e aplicar outras medidas de segurança, como validação dos
dados e verificação de permissões.



## Folha 2 -- Revisão

### 1. Atualizar senha

A atualização de senha é responsável por receber os dados de um usuário,
verificar se as informações são válidas e, caso estejam corretas, salvar
a nova senha no banco de dados.

#### Etapas comuns

1.  Verificar se o usuário está autenticado e possui permissão para
    realizar a alteração.
2.  Receber os dados enviados pelo formulário.
3.  Validar as informações recebidas.
4.  Gerar o hash da nova senha utilizando `password_hash()`.
5.  Atualizar a senha no banco de dados.

**Importante:** as senhas não devem ser armazenadas diretamente como
texto. O correto é utilizar funções próprias para armazenamento seguro
de senhas, como `password_hash()`. Para conferir uma senha
posteriormente, utiliza-se `password_verify()`.

### 2. Arquivos e autenticação

-   `require_once` → inclui outro arquivo PHP dentro do código. O
    arquivo será incluído apenas uma vez durante a execução, evitando
    inclusões duplicadas.
-   `../` → indica que o caminho deve voltar uma pasta na estrutura de
    diretórios.
-   `infra/admin.php` → representa um arquivo localizado dentro da pasta
    `infra`. Pelo nome, pode conter funções relacionadas à administração
    e à verificação de permissões; a finalidade exata depende do código
    desse arquivo.

**Atenção à segurança:** apenas incluir um arquivo de autenticação não
garante que a página esteja protegida. É necessário executar as funções
responsáveis por verificar se o usuário está logado e se possui a
permissão necessária.

**Acesso à página de login:** o sistema de login autentica o usuário,
verificando suas credenciais. Caso ele já esteja autenticado,
normalmente não é necessário permitir que acesse novamente a página de
login. Porém, estar logado não significa automaticamente possuir
permissão de administrador.

### 3. Variáveis e métodos

-   `$_SERVER` → variável superglobal do PHP que armazena informações
    sobre o servidor e a requisição atual.
-   `REQUEST_METHOD` → índice de `$_SERVER` que informa qual método HTTP
    foi utilizado na requisição, como `GET` ou `POST`.
-   `!==` → operador de comparação estrita que verifica se dois valores
    são diferentes em valor ou em tipo.
-   `POST` → método HTTP utilizado para enviar informações ao servidor,
    normalmente por meio de formulários.

### 4. Verificação do método POST

Exemplo de código:

```php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método inválido');
}
```

#### Explicação de cada comando

-   `$_SERVER['REQUEST_METHOD']` → identifica o método utilizado na
    requisição.
-   `!== 'POST'` → verifica se o método utilizado é diferente de POST.
-   `http_response_code(405)` → define o código de resposta HTTP 405,
    que significa que o método utilizado não é permitido para aquele
    recurso.
-   `exit()` → interrompe a execução do código e apresenta a mensagem
    informada.

**Resumindo:** se a requisição não for enviada pelo método POST, o
sistema interrompe a execução e informa que o método utilizado não é
permitido.

Essa verificação é importante porque impede que uma operação destinada a
receber dados por POST seja executada por outro método HTTP. Ela não
substitui a validação dos dados nem a verificação de autenticação e
permissões.

### 5. Segurança e validação

São recursos utilizados para verificar os dados recebidos e proteger o
sistema contra requisições inválidas ou mal-intencionadas.

-   `validate_token_csrf()` → parece ser uma função personalizada do
    projeto, responsável por verificar o token CSRF. Esse mecanismo
    ajuda a impedir que outros sites provoquem requisições indesejadas
    em nome de um usuário autenticado.

-   `valor_post('id')` → parece ser uma função personalizada do projeto,
    responsável por recuperar o valor enviado pelo formulário por meio
    do método POST, utilizando o campo `id`. Sua implementação exata
    depende do código da função.

-   `filter_var()` → verifica ou filtra um valor de acordo com uma regra
    definida. Pode ser utilizada, por exemplo, para validar e-mails e
    números inteiros.

    Exemplo:

    ```php
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    ```

    Nesse caso, verifica se o valor recebido é um número inteiro válido.
    Se não for válido, a função retorna `false`.

-   `trim()` → remove espaços em branco do início e do final de uma
    string.

    Exemplo:

    ```php
    $nome = trim($_POST['nome']);
    ```

    Se o usuário digitar `" Maria "`, o resultado será `"Maria"`.

### 6. Valores

-   `null` → representa a ausência de um valor. Em PHP, uma variável
    pode estar definida e possuir o valor `null`.
-   `""` → representa uma string vazia, ou seja, um texto sem nenhum
    caractere.

#### Diferença

```php
$nome = null;
$nome = "";
```

No primeiro caso, a variável possui valor nulo. No segundo, ela possui
uma string, mas sem conteúdo.

### 7. Operadores de comparação

São utilizados para comparar valores e verificar se determinadas
condições são verdadeiras ou falsas.

  -----------------------------------------------------------------------
  Operador                Significado             Explicação
  ----------------------- ----------------------- -----------------------
  `===`                   Igualdade estrita       Compara o valor e o
                                                  tipo dos dados.

  `==`                    Igualdade               Compara os valores,
                                                  permitindo conversões
                                                  de tipo.

  `=`                     Atribuição              Atribui um valor a uma
                                                  variável.

  `!==`                   Diferença estrita       Verifica se os valores
                                                  ou os tipos são
                                                  diferentes.

  `!=`                    Diferente               Verifica se os valores
                                                  são diferentes,
                                                  permitindo conversões
                                                  de tipo.

  `>`                     Maior que               Verifica se um valor é
                                                  maior que outro.

  `<`                     Menor que               Verifica se um valor é
                                                  menor que outro.

  `>=`                    Maior ou igual          Verifica se um valor é
                                                  maior ou igual a outro.

  `<=`                    Menor ou igual          Verifica se um valor é
                                                  menor ou igual a outro.
  -----------------------------------------------------------------------

#### Exemplos práticos

```php
5 === "5"; // false
5 == "5";  // true
$id = 10;  // atribui o valor 10 à variável
5 !== "5"; // true
```

A principal diferença é que `===` e `!==` verificam também o tipo do
dado, enquanto `==` e `!=` permitem conversões de tipo durante a
comparação.



### Resumo dos pontos de atenção

-   `define()` cria constantes; não cria mensagens flash
    automaticamente.
-   `isset()` verifica se uma variável está definida e não é `null`.
-   `POST` não criptografa os dados; HTTPS é necessário para proteger a
    comunicação.
-   `UPDATE` sem `WHERE` pode alterar todos os registros da tabela.
-   `prepare()` e `bind_param()` são utilizados em consultas preparadas
    com MySQLi.
-   Estar autenticado não significa necessariamente ter permissão de
    administrador.
-   `strlen()` conta bytes, enquanto `mb_strlen()` pode ser utilizada
    para contar caracteres em textos UTF-8.
-   `validate_token_csrf()` e `valor_post()` parecem ser funções
    próprias do projeto; sua implementação deve ser consultada para
    confirmar exatamente o que fazem.
