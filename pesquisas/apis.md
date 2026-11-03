# PESQUISA: APIs (Application Programming Interfaces)

## 1. Introdução

Atualmente, os sistemas e aplicativos precisam trocar informações para realizar diferentes funções. Um aplicativo de banco, por exemplo, precisa acessar informações financeiras, enquanto um aplicativo de previsão do tempo precisa receber dados sobre temperatura e condições climáticas.

Para que essa comunicação aconteça de maneira organizada, são utilizadas as **APIs (Application Programming Interfaces)**, que permitem a integração entre diferentes sistemas e aplicações.

As APIs são muito importantes no desenvolvimento de sistemas, pois facilitam o compartilhamento de informações, a automatização de processos e a criação de aplicações mais completas.

## 2. O que são APIs?

API é a sigla para *Application Programming Interface*, que significa Interface de Programação de Aplicações.

Uma API é um conjunto de regras e definições que permite que diferentes programas se comuniquem e troquem informações. Ela funciona como uma intermediária entre sistemas, estabelecendo como uma aplicação pode solicitar informações ou utilizar determinados recursos de outra.

Por exemplo, quando um aplicativo precisa consultar a previsão do tempo, ele pode enviar uma solicitação para uma API responsável por fornecer informações meteorológicas. A API recebe essa solicitação, consulta os dados necessários e devolve uma resposta para o aplicativo.

É importante entender que uma API não é necessariamente um programa completo. Ela funciona como uma interface que disponibiliza recursos e estabelece regras para que outros sistemas possam utilizá-los.

## 3. Para que servem as APIs?

As APIs são utilizadas para conectar aplicações, compartilhar dados e permitir que diferentes tecnologias trabalhem em conjunto.

Entre suas principais finalidades estão:

- **Integração de sistemas:** permite que diferentes aplicações se comuniquem.
- **Compartilhamento de informações:** facilita a troca de dados entre programas.
- **Automatização de tarefas:** permite executar operações sem a necessidade de realizar tudo manualmente.
- **Reutilização de funcionalidades:** possibilita utilizar recursos já desenvolvidos por outros sistemas.
- **Agilidade no desenvolvimento:** evita que determinadas funcionalidades precisem ser criadas novamente.
- **Comunicação entre plataformas:** permite que sistemas desenvolvidos em linguagens diferentes troquem informações.

## 4. Como funciona uma API?

O funcionamento de uma API geralmente acontece por meio de uma comunicação entre cliente e servidor.

O **cliente** é o sistema que realiza uma solicitação, enquanto o **servidor** é responsável por receber essa solicitação, processá-la e devolver uma resposta.

O processo pode ser dividido em quatro etapas:

1. **Solicitação:** o cliente envia um pedido para a API.
2. **Recebimento:** o servidor recebe a solicitação e verifica o que precisa ser feito.
3. **Processamento:** o servidor executa a operação necessária, podendo consultar ou alterar informações no banco de dados.
4. **Resposta:** a API devolve o resultado para o cliente.

### Exemplo de funcionamento

Imagine um sistema que precisa consultar os dados de um sensor de temperatura.

1. O sistema envia uma solicitação para a API.
2. A API recebe o pedido e consulta o banco de dados.
3. O banco de dados retorna as informações do sensor.
4. A API organiza os dados e envia uma resposta.
5. O sistema apresenta as informações ao usuário.

### Representação do processo

```text
CLIENTE
   |
   | Solicitação
   v
API
   |
   | Consulta
   v
BANCO DE DADOS
   |
   | Retorno dos dados
   v
API
   |
   | Resposta
   v
CLIENTE
```

## 5. APIs REST

Uma das formas mais utilizadas para desenvolver APIs é a arquitetura REST, sigla para *Representational State Transfer*.

REST é um estilo de arquitetura que define princípios para organizar a comunicação entre sistemas por meio de requisições HTTP.

As APIs que seguem esses princípios são chamadas de **APIs RESTful**.

Elas geralmente utilizam URLs para identificar recursos e métodos HTTP para indicar qual operação deve ser realizada.

Por exemplo:

```text
https://api.exemplo.com/sensores
```

Nesse endereço, `sensores` representa o recurso que pode ser consultado ou manipulado pela API.

Entre as principais características de uma API REST estão:

