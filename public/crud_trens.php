<?php
include("../infra/conexao.php");

$sql = "SELECT * FROM trens";
$resultado = mysqli_query($conexao, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trens - Ferroma</title>

    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body>

    <h1>Trens</h1>

    <a href="cadastrar_trens.php">Cadastrar trem</a>

    <br><br>

    <table border="1">
        <tr>
            <th>Identificação</th>
            <th>Modelo</th>
            <th>Capacidade</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>

        <?php while ($trem = mysqli_fetch_assoc($resultado)) { ?>

            <tr>
                <td><?= $trem['identificacao'] ?></td>
                <td><?= $trem['modelo'] ?></td>
                <td><?= $trem['capacidade'] ?></td>
                <td><?= $trem['status'] ?></td>

                <td>
                    <a href="editar_trem.php?id=<?= $trem['id'] ?>">Editar</a>

                    <a href="excluir_trem.php?id=<?= $trem['id'] ?>"
                       onclick="return confirm('Deseja excluir este trem?')">
                        Excluir
                    </a>
                </td>
            </tr>

        <?php } ?>

    </table>

</body>

</html>