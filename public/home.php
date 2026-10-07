<?php

require_once "../infra/auth.php";
exigir_login();
require_once "../infra/conexao.php";

$mensagem_login = "";
if (isset($_SESSION["mensagem_login"])) {
    $mensagem_login = $_SESSION["mensagem_login"];
    unset($_SESSION["mensagem_login"]);
}

$sensores = $conexao->query(
    "SELECT id, nome, identificacao, localizacao, tipo_sensor
     FROM sensores ORDER BY nome"
)->fetch_all(MYSQLI_ASSOC);

$usuarios = [
    "Administrador" => 0,
    "Funcionario" => 0
];

$resultado = $conexao->query(
    "SELECT tipo_usuario, COUNT(*) total
     FROM usuarios GROUP BY tipo_usuario"
);

while ($usuario = $resultado->fetch_assoc()) {
    if (isset($usuarios[$usuario["tipo_usuario"]])) {
        $usuarios[$usuario["tipo_usuario"]] = (int)$usuario["total"];
    }
}

$resultado = $conexao->query(
    "SELECT COUNT(*) total,
            SUM(status = 'Ativo') ativos,
            SUM(status = 'Em Manutenção') manutencao
     FROM trens"
)->fetch_assoc();

$totalTrens = (int)$resultado["total"];
$trensAtivos = (int)$resultado["ativos"];
$manutencao = (int)$resultado["manutencao"];

$disponibilidade = $totalTrens > 0
    ? round(($trensAtivos / $totalTrens) * 100, 1)
    : 0;

function e($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, "UTF-8");
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Ferrorama</title>

    <link rel="stylesheet" href="../assets/style/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<body>

<?php if ($mensagem_login != ""): ?>
    <script>
        alert("<?php echo $mensagem_login; ?>");
    </script>

<?php endif; ?>
<header class="cabecalho">

    <h2>
        Olá, <?= escapar($_SESSION["usuario"]["nome"]) ?>
    </h2>

    <form method="post" action="logout.php">
        <input type="hidden" name="token_csrf" value="<?= escapar(token_csrf()) ?>">
        <button type="submit" class="item">Sair</button>
    </form>

</header>

<div class="layout">

    <aside class="menu-lateral">

        <a href="home.php" class="item ativo">Dashboard</a>
        <a href="crud_sensor.php" class="item">Sensores</a>
        <a href="crud_trens.php" class="item">Trens</a>

        <?php if ($_SESSION["usuario"]["tipo_usuario"] === "Administrador"): ?>
            <a href="crud_usuarios.php" class="item">Usuários</a>
        <?php endif; ?>

        <a href="crud_viagens.php" class="item">Viagens</a>

    </aside>

    <main class="conteudo">

        <section class="dashboard-apresentacao">

            <div>
                <p class="dashboard-sobrelinha">CENTRAL DE CONTROLE</p>
                <h1>Visão geral do Ferrorama</h1>
                <p>Acompanhe o estado da operação em um único lugar.</p>
            </div>

            <div class="dashboard-atualizacao">
                <span class="dashboard-ponto"></span>
                <span id="ultima-atualizacao">Atualizado agora</span>
            </div>

        </section>

        <section class="dashboard-usuarios">

            <div class="dashboard-usuarios-resumo">

                <div>
                    <p class="dashboard-sobrelinha">ACESSOS CADASTRADOS</p>
                    <h2>Visão geral de usuários</h2>
                    <p>Distribuição das contas registradas no sistema.</p>
                </div>

                <div class="dashboard-usuarios-total">
                    <strong><?= array_sum($usuarios) ?></strong>
                    <span>Usuários no total</span>
                </div>

            </div>

            <div class="dashboard-usuarios-perfis">

                <article class="dashboard-perfil dashboard-perfil-admin">
                    <div>
                        <span>Administradores</span>
                        <strong><?= $usuarios["Administrador"] ?></strong>
                    </div>
                </article>

                <article class="dashboard-perfil dashboard-perfil-funcionario">
                    <div>
                        <span>Funcionários</span>
                        <strong><?= $usuarios["Funcionario"] ?></strong>
                    </div>
                </article>

                <?php if ($_SESSION["usuario"]["tipo_usuario"] === "Administrador"): ?>
                    <a class="dashboard-usuarios-link" href="crud_usuarios.php">
                        Gerenciar usuários
                    </a>
                <?php endif; ?>

            </div>

        </section>

        <section class="dashboard-cards">

            <article class="dashboard-card dashboard-card-sensores">
                <div class="dashboard-card-cabecalho">
                    <p>Sensores cadastrados</p>
                </div>
                <strong><?= count($sensores) ?></strong>
                <small>Dispositivos monitorados</small>
            </article>


            <article class="dashboard-card dashboard-card-trens">
                <div class="dashboard-card-cabecalho">
                    <p>Trens em operação</p>
                </div>
                <strong><?= $trensAtivos ?></strong>
                <small>Com status ativo no sistema</small>
            </article>


            <article class="dashboard-card dashboard-card-alertas">
                <div class="dashboard-card-cabecalho">
                    <p>Alertas operacionais</p>
                </div>
                <strong><?= $manutencao ?></strong>
                <small>Trens em manutenção</small>
            </article>

            <article class="dashboard-card dashboard-card-disponibilidade">

                <div class="dashboard-card-cabecalho">
                    <p>Disponibilidade</p>
                </div>

                <strong><?= number_format($disponibilidade, 1, ",", "") ?>%</strong>

                <div class="dashboard-progresso">
                    <span style="width: <?= $disponibilidade ?>%"></span>
                </div>

                <small>Percentual de trens ativos</small>

            </article>

        </section>

        <section class="planilha_dashboard">

            <div class="dashboard-tabela-cabecalho">

                <div>
                    <p class="dashboard-sobrelinha">MONITORAMENTO</p>
                    <h2>Sensores do sistema</h2>
                </div>

                <a href="crud_sensor.php" class="dashboard-link">
                    Gerenciar sensores
                </a>

            </div>

            <div class="table-responsive">

                <table class="table table-borderless">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NOME</th>
                            <th>IDENTIFICAÇÃO</th>
                            <th>LOCALIZAÇÃO</th>
                            <th>TIPO DE DADO</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($sensores): ?>

                        <?php foreach ($sensores as $sensor): ?>

                            <tr>

                                <th>#<?= e($sensor["id"]) ?></th>

                                <td><?= e($sensor["nome"]) ?></td>

                                <td><?= e($sensor["identificacao"]) ?></td>

                                <td><?= e($sensor["localizacao"]) ?></td>

                                <td>
                                    <span class="dashboard-tag">
                                        <?= e($sensor["tipo_sensor"]) ?>
                                    </span>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="text-center py-4">
                                Nenhum sensor cadastrado.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

<script src="../scripts/script_home.js"></script>

</body>
</html>