- **Comunicação por HTTP:** utiliza métodos e recursos do protocolo HTTP.
- **Cliente e servidor:** separa as responsabilidades do sistema que solicita e do sistema que fornece os dados.
- **Stateless:** cada requisição deve conter as informações necessárias para ser compreendida, sem depender de um estado de comunicação armazenado entre requisições.
- **Identificação de recursos:** utiliza URLs para identificar os recursos disponíveis.
- **Representação de dados:** pode utilizar formatos como JSON para transmitir informações.

## 6. Métodos HTTP utilizados em APIs

Os métodos HTTP indicam qual operação o cliente deseja realizar em determinado recurso.

Os métodos mais utilizados em APIs são:

| Método | Função | Exemplo |
| :--- | :--- | :--- |
| `GET` | Consultar informações. | Listar todos os sensores. |
| `POST` | Cadastrar ou enviar informações. | Cadastrar um novo sensor. |
| `PUT` | Substituir completamente um recurso. | Atualizar todos os dados de um sensor. |
| `PATCH` | Alterar parcialmente um recurso. | Alterar apenas a localização do sensor. |
| `DELETE` | Excluir um recurso. | Excluir um sensor. |

### Exemplo prático

Imagine uma API responsável pelo gerenciamento de sensores de um sistema ferroviário.

```http
GET /sensores
```

Consulta todos os sensores cadastrados.

```http
POST /sensores
```

Envia os dados para cadastrar um novo sensor.

```http
PUT /sensores/1
```

Substitui os dados do sensor de identificação 1.

```http
PATCH /sensores/1
```

Altera apenas algumas informações do sensor 1.

```http
DELETE /sensores/1
```

Solicita a exclusão do sensor 1.

## 7. Requisições e respostas HTTP

A comunicação entre o cliente e a API acontece por meio de requisições e respostas HTTP.

Uma requisição HTTP contém informações como:

- **Método:** indica a operação desejada, como GET ou POST.
- **URL:** identifica o endereço do recurso.
- **Cabeçalhos (headers):** apresentam informações adicionais sobre a comunicação.
- **Corpo (body):** contém dados enviados ao servidor, quando necessário.

A resposta HTTP é enviada pelo servidor e pode conter:

- **Código de status:** informa o resultado da solicitação.
- **Cabeçalhos:** apresentam informações sobre a resposta.
- **Corpo:** contém os dados retornados pela API, quando houver.

### Principais códigos de status HTTP

| Código | Significado | Explicação |
| :--- | :--- | :--- |
| `200` | OK | A solicitação foi realizada com sucesso. |
| `201` | Created | Um novo recurso foi criado. |
| `204` | No Content | A operação foi concluída sem conteúdo na resposta. |
| `400` | Bad Request | A solicitação apresenta informações inválidas. |
| `401` | Unauthorized | É necessário realizar a autenticação. |
| `403` | Forbidden | O acesso ao recurso não é permitido. |
| `404` | Not Found | O recurso solicitado não foi encontrado. |
| `500` | Internal Server Error | Ocorreu um erro interno no servidor. |

Os códigos ajudam os sistemas a identificar se uma operação foi realizada corretamente ou se aconteceu algum problema durante a comunicação.

## 8. Endpoints

Um endpoint é um endereço específico disponibilizado por uma API para acessar determinado recurso ou executar uma operação.

Cada endpoint representa uma possibilidade de interação com o sistema.

Por exemplo:

```text
https://api.exemplo.com/sensores
https://api.exemplo.com/sensores/1
https://api.exemplo.com/trens
https://api.exemplo.com/viagens
```

Nesse exemplo:

- `/sensores` permite acessar os sensores.
- `/sensores/1` identifica um sensor específico.
- `/trens` permite acessar os trens.
- `/viagens` permite acessar as viagens.

Um mesmo endpoint pode aceitar diferentes métodos HTTP. Por exemplo, o endereço `/sensores` pode ser utilizado com GET para consultar informações e com POST para cadastrar um novo sensor.

## 9. JSON: formato de troca de informações

JSON é a sigla para *JavaScript Object Notation*. É um formato leve de representação e troca de dados, muito utilizado na comunicação entre APIs.

Apesar de ter sido inspirado na sintaxe do JavaScript, o JSON pode ser utilizado em diferentes linguagens de programação, como PHP, Java, Python e JavaScript.

Ele organiza as informações em pares de chave e valor, utilizando uma estrutura simples e fácil de interpretar.

### Exemplo de JSON

```json
{
    "id": 1,
    "nome": "Sensor de Temperatura",
    "tipo": "Temperatura",
    "localizacao": "Vagão 01",
    "temperatura": 28.5,
    "status": "Ativo"
}
```

