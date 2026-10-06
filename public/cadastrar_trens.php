<?php
include("../infra/conexao.php");

if (isset($_POST['cadastrar'])) {

    $identificacao = $_POST['identificacao'];
    $modelo = $_POST['modelo'];
    $capacidade = $_POST['capacidade'];
    $status = $_POST['status'];

    $sql = "INSERT INTO trens (identificacao, modelo, capacidade, status)
            VALUES ('$identificacao', '$modelo', '$capacidade', '$status')";

    mysqli_query($conexao, $sql);

    header("Location: crud_trens.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Trem</title>
</head>

<body>

    <h1>Cadastrar Trem</h1>

    <form method="POST">

        <label>Identificação:</label>
        <input type="text" name="identificacao" required>

        <br><br>

        <label>Modelo:</label>
        <input type="text" name="modelo" required>

        <br><br>

        <label>Capacidade:</label>
        <input type="number" name="capacidade" required>

        <br><br>

        <label>Status:</label>
        <select name="status" required>
            <option value="Ativo">Ativo</option>
            <option value="Inativo">Inativo</option>
            <option value="Em Manutenção">Em Manutenção</option>
        </select>

        <br><br>

        <button type="submit" name="cadastrar">Cadastrar</button>

    </form>

    <br>

    <a href="crud_trens.php">Voltar</a>

</body>

</html>