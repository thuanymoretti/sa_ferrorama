<?php
require_once "../infra/auth.php";
exigir_login();

require_once "../infra/conexao.php";

// Busca todos os trens cadastrados
$sql = "SELECT * FROM trens";
$resultado = mysqli_query($conexao, $sql);

// Verifica se ocorreu algum erro na consulta
if (!$resultado) {
    die("Erro ao buscar os trens: " . mysqli_error($conexao));
}

// Guarda os dados do usuário conectado
$usuario = $_SESSION["usuario"] ?? null;
?>

<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Trens | Ferrorama</title>
    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body class="pagina-cadastro">

    <header class="cabecalho">

        <h2>
            Olá,
            <?= htmlspecialchars($usuario["nome"] ?? "Usuário", ENT_QUOTES, "UTF-8") ?>
        </h2>

        <a class="item" href="home.php">
            Voltar ao painel
        </a>

    </header>

    <div class="layout">

        <aside class="menu-lateral">

            <a href="home.php" class="item">
                <i class="bi bi-grid-1x2-fill"></i>
                Dashboard
            </a>

            <a href="crud_sensor.php" class="item">
                <i class="bi bi-cpu-fill"></i>
                Sensores
            </a>

            <a href="crud_trens.php" class="item ativo">
                <i class="bi bi-train-front-fill"></i>
                Trens
            </a>

            <?php if (
                isset($usuario["tipo_usuario"]) &&
                $usuario["tipo_usuario"] === "Administrador"
            ): ?>

                <a href="crud_usuarios.php" class="item">
                    <i class="bi bi-people-fill"></i>
                    Usuários
                </a>

            <?php endif; ?>

            <a href="crud_viagens.php" class="item">
                <i class="bi bi-calendar-check-fill"></i>
                Viagens
            </a>

        </aside>

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

                        <?php if (mysqli_num_rows($resultado) > 0): ?>

                            <?php while ($trem = mysqli_fetch_assoc($resultado)): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($trem["identificacao"], ENT_QUOTES, "UTF-8") ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($trem["modelo"], ENT_QUOTES, "UTF-8") ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars((string) $trem["capacidade"], ENT_QUOTES, "UTF-8") ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($trem["status"], ENT_QUOTES, "UTF-8") ?>
                                    </td>

                                    <td>
                                    <?php if ($_SESSION["usuario"]["tipo_usuario"] === "Administrador"): ?>
                                      <a class="btn-editar" href="editar_sensor.php?id=<?= (int) $sensor['id'] ?>">  Editar </a>
                                <form method="post" action="excluir_sensor.php" onsubmit="return confirm('Confirma a exclusão deste sensor?')" style="display:inline"> 
                                <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">
                                <input type="hidden" name="id" value="<?= (int) $sensor['id'] ?>">
                                <button class="btn-excluir" type="submit">Excluir</button>
                                </form>
                                    <?php else: ?>—<?php endif; ?>
                                </td>

                                   

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="5">
                                    Nenhum trem cadastrado.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <?php if ($_SESSION["usuario"]["tipo_usuario"] === "Administrador"): ?><a href="cadastrar_trens.php"><button type="button">Cadastrar novo trem</button></a><?php endif; ?>
            </div>
            </a>

        </main>

    </div>

</body>

</html>
```