Nesse exemplo:

- `id` representa a identificação do sensor.
- `nome` representa o nome do sensor.
- `tipo` informa a categoria do sensor.
- `localizacao` indica onde ele está instalado.
- `temperatura` apresenta um valor de exemplo.
- `status` informa a situação do sensor.

O JSON facilita a comunicação porque possui uma estrutura organizada, que pode ser interpretada por diferentes sistemas.

## 10. Diferença entre API e sistema tradicional

Uma API e um sistema tradicional não são exatamente a mesma coisa.

Um sistema tradicional pode apresentar páginas e funcionalidades diretamente para o usuário, enquanto uma API disponibiliza recursos e informações para serem utilizados por outros sistemas.

| Sistema tradicional | API |
| :--- | :--- |
| Geralmente apresenta uma interface visual para o usuário. | Geralmente disponibiliza dados e funcionalidades para outros programas. |
| Pode utilizar formulários, botões e páginas. | Utiliza endpoints e requisições. |
| O usuário interage diretamente com a interface. | Outro sistema realiza a comunicação. |
| Pode apresentar informações em páginas HTML. | Frequentemente utiliza formatos como JSON. |

É importante destacar que um sistema pode possuir uma interface tradicional e também disponibilizar uma API para integração com outras aplicações.

## 11. Como utilizar APIs com PHP?

O PHP permite trabalhar com APIs tanto para consumir informações de outros serviços quanto para desenvolver uma API própria.

Uma das ferramentas utilizadas para realizar requisições HTTP no PHP é o **cURL**, que permite estabelecer comunicação com servidores utilizando protocolos como HTTP e HTTPS.

A documentação oficial do PHP apresenta as funcionalidades dessa biblioteca e exemplos de utilização.

### 11.1. Consumindo uma API com GET

O método GET é utilizado para consultar informações disponibilizadas por uma API.

Exemplo de código PHP:

```php
<?php

// Endereço da API
$url = "https://api.exemplo.com/sensores";

// Inicia a conexão
$curl = curl_init($url);

// Configura a requisição
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Executa a requisição
$resposta = curl_exec($curl);

// Verifica se ocorreu algum erro
if ($resposta === false) {
    echo "Erro na comunicação com a API.";
} else {

    // Converte o JSON em dados PHP
    $dados = json_decode($resposta, true);

    // Exibe os dados recebidos
    print_r($dados);
}

// Encerra a conexão
curl_close($curl);

?>
```

Nesse exemplo, o PHP acessa o endereço da API, recebe as informações e utiliza a função `json_decode()` para transformar o conteúdo JSON em uma estrutura que pode ser utilizada no código.

### 11.2. Enviando informações com POST

O método POST é utilizado para enviar informações para uma API, como no cadastro de um novo sensor.

Exemplo:

```php
<?php

// Endereço da API
$url = "https://api.exemplo.com/sensores";

// Informações que serão enviadas
$dados = [
    "nome" => "Sensor de Temperatura",
    "tipo" => "Temperatura",
    "localizacao" => "Vagão 01"
];

// Converte os dados para JSON
$json = json_encode($dados);

// Inicia a conexão
$curl = curl_init($url);

// Configura a requisição
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Define o formato dos dados
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

// Executa a requisição
$resposta = curl_exec($curl);

// Exibe a resposta
echo $resposta;

// Encerra a conexão
curl_close($curl);

?>
```

Nesse caso, o PHP transforma as informações em JSON e envia os dados para a API utilizando o método POST.

**Observação:** os endereços utilizados nos exemplos são fictícios. Para funcionar de verdade, é necessário utilizar uma API existente ou desenvolver uma API própria.

## 12. Como desenvolver uma API em PHP?

Além de consumir APIs, também é possível criar uma API utilizando PHP.

Uma API própria pode disponibilizar informações armazenadas em um banco de dados para que outros sistemas consigam consultá-las.

Por exemplo, um sistema pode possuir uma tabela chamada `sensores` no MySQL. Uma API desenvolvida em PHP pode consultar essa tabela e retornar os registros em formato JSON.

### Exemplo de API para consultar sensores

