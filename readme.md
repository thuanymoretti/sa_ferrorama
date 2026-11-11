# Sistema Ferrorama

## Sobre o projeto

O Ferrorama é uma aplicação web desenvolvida com a proposta de simular um sistema de gerenciamento e monitoramento ferroviário. O projeto surgiu com o objetivo de reunir em uma única plataforma informações importantes sobre a operação ferroviária, facilitando o controle de usuários, trens, sensores e viagens.

Por meio do sistema, é possível realizar o gerenciamento de diferentes informações relacionadas ao ambiente ferroviário, permitindo que os usuários tenham acesso a uma plataforma organizada para consultar e administrar os dados cadastrados.

A aplicação foi desenvolvida utilizando PHP e MySQL, responsáveis pelo processamento das informações e armazenamento dos dados. Para a construção da interface, foram utilizados HTML, CSS e JavaScript, buscando proporcionar uma navegação simples e organizada.

O Ferrorama também possui diferentes níveis de acesso, permitindo que as funcionalidades sejam disponibilizadas de acordo com o tipo de usuário cadastrado no sistema.

## Objetivo

O principal objetivo do Ferrorama é desenvolver uma solução que auxilie no gerenciamento das informações relacionadas ao setor ferroviário, proporcionando maior organização e facilidade no controle dos dados.

A proposta envolve o gerenciamento de usuários, trens, sensores e viagens, além da possibilidade de acompanhar informações operacionais e consultar dados relacionados ao funcionamento do sistema.

Outro objetivo é aplicar, na prática, os conhecimentos adquiridos durante o curso técnico em Desenvolvimento de Sistemas, envolvendo desenvolvimento web, programação em PHP, integração com banco de dados, organização de código e segurança da informação.

## Funcionamento do sistema

O funcionamento do Ferrorama é baseado no acesso de usuários autenticados, que podem utilizar as funcionalidades disponíveis conforme suas permissões.

Inicialmente, o usuário realiza o login informando seu e-mail e senha. Após a validação das credenciais, o sistema permite o acesso ao ambiente interno, onde são disponibilizadas as áreas de gerenciamento e consulta.

O sistema possui dois tipos de usuários, cada um com permissões específicas:

**Administrador:** possui permissões para gerenciar as contas de usuários, realizar cadastros, editar informações, excluir registros e definir os perfis de acesso.

**Funcionário:** possui acesso ao painel e às áreas operacionais do sistema, mas não pode realizar o gerenciamento das contas de usuários.

Dessa forma, o controle de acesso permite separar as responsabilidades de cada perfil, evitando que todos os usuários tenham acesso às mesmas operações administrativas.

## Principais funcionalidades

O sistema foi planejado para oferecer diferentes funcionalidades relacionadas ao gerenciamento ferroviário. Cada módulo possui uma finalidade específica e contribui para a organização das informações armazenadas na aplicação.

### Gerenciamento de usuários

O módulo de usuários permite realizar o cadastro e o gerenciamento das contas utilizadas para acessar o sistema. Cada usuário possui informações como nome, e-mail, telefone e tipo de perfil.

O administrador é responsável por cadastrar novos usuários, editar informações, excluir contas e redefinir senhas. Essas operações são restritas aos usuários que possuem as permissões necessárias.

### Gerenciamento de sensores

O módulo de sensores permite cadastrar e consultar os sensores relacionados à operação ferroviária.

Cada sensor deve possuir informações de identificação, nome, localização, tipo de monitoramento e vínculo com um trem. Esses dados permitem organizar os equipamentos e identificar a qual trem cada sensor está relacionado.

De acordo com as regras de negócio, os sensores podem monitorar diferentes informações, como velocidade, temperatura, consumo de energia, falhas e localização.

### Gerenciamento de trens

O sistema permite cadastrar e gerenciar os trens utilizados na operação ferroviária, armazenando informações como identificação, modelo, capacidade e situação operacional.

Esse gerenciamento facilita a organização dos registros e permite relacionar os trens aos sensores cadastrados, contribuindo para o controle das informações de cada veículo ferroviário.

### Controle de viagens

O módulo de viagens tem como finalidade organizar informações relacionadas aos deslocamentos ferroviários, permitindo registrar dados como trem utilizado, data de início, data de chegada e origem.

Essas informações contribuem para o acompanhamento e a organização das operações realizadas, permitindo manter os registros das viagens associados aos respectivos trens.

### Dashboard

O dashboard funciona como um painel principal do sistema, reunindo indicadores e informações importantes em uma única tela.

Sua finalidade é facilitar a visualização dos dados gerais, permitindo que os usuários tenham uma visão mais organizada das informações relacionadas aos trens, sensores e demais registros.

### Relatórios operacionais

O sistema também prevê a geração e consulta de relatórios relacionados à operação ferroviária.

