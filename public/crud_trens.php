<?php
require_once "../infra/auth.php";
exigir_login();
require_once "../infra/conexao.php";

$resultado = $conexao->query(
    "SELECT id, identificacao, modelo, capacidade, status
     FROM trens ORDER BY identificacao"
);
$usuario = $_SESSION["usuario"];
$ehAdministrador = $usuario["tipo_usuario"] === "Administrador";
$csrf = token_csrf();
$mensagem = obter_flash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trens cadastrados | Ferrorama</title>
    <link rel="stylesheet" href="../assets/style/style.css">
</head>
<body>
    <header class="cabecalho">
        <h2>Olá, <?= escapar((string) $usuario["nome"]) ?></h2>
        <a href="home.php" class="item">Voltar ao painel</a>
    </header>

    <div class="layout">
        <aside class="menu-lateral">
            <a href="home.php" class="item"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a href="crud_sensor.php" class="item"><i class="bi bi-cpu-fill"></i> Sensores</a>
            <a href="crud_trens.php" class="item ativo"><i class="bi bi-train-front-fill"></i> Trens</a>
            <?php if ($ehAdministrador): ?>
                <a href="crud_usuarios.php" class="item"><i class="bi bi-people-fill"></i> Usuários</a>
            <?php endif; ?>
            <a href="crud_viagens.php" class="item"><i class="bi bi-calendar-check-fill"></i> Viagens</a>
        </aside>

        <main class="conteudo">
            <div class="titulo_sensores">
                <h1>Trens cadastrados</h1>
            </div>

            <?php if ($mensagem): ?>
                <p class="mensagem-<?= escapar($mensagem["tipo"]) ?>" role="status">
                    <?= escapar($mensagem["mensagem"]) ?>
                </p>
            <?php endif; ?>

            <div class="planilha_dashboard">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>IDENTIFICAÇÃO</th>
                            <th>MODELO</th>
                            <th>CAPACIDADE</th>
                            <th>STATUS</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado->num_rows > 0): ?>
                            <?php while ($trem = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= (int) $trem["id"] ?></td>
                                    <td><?= escapar($trem["identificacao"]) ?></td>
                                    <td><?= escapar($trem["modelo"]) ?></td>
                                    <td><?= (int) $trem["capacidade"] ?></td>
                                    <td><?= escapar($trem["status"]) ?></td>
                                    <td>
                                        <?php if ($ehAdministrador): ?>
                                            <a class="btn-editar" href="editar_trens.php?id=<?= (int) $trem["id"] ?>">Editar</a>
                                            <form method="post" action="excluir_trens.php" onsubmit="return confirm('Confirma a exclusão deste trem?')" style="display:inline">
                                                <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">
                                                <input type="hidden" name="id" value="<?= (int) $trem["id"] ?>">
                                                <button class="btn-excluir" type="submit">Excluir</button>
                                            </form>
                                        <?php else: ?>—<?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">Nenhum trem cadastrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if ($ehAdministrador): ?>
                    <a href="cadastrar_trens.php"><button type="button">Cadastrar novo trem</button></a>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