```php
<?php

// Conecta ao banco de dados
require_once "../infra/conexao.php";

// Define que a resposta será em JSON
header("Content-Type: application/json; charset=utf-8");

// Consulta os sensores cadastrados
$sql = "SELECT id, nome, identificacao, tipo_sensor,
        localizacao, dados_adicionais, trem_id
        FROM sensores";

$resultado = mysqli_query($conexao, $sql);

// Verifica se a consulta foi realizada
if (!$resultado) {

    http_response_code(500);

    echo json_encode([
        "erro" => "Não foi possível consultar os sensores."
    ]);

    exit;
}

// Cria uma lista para armazenar os sensores
$sensores = [];

// Percorre os resultados da consulta
while ($sensor = mysqli_fetch_assoc($resultado)) {

    $sensores[] = $sensor;

}

// Retorna os dados em JSON
echo json_encode($sensores, JSON_UNESCAPED_UNICODE);

?>
```

### Explicação do código

1. `require_once` realiza a conexão com o banco de dados.
2. `header()` informa que a resposta será enviada no formato JSON.
3. A variável `$sql` armazena a consulta SQL.
4. `mysqli_query()` executa a consulta no banco de dados.
5. `mysqli_fetch_assoc()` recupera os registros em formato de array associativo.
6. `json_encode()` transforma os dados em JSON.
7. O resultado é enviado como resposta da API.

Esse exemplo representa uma implementação inicial de consulta. Para uma API utilizada em produção, também é necessário implementar controle de acesso, validação de dados, tratamento de erros e outras medidas de segurança.

## 13. Segurança em APIs

A segurança é uma parte fundamental do desenvolvimento de APIs, principalmente quando elas trabalham com informações pessoais, financeiras ou dados importantes de uma empresa.

Uma API sem proteção adequada pode permitir que pessoas não autorizadas consultem, alterem ou excluam informações.

Entre as principais medidas de segurança estão:

- **Autenticação:** verifica a identidade de quem está acessando a API.
- **Autorização:** determina quais recursos e operações cada usuário pode acessar.
- **HTTPS:** protege a comunicação entre o cliente e o servidor por meio de criptografia.
- **Validação de dados:** verifica se as informações recebidas estão corretas.
- **Controle de acesso:** impede que usuários realizem operações sem permissão.
- **Proteção de informações:** evita a exposição de senhas e dados sensíveis.
- **Limitação de requisições:** ajuda a evitar o uso excessivo dos recursos do servidor.

A OWASP, organização reconhecida na área de segurança de aplicações, publica o documento *OWASP API Security Top 10*, que apresenta riscos importantes relacionados à segurança de APIs.

Entre os problemas destacados estão falhas de autenticação, autorização inadequada, configurações incorretas e consumo excessivo de recursos.

## 14. Exemplos de utilização das APIs no cotidiano

As APIs estão presentes em diversas atividades realizadas diariamente, mesmo quando os usuários não percebem sua utilização.

| Área | Exemplo de utilização |
| :--- | :--- |
| Bancos | Integração de sistemas de pagamento e consulta de informações financeiras. |
| Transporte | Compartilhamento de informações sobre localização e trajetos. |
| Comércio eletrônico | Integração com serviços de pagamento e controle de pedidos. |
| Redes sociais | Compartilhamento de conteúdos e integração com outros aplicativos. |
| Previsão do tempo | Consulta de dados meteorológicos. |
| Educação | Integração entre plataformas de ensino e sistemas escolares. |
| Saúde | Compartilhamento autorizado de informações entre sistemas de saúde. |

Esses exemplos demonstram como as APIs facilitam a integração de diferentes serviços e tornam os sistemas mais conectados.

## 15. Aplicação das APIs no projeto Ferrorama

No projeto Ferrorama, que tem como objetivo desenvolver um sistema de monitoramento e gerenciamento ferroviário, as APIs podem ser utilizadas para facilitar a comunicação entre diferentes partes do sistema.

O projeto possui informações relacionadas a usuários, trens, sensores e viagens, armazenadas em um banco de dados MySQL.

Uma API poderia disponibilizar essas informações para outros sistemas, permitindo que os dados fossem consultados sem que fosse necessário acessar diretamente o banco de dados.

### Exemplos de utilização no Ferrorama

| Endpoint | Método HTTP | Funcionalidade |
| :--- | :--- | :--- |
| `/sensores` | GET | Consultar todos os sensores cadastrados. |
| `/sensores/1` | GET | Consultar um sensor específico. |
| `/sensores` | POST | Cadastrar um novo sensor. |
| `/sensores/1` | PUT | Atualizar os dados de um sensor. |
| `/sensores/1` | DELETE | Excluir um sensor. |
| `/trens` | GET | Consultar os trens cadastrados. |
| `/viagens` | GET | Consultar as viagens registradas. |

