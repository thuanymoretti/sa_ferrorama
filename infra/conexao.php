<?php

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "sa_ferrorama";
$porta = 3389;
//trocar o número da porta conforme a porta do banco de dados que você está usando

$conexao = new mysqli($host, $usuario, $senha, $banco, $porta);

if ($conexao->connect_error) {
    die("Erro na conexão com o banco: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");

?>