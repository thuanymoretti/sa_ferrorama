# Sistema Ferroma

## Sobre o projeto

O Ferroma é uma aplicação web desenvolvida para simular um sistema de gerenciamento ferroviário. O sistema permite o cadastro e gerenciamento de usuários, sensores, trens e viagens.

O projeto utiliza PHP e MySQL para o funcionamento do sistema, além de HTML, CSS e JavaScript para o desenvolvimento das páginas e funcionalidades.

## Funcionalidades

- Cadastro de usuários
- Login e autenticação
- Gerenciamento de usuários
- Cadastro de sensores
- Gerenciamento de sensores
- Gerenciamento de trens
- Controle de viagens
- Dashboard do sistema

## Tecnologias utilizadas

- HTML
- CSS
- JavaScript
- PHP
- MySQL
- XAMPP

## Como executar o projeto

1. Baixe ou clone o repositório.
2. Coloque a pasta do projeto dentro da pasta `htdocs` do XAMPP.
3. Abra o XAMPP e inicie o Apache e o MySQL.
4. Acesse o phpMyAdmin e importe o arquivo `database/db.sql`.
5. Se o banco já existia antes da autenticação, execute uma vez `database/migracao_autenticacao.sql` no phpMyAdmin para adicionar o campo de hash das senhas. A migração preserva os usuários já cadastrados.
6. Abra o projeto no navegador através do endereço:

`http://localhost/sa_ferrorama/`

Na primeira configuração, crie o primeiro administrador pelo terminal local, fora do navegador: `C:\xampp\php\php.exe scripts\criar_administrador.php`. Execute o comando a partir da pasta do projeto. Depois, acesse pelo navegador e entre com as credenciais. O cadastro inicial não fica exposto como formulário público.

Administradores autenticados podem cadastrar, editar e excluir usuários, definir perfis e redefinir senhas. Funcionários autenticados podem acessar o painel e as áreas operacionais, mas não podem administrar contas. As senhas são armazenadas com `password_hash`; ações de alteração de usuários exigem POST e token CSRF.

## Equipe

- Arthur Gomes
- Julia Muller
- Kamily Rosa
- Thuany Moretti

## Status do projeto

Em desenvolvimento.

O projeto está em processo de desenvolvimento e poderá receber novas funcionalidades, melhorias e correções.

## Objetivo

Projeto desenvolvido para fins acadêmicos na disciplina de Programação de Aplicativos, com o objetivo de colocar em prática conhecimentos de desenvolvimento web, programação em PHP e banco de dados.

## Licença

Este projeto utiliza a Licença MIT.
