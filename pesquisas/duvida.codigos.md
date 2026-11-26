 span: é uma tag de contêiner genérico em linha;

 http_response_code(405): indica que o servidor recebeu e reconheceu a requisição feita pelo navegador (ou cliente), mas o método HTTP utilizado não é permitido para aquela URL ou recurso específico.

 if (!$tremExiste) 
A expressão if (!$tremExiste) significa "se o trem NÃO existe" e serve para executar um bloco de código apenas quando uma condição específica for falsa 

mysqli_sql_exception
mysqli_sql_exception é uma classe nativa do PHP que representa um erro ocorrido durante uma operação com o banco de dados MySQL usando a extensão MySQLi. 

$erro->getMessage());
$erro->getMessage() recupera e retorna o texto descritivo da mensagem de um erro ou exceção que ocorreu durante a execução do código. 

ini_get
ini_get é uma função nativa do PHP que lê e retorna o valor atual de uma diretiva de configuração 

trim( )
O trim()
É uma função do PHP que remove os espaços em branco do início e do final de um texto. 

$params:   são valores ou variáveis passados para um sistema, função, rota ou requisição para definir como algo deve funcionar ou quais dados devem ser retornados/alterados.

in_array( )
A função in_array() verifica se o tipo de sensor informado existe dentro da lista de tipos permitidos. Utilizamos o ! para identificar quando o valor não está nessa lista e impedir o cadastro de informações inválidas. 

 setcookie
setcookie()  serve para armazenar pequenos dados no navegador do usuário e manter o estado entre requisições 

$schemaOk :define a estrutura, as regras ou a organização de dados em um sistema. 

fetch_assoc() 
O fetch_assoc() busca os registros retornados pelo banco de dados, um por vez, organizando as informações em um array associativo. Assim, conseguimos acessar cada informação pelo nome da coluna, como o ID e a identificação do trem, para preencher automaticamente as opções do formulário.

 $sensores = $conexao->query(
    "SELECT id, nome, identificacao, localizacao, tipo_sensor
     FROM sensores ORDER BY nome"
)->fetch_all(MYSQLI_ASSOC);
Esse código realiza uma consulta no banco de dados para buscar os sensores cadastrados na tabela sensores. Ele seleciona as informações de ID, nome, identificação, localização e tipo do sensor, organizando os resultados em ordem alfabética pelo nome. Em seguida, utiliza o fetch_all(MYSQLI_ASSOC) para armazenar todos os dados encontrados em um array associativo na variável $sensores. Essa função serve para facilitar a utilização e a exibição dos dados dos sensores no sistema, como em uma tabela HTML.

$ehAdministrador 
A variável $ehAdministrador verifica se o usuário logado é administrador, permitindo controlar o acesso a funcionalidades restritas do sistema. 

COUNT(*)
O COUNT(*) é uma função SQL que conta a quantidade total de registros (linhas) de uma tabela ou de uma consulta, servindo para saber quantos dados existem no banco de dados.

SUM 
O SUM() é uma função SQL que soma os valores de uma coluna numérica, servindo para calcular o total de determinados dados no banco de dados. 

round
O ROUND() é uma função que arredonda um valor numérico, servindo para deixar os resultados com uma quantidade específica de casas decimais. 

strlen() 
A função strlen() conta o tamanho do texto informado pelo usuário. Utilizamos ela para verificar se a identificação e o modelo do trem respeitam os limites de tamanho definidos no sistema, evitando cadastros com informações muito extensas. 

ENT_QUOTES
O ENT_QUOTES é uma opção do htmlspecialchars() que converte aspas simples e duplas em entidades HTML, ajudando a proteger o sistema contra a inserção de códigos maliciosos. 

 array_sum()
O array_sum() é uma função PHP que soma todos os valores numéricos de um array, servindo para calcular o total dos valores armazenados em uma lista. 

array_column() 
A função array_column() pega uma coluna específica de uma lista de dados. No nosso sistema, utilizamos ela para obter os IDs dos usuários cadastrados e, junto com in_array(), verificar se o usuário selecionado realmente existe no banco de dados. 

htmlspecialchars() 
A função htmlspecialchars() transforma caracteres especiais em entidades HTML, impedindo que conteúdos digitados pelos usuários sejam interpretados como códigos pelo navegador. Utilizamos ela para aumentar a segurança do sistema contra ataques XSS e garantir que as informações sejam exibidas como texto.

preg_match() 
O preg_match() verifica se o nome está dentro do padrão permitido. 

preg_replace() 
A função preg_replace() procura determinados caracteres em um texto e os substitui por outro conteúdo.
Remove caracteres indesejados e mantém apenas os números do telefone.

strtolower() 
A função strtolower() transforma todas as letras maiúsculas de um texto em letras minúsculas. 

fwrite() 
A função fwrite() escreve ou envia um conteúdo para um fluxo de saída. fgets() 
A função fgets() lê uma linha de informação de um fluxo de entrada. 