De acordo com os requisitos definidos, os relatórios devem permitir a análise de informações por período, sensor, trem e tipo de falha, auxiliando na consulta dos registros e no acompanhamento das operações.

## Regras de negócio

As regras de negócio estabelecem as condições que devem ser respeitadas durante a utilização do sistema. Elas definem limitações, permissões e comportamentos necessários para manter o funcionamento adequado da aplicação.

As regras definidas para o Ferrorama são:

- **RN01:** Apenas usuários cadastrados podem acessar o sistema através de e-mail e senha.
- **RN02:** Após login válido, o sistema deve criar uma sessão para o administrador.
- **RN03:** Todo sensor deve possuir nome, localização, tipo de monitoramento e vínculo com um trem.
- **RN04:** Os sensores podem monitorar velocidade, temperatura, consumo de energia, falhas e localização.
- **RN05:** Sensores com dados registrados não podem ser excluídos.
- **RN06:** Antes de excluir um sensor, o sistema deve solicitar confirmação do usuário.
- **RN07:** Os dados dos sensores devem ser atualizados em tempo real.
- **RN08:** O sistema deve classificar o status como normal, alerta ou falha.
- **RN09:** Administradores podem gerar relatórios analíticos da operação ferroviária.
- **RN10:** Os relatórios gerados devem ficar salvos para consultas futuras.
- **RN11:** Os relatórios devem permitir filtros por período, sensor, trem e tipo de falha.
- **RN12:** O sistema deve encerrar a sessão e retornar à tela de login.

## Requisitos funcionais

Os requisitos funcionais descrevem as operações e funcionalidades que o sistema deve disponibilizar aos seus usuários. Eles representam as ações que podem ser realizadas dentro da aplicação e servem como referência durante o desenvolvimento.

Os requisitos definidos para o Ferrorama são:

- **RF01:** Permitir login utilizando e-mail e senha.
- **RF02:** Validar os dados do usuário no banco de dados.
- **RF03:** Permitir cadastrar sensores ferroviários.
- **RF04:** Permitir cadastrar trens no sistema.
- **RF05:** Exibir todos os sensores cadastrados.
- **RF06:** Permitir excluir sensores cadastrados.
- **RF07:** Solicitar confirmação antes da exclusão.
- **RF08:** Permitir visualizar detalhes dos sensores.
- **RF09:** Exibir velocidade, localização e status operacional.
- **RF10:** Exibir gráficos e indicadores operacionais.
- **RF11:** Permitir gerar relatórios operacionais.
- **RF12:** Permitir consultar relatórios salvos.
- **RF13:** Permitir filtros por período e falha.
- **RF14:** Permitir encerrar a sessão do usuário.

## Requisitos não funcionais

Os requisitos não funcionais estão relacionados às características de qualidade do sistema, estabelecendo condições de segurança, desempenho, disponibilidade, estabilidade e facilidade de utilização.

Esses requisitos são importantes para garantir que a aplicação não apenas ofereça as funcionalidades necessárias, mas também apresente condições adequadas de funcionamento.

Os requisitos definidos são:

- **RNF01:** As senhas devem ser criptografadas no banco de dados.
- **RNF02:** O sistema deve atualizar dados em tempo real com baixa latência.
- **RNF03:** A interface deve ser intuitiva e responsiva.
- **RNF04:** O sistema deve funcionar 24 horas por dia.
- **RNF05:** O sistema deve suportar expansão de sensores e trens.
- **RNF06:** Os dados enviados pelos sensores não podem ser perdidos.
- **RNF07:** O sistema deve funcionar nos principais navegadores.
- **RNF08:** O sistema deve utilizar banco de dados relacional.
- **RNF09:** As operações devem responder em até 3 segundos.
- **RNF10:** O sistema deve manter estabilidade durante o monitoramento contínuo.

## Tecnologias utilizadas

Durante o desenvolvimento do Ferrorama, foram utilizadas diferentes tecnologias voltadas à construção de aplicações web e ao gerenciamento de informações.

O **HTML** foi utilizado para estruturar o conteúdo das páginas, organizando os elementos que são apresentados aos usuários. O **CSS** é responsável pela parte visual do sistema, permitindo definir cores, espaçamentos, alinhamentos e a aparência dos componentes.

O **JavaScript** foi utilizado para adicionar interatividade e comportamentos às páginas, enquanto o **PHP** é responsável pelo processamento das informações no servidor e pela execução das funcionalidades relacionadas ao sistema.

Para o armazenamento e gerenciamento dos dados, foi utilizado o **MySQL**, que permite organizar as informações em tabelas e estabelecer relacionamentos entre os registros.

Também foi utilizado o **Bootstrap** como auxílio na construção da interface, além do **XAMPP**, que fornece o ambiente local necessário para executar o servidor Apache e o banco de dados MySQL durante o desenvolvimento.

## Banco de dados

