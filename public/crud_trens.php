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

    <title>Trens</title>

    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body>

    <main>

        <h1>Trens</h1>

        <div class="planilha_dashboard">

            <table class="table">

                <thead>
                    <tr>
                        <th>Identificação</th>
                        <th>Modelo</th>
                        <th>Capacidade</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($trem = mysqli_fetch_assoc($resultado)) { ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($trem['identificacao']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($trem['modelo']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($trem['capacidade']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($trem['status']) ?>
                            </td>

                            <td>

                                <a href="editar_trens.php?id=<?= $trem['id'] ?>">
                                    Editar
                                </a>

                                <a href="excluir_trens.php?id=<?= $trem['id'] ?>"
                                   onclick="return confirm('Deseja realmente excluir este trem?')">
                                    Excluir
                                </a>

                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

        <a href="cadastrar_trens.php">
            <button type="button">Cadastrar trem</button>
        </a>

    </main>

</body>

</html>