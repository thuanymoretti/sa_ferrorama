<?php

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "sa_ferrorama";
$porta = 6608;
//trocr o numro da porta cnforme a porta do banco de dados que você está usando

$conexao = new mysqli($host, $usuario, $senha, $banco, $porta);

if ($conexao->connect_error) {
    die("Erro na conexão com o banco: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");

?>