O Ferrorama utiliza o MySQL como sistema de gerenciamento de banco de dados, sendo responsável por armazenar e organizar as informações utilizadas pela aplicação.

A estrutura do banco de dados é dividida em tabelas, cada uma responsável por armazenar informações específicas. Essa organização permite relacionar os registros e facilitar as operações de cadastro, consulta, edição e exclusão.

Entre as principais tabelas utilizadas estão:

- **usuarios:** armazena os dados das pessoas cadastradas, incluindo informações pessoais e dados de acesso ao sistema.
- **trens:** armazena informações de identificação, modelo, capacidade e situação dos trens.
- **sensores:** registra os dados dos sensores e o trem ao qual estão vinculados.
- **viagens:** armazena informações relacionadas às viagens ferroviárias, como trem utilizado, datas e origem.

A utilização de um banco de dados relacional permite manter as informações organizadas e facilita a comunicação entre as diferentes partes do sistema.

## Segurança e controle de acesso

A segurança é uma parte importante do desenvolvimento do Ferrorama, principalmente porque o sistema trabalha com contas de usuários e informações armazenadas no banco de dados.

Para controlar o acesso, a aplicação utiliza autenticação por e-mail e senha, além de sessões para manter o usuário conectado durante a navegação.

As permissões são definidas de acordo com o tipo de usuário, garantindo que determinadas operações administrativas sejam realizadas apenas por administradores.

Além disso, o sistema utiliza `password_hash()` para armazenar as senhas de forma protegida, evitando que sejam salvas diretamente em texto no banco de dados.

As operações de alteração de usuários utilizam requisições POST e tokens CSRF, que ajudam a proteger o sistema contra requisições indevidas.

Outro cuidado adotado foi a criação do primeiro administrador pelo terminal local, evitando que essa funcionalidade fique disponível em um formulário público.

## Como executar o projeto

Para executar o Ferrorama em um ambiente local, é necessário possuir o XAMPP instalado e realizar algumas configurações antes de acessar a aplicação.

### Pré-requisitos

Antes de iniciar a execução, é necessário possuir:

- XAMPP instalado no computador.
- Navegador de internet.
- Repositório do projeto baixado ou clonado.

### Preparação do ambiente

1. Baixe ou clone o repositório.
2. Coloque a pasta do projeto dentro da pasta `htdocs` do XAMPP.
3. Abra o painel de controle do XAMPP.
4. Inicie os serviços Apache e MySQL.

### Configuração do banco de dados

1. Acesse o phpMyAdmin pelo navegador.
2. Importe o arquivo `database/db.sql` para criar o banco de dados e suas tabelas.
3. Caso o banco já existisse antes da implementação da autenticação, execute uma vez o arquivo `database/migracao_autenticacao.sql` para adicionar o campo de hash das senhas. Essa migração preserva os usuários já cadastrados.

### Criação do primeiro administrador

Na primeira configuração do sistema, é necessário criar o primeiro administrador pelo terminal local, fora do navegador.

Execute o seguinte comando a partir da pasta do projeto:

`C:\xampp\php\php.exe scripts\criar_administrador.php`

Após a execução, siga as instruções apresentadas no terminal para realizar a criação da conta.

O cadastro inicial é feito dessa maneira para evitar que qualquer pessoa possa criar uma conta de administrador através de um formulário público.

### Acesso ao sistema

Depois de realizar as configurações anteriores, abra o navegador e acesse o seguinte endereço:

`http://localhost/sa_ferrorama/`

Na tela inicial, informe o e-mail e a senha do administrador cadastrado para entrar no sistema.

## Mockup do projeto

O protótipo visual do Ferrorama foi desenvolvido no Canva, com o objetivo de planejar a organização das páginas e a aparência da aplicação durante seu desenvolvimento.

O mockup apresenta uma representação visual da proposta do sistema e serve como referência para a construção da interface.

O protótipo pode ser acessado pelo link:

https://canva.link/sar0cfaojf8rln7

## Status do projeto

**Finalizado.**

Encerramos o processo de desenvolvimento do site.

## Objetivo acadêmico

O Ferrorama foi desenvolvido para fins acadêmicos na disciplina de Programação de Aplicativos, como parte das atividades práticas do curso técnico em Desenvolvimento de Sistemas.

A construção do projeto permite trabalhar diferentes etapas do desenvolvimento de software, desde a definição das regras de negócio e dos requisitos até a implementação das funcionalidades, integração com banco de dados e realização de testes.

Além disso, o trabalho em equipe proporciona a divisão de responsabilidades e a colaboração entre os integrantes durante o desenvolvimento da aplicação, permitindo colocar em prática os conhecimentos adquiridos ao longo do curso.

## Licença

Este projeto utiliza a Licença MIT, que permite o uso, a cópia, a modificação e a distribuição do software, respeitando as condições estabelecidas pela licença.