### Exemplo prático

Imagine que o Ferrorama precise disponibilizar os dados de um sensor para um painel externo de monitoramento.

A API poderia receber uma solicitação:

```http
GET /sensores/1
```

E retornar uma resposta semelhante a esta:

```json
{
    "id": 1,
    "nome": "Sensor Principal",
    "identificacao": "SEN001",
    "tipo_sensor": "Temperatura",
    "localizacao": "Vagão 01",
    "trem_id": 2
}
```

Com essas informações, outro sistema poderia apresentar os dados do sensor em uma tela de monitoramento.

Dessa maneira, as APIs poderiam contribuir para a integração do Ferrorama com outras plataformas, facilitando o acesso às informações e possibilitando futuras expansões do projeto.

**Importante:** esse exemplo demonstra uma possível aplicação da tecnologia no Ferrorama. Ele não significa que o sistema atual já possui uma API implementada.

## 16. Vantagens e desafios das APIs

As APIs oferecem diversas vantagens para o desenvolvimento de sistemas, mas também apresentam desafios que precisam ser considerados durante sua implementação.

### Vantagens

- Facilitam a integração entre diferentes sistemas.
- Permitem compartilhar informações de maneira organizada.
- Reduzem a necessidade de desenvolver funcionalidades repetidas.
- Ajudam na automatização de processos.
- Permitem ampliar as funcionalidades de uma aplicação.
- Facilitam a comunicação entre diferentes linguagens de programação.

### Desafios

- Exigem cuidados com a segurança das informações.
- Precisam de documentação para facilitar sua utilização.
- Podem apresentar falhas de comunicação.
- Dependem da disponibilidade do servidor que fornece os dados.
- Necessitam de manutenção e atualizações.
- Precisam controlar corretamente o acesso aos recursos.

## 17. Documentação de APIs

A documentação de uma API apresenta informações sobre como ela funciona e como pode ser utilizada por outros desenvolvedores.

Uma boa documentação deve informar:

- Endereços dos endpoints.
- Métodos HTTP disponíveis.
- Parâmetros necessários.
- Formato dos dados enviados e recebidos.
- Códigos de resposta.
- Regras de autenticação e autorização.
- Exemplos de requisições e respostas.

Uma ferramenta importante nesse contexto é a **OpenAPI Specification**, um padrão utilizado para descrever APIs HTTP de maneira organizada.

Com essa especificação, é possível documentar os recursos de uma API e facilitar sua compreensão, utilização e teste por outros desenvolvedores.

## 18. Conclusão

As APIs são fundamentais para o desenvolvimento de sistemas modernos, pois permitem que diferentes aplicações se comuniquem e compartilhem informações de maneira organizada.

Por meio de métodos HTTP, endpoints e formatos como JSON, elas possibilitam consultar, cadastrar, atualizar e excluir informações em diferentes sistemas.

Além disso, tecnologias como PHP e MySQL permitem desenvolver APIs próprias, conectando aplicações aos bancos de dados e disponibilizando informações para outros serviços.

No projeto Ferrorama, por exemplo, uma API poderia facilitar o compartilhamento de informações sobre sensores, trens e viagens, contribuindo para a integração e expansão do sistema.

Portanto, compreender o funcionamento das APIs é importante para quem estuda Desenvolvimento de Sistemas, pois essa tecnologia está presente em diversas aplicações utilizadas no cotidiano e no mercado de trabalho.

## 19. Referências bibliográficas

As informações desta pesquisa foram consultadas em documentações técnicas e materiais de organizações reconhecidas na área de desenvolvimento de software e segurança da informação.

1. MDN WEB DOCS (Mozilla). Documentação sobre HTTP, APIs e métodos de comunicação. Disponível em: https://developer.mozilla.org/pt-BR/

2. PHP. Manual oficial da linguagem PHP. Disponível em: https://www.php.net/manual/pt_BR/

3. OWASP FOUNDATION. Segurança de APIs e proteção de aplicações. Disponível em: https://owasp.org/

4. GOVERNO FEDERAL DO BRASIL. Documentação e orientações sobre APIs. Disponível em: https://www.gov.br/gsi/pt-br/seguranca-da-informacao-e-cibernetica/osic/osic-10-23?

5. GOOGLE CLOUD. Documentação sobre APIs e OpenAPI. Disponível em: https://cloud.google.com/docs?hl=pt-br

Acesso em: 03 nov. 2026.