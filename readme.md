# Sistema Ferrorama

## Sobre o projeto

O Ferrorama é uma aplicação web desenvolvida para simular um sistema de gerenciamento e monitoramento ferroviário. O sistema permite o cadastro e gerenciamento de usuários, sensores, trens e viagens, auxiliando no controle das informações relacionadas à operação ferroviária.

O projeto utiliza PHP e MySQL para o funcionamento e armazenamento dos dados, além de HTML, CSS e JavaScript para o desenvolvimento das páginas e funcionalidades.

## Objetivo

O objetivo do projeto é desenvolver um sistema que facilite o gerenciamento das informações ferroviárias, permitindo o controle de usuários, trens e sensores, além do acompanhamento das operações.

O sistema também tem como finalidade aplicar os conhecimentos adquiridos no curso técnico em Desenvolvimento de Sistemas.

## Funcionalidades

- Cadastro de usuários
- Login e autenticação
- Gerenciamento de usuários
- Cadastro de sensores
- Gerenciamento de sensores
- Cadastro e gerenciamento de trens
- Controle de viagens
- Dashboard do sistema
- Controle de acesso por tipo de usuário

## Regras de negócio

As regras de negócio definem as condições que devem ser respeitadas durante o funcionamento do sistema.

- RN01 - Apenas usuários cadastrados podem acessar o sistema através de e-mail e senha.
- RN02 - Após login válido, o sistema deve criar uma sessão para o administrador.
- RN03 - Todo sensor deve possuir nome, localização, tipo de monitoramento e vínculo com um trem.
- RN04 - Os sensores podem monitorar velocidade, temperatura, consumo de energia, falhas e localização.
- RN05 - Sensores com dados registrados não podem ser excluídos.
- RN06 - Antes de excluir um sensor, o sistema deve solicitar confirmação do usuário.
- RN07 - Os dados dos sensores devem ser atualizados em tempo real.
- RN08 - O sistema deve classificar o status como normal, alerta ou falha.
- RN09 - Administradores podem gerar relatórios analíticos da operação ferroviária.
- RN10 - Os relatórios gerados devem ficar salvos para consultas futuras.
- RN11 - Os relatórios devem permitir filtros por período, sensor, trem e tipo de falha.
- RN12 - O sistema deve encerrar a sessão e retornar à tela de login.

## Requisitos funcionais

Os requisitos funcionais representam as funcionalidades que o sistema deve oferecer aos usuários.

- RF01 - Permitir login utilizando e-mail e senha.
- RF02 - Validar os dados do usuário no banco de dados.
- RF03 - Permitir cadastrar sensores ferroviários.
- RF04 - Permitir cadastrar trens no sistema.
- RF05 - Exibir todos os sensores cadastrados.
- RF06 - Permitir excluir sensores cadastrados.
- RF07 - Solicitar confirmação antes da exclusão.
- RF08 - Permitir visualizar detalhes dos sensores.
- RF09 - Exibir velocidade, localização e status operacional.
- RF10 - Exibir gráficos e indicadores operacionais.
- RF11 - Permitir gerar relatórios operacionais.
- RF12 - Permitir consultar relatórios salvos.
- RF13 - Permitir filtros por período e falha.
- RF14 - Permitir encerrar a sessão do usuário.

## Requisitos não funcionais

Os requisitos não funcionais definem características relacionadas à qualidade, segurança e funcionamento do sistema.

- RNF01 - As senhas devem ser criptografadas no banco de dados.
- RNF02 - O sistema deve atualizar dados em tempo real com baixa latência.
- RNF03 - A interface deve ser intuitiva e responsiva.
- RNF04 - O sistema deve funcionar 24 horas por dia.
- RNF05 - O sistema deve suportar expansão de sensores e trens.
- RNF06 - Os dados enviados pelos sensores não podem ser perdidos.
- RNF07 - O sistema deve funcionar nos principais navegadores.
- RNF08 - O sistema deve utilizar banco de dados relacional.
- RNF09 - As operações devem responder em até 3 segundos.
- RNF10 - O sistema deve manter estabilidade durante o monitoramento contínuo.

## Tecnologias utilizadas

- HTML
- CSS
- JavaScript
- PHP
- MySQL
- Bootstrap
- XAMPP

## Como executar o projeto

1. Baixe ou clone o repositório.
2. Coloque a pasta do projeto dentro da pasta `htdocs` do XAMPP.
3. Abra o XAMPP e inicie o Apache e o MySQL.
4. Acesse o phpMyAdmin e importe o arquivo `database/db.sql`.
5. Caso o banco já existisse antes da implementação da autenticação, execute uma vez o arquivo `database/migracao_autenticacao.sql` para adicionar o campo de hash das senhas. A migração preserva os usuários já cadastrados.
6. Na primeira configuração, crie o primeiro administrador pelo terminal local, fora do navegador, utilizando o comando:

`C:\xampp\php\php.exe scripts\criar_administrador.php`

Execute o comando a partir da pasta do projeto.

7. Abra o projeto no navegador através do endereço:

`http://localhost/sa_ferrorama/`

## Segurança e controle de acesso

O sistema possui autenticação e controle de permissões de acordo com o tipo de usuário.

- Administrador: pode cadastrar, editar e excluir usuários, definir perfis e redefinir senhas.
- Funcionário: pode acessar o painel e as áreas operacionais, mas não pode administrar contas.

As senhas são armazenadas utilizando `password_hash()`, garantindo maior segurança. As operações de alteração de usuários utilizam requisições POST e tokens CSRF para ajudar a proteger o sistema.

O cadastro inicial do administrador é realizado pelo terminal, não ficando disponível como formulário público.

## Mockup do projeto

O protótipo visual do sistema foi desenvolvido no Canva e pode ser acessado pelo link:

https://canva.link/sar0cfaojf8rln7


## Status do projeto

Em desenvolvimento.

O projeto está em processo de desenvolvimento e poderá receber novas funcionalidades, melhorias e correções.

## Objetivo acadêmico

Projeto desenvolvido para fins acadêmicos na disciplina de Programação de Aplicativos, com o objetivo de colocar em prática conhecimentos de desenvolvimento web, programação em PHP, banco de dados e segurança da informação.

## Licença

Este projeto utiliza a Licença MIT.