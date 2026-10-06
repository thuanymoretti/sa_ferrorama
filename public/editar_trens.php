<?php
include("../infra/conexao.php");

$id = $_GET['id'];

$sql = "SELECT * FROM trens WHERE id = $id";
$resultado = mysqli_query($conexao, $sql);
$trem = mysqli_fetch_assoc($resultado);

if (isset($_POST['editar'])) {

    $identificacao = $_POST['identificacao'];
    $modelo = $_POST['modelo'];
    $capacidade = $_POST['capacidade'];
    $status = $_POST['status'];

    $sql = "UPDATE trens SET
            identificacao = '$identificacao',
            modelo = '$modelo',
            capacidade = '$capacidade',
            status = '$status'
            WHERE id = $id";

    mysqli_query($conexao, $sql);

    header("Location: crud_trens.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Editar Trem</title>
</head>

<body>

    <h1>Editar Trem</h1>

    <form method="POST">

        <label>Identificação:</label>
        <input type="text" name="identificacao"
               value="<?= $trem['identificacao'] ?>" required>

        <br><br>

        <label>Modelo:</label>
        <input type="text" name="modelo"
               value="<?= $trem['modelo'] ?>" required>

        <br><br>

        <label>Capacidade:</label>
        <input type="number" name="capacidade"
               value="<?= $trem['capacidade'] ?>" required>

        <br><br>

        <label>Status:</label>
        <select name="status" required>

            <option value="Ativo"
                <?= $trem['status'] == 'Ativo' ? 'selected' : '' ?>>
                Ativo
            </option>

            <option value="Inativo"
                <?= $trem['status'] == 'Inativo' ? 'selected' : '' ?>>
                Inativo
            </option>

            <option value="Em Manutenção"
                <?= $trem['status'] == 'Em Manutenção' ? 'selected' : '' ?>>
                Em Manutenção
            </option>

        </select>

        <br><br>

        <button type="submit" name="editar">Salvar alterações</button>

    </form>

    <br>

    <a href="crud_trens.php">Voltar</a>

</body>

</html>