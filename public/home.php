<?php
include "../infra/conexao.php";
$administrador = "Administrador";
$resultado = $conexao->query("SELECT * FROM sensores ORDER BY nome");
$sensores = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
$estatisticas = ["sensores" => count($sensores), "trens_ativos" => 0, "trens_manutencao" => 0, "disponibilidade" => 0];
$resultadoEstatisticas = $conexao->query("SELECT COUNT(*) AS total, SUM(status = 'Ativo') AS ativos, SUM(status = 'Em Manutenção') AS manutencao FROM trens");
if ($resultadoEstatisticas) {
    $dados = $resultadoEstatisticas->fetch_assoc();
    $totalTrens = (int) $dados["total"];
    $estatisticas["trens_ativos"] = (int) $dados["ativos"];
    $estatisticas["trens_manutencao"] = (int) $dados["manutencao"];
    $estatisticas["disponibilidade"] = $totalTrens > 0 ? round(($estatisticas["trens_ativos"] / $totalTrens) * 100, 1) : 0;
}
function e(string $texto): string { return htmlspecialchars($texto, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Ferrorama</title>
    <link rel="stylesheet" href="../assets/style/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<header class="cabecalho">
    <h2><i class="bi bi-train-front-fill"></i> Bem-vindo, <?= e($administrador) ?></h2>
    <a href="login.html" class="item"><i class="bi bi-box-arrow-right"></i> Sair</a>
</header>
<div class="layout">
    <aside class="menu-lateral">
        <a href="home.php" class="item ativo"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
        <a href="crud_sensor.php" class="item"><i class="bi bi-cpu-fill"></i> Sensores</a>
        <a href="crud_trens.php" class="item"><i class="bi bi-train-front-fill"></i> Trens</a>
        <a href="crud_usuarios.php" class="item"><i class="bi bi-people-fill"></i> Cadastrados</a>
        <a href="crud_viagens.php" class="item"><i class="bi bi-calendar-check-fill"></i> Viagens</a>
    </aside>
    <main class="conteudo">
        <section class="dashboard-apresentacao" aria-labelledby="titulo-dashboard">
            <div><p class="dashboard-sobrelinha">CENTRAL DE CONTROLE</p><h1 id="titulo-dashboard">Visão geral do Ferrorama</h1><p>Acompanhe o estado da operação em um único lugar.</p></div>
            <div class="dashboard-atualizacao" aria-live="polite"><span class="dashboard-ponto"></span><span id="ultima-atualizacao">Atualizado agora</span></div>
        </section>
        <section class="dashboard-cards" aria-label="Resumo do sistema">
            <article class="dashboard-card dashboard-card-sensores"><div class="dashboard-icone"><i class="bi bi-cpu-fill"></i></div><div class="dashboard-card-cabecalho"><p>Sensores cadastrados</p><i class="bi bi-arrow-up-right"></i></div><strong id="total-sensores"><?= $estatisticas["sensores"] ?></strong><small>Dispositivos monitorados</small></article>
            <article class="dashboard-card dashboard-card-trens"><div class="dashboard-icone"><i class="bi bi-train-front-fill"></i></div><div class="dashboard-card-cabecalho"><p>Trens em operação</p><i class="bi bi-arrow-up-right"></i></div><strong id="trens-ativos"><?= $estatisticas["trens_ativos"] ?></strong><small>Com status ativo no sistema</small></article>
            <article class="dashboard-card dashboard-card-alertas"><div class="dashboard-icone"><i class="bi bi-exclamation-triangle-fill"></i></div><div class="dashboard-card-cabecalho"><p>Alertas operacionais</p><i class="bi bi-arrow-up-right"></i></div><strong id="trens-manutencao"><?= $estatisticas["trens_manutencao"] ?></strong><small>Trens em manutenção</small></article>
            <article class="dashboard-card dashboard-card-disponibilidade"><div class="dashboard-icone"><i class="bi bi-activity"></i></div><div class="dashboard-card-cabecalho"><p>Disponibilidade</p><i class="bi bi-arrow-up-right"></i></div><strong id="disponibilidade"><?= number_format($estatisticas["disponibilidade"], 1, ",", "") ?>%</strong><div class="dashboard-progresso" aria-label="Disponibilidade dos trens"><span id="barra-disponibilidade" style="width: <?= $estatisticas["disponibilidade"] ?>%"></span></div><small>Percentual de trens ativos</small></article>
        </section>
        <section class="planilha_dashboard" aria-label="Lista de sensores">
            <div class="dashboard-tabela-cabecalho"><div><p class="dashboard-sobrelinha">MONITORAMENTO</p><h2><i class="bi bi-broadcast-pin"></i> Sensores do sistema</h2></div><a href="crud_sensor.php" class="dashboard-link">Gerenciar sensores <i class="bi bi-arrow-right"></i></a></div>
            <div class="table-responsive"><table class="table table-borderless"><thead><tr><th>ID</th><th>NOME</th><th>IDENTIFICAÇÃO</th><th>LOCALIZAÇÃO</th><th>TIPO DE DADO</th></tr></thead><tbody>
            <?php if ($sensores): ?>
                <?php foreach ($sensores as $sensor): ?>
                    <tr><th scope="row">#<?= e((string) $sensor["id"]) ?></th><td><?= e((string) $sensor["nome"]) ?></td><td><?= e((string) $sensor["identificacao"]) ?></td><td><?= e((string) $sensor["localizacao"]) ?></td><td><span class="dashboard-tag"><?= e((string) $sensor["tipo_sensor"]) ?></span></td></tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center py-4">Nenhum sensor cadastrado.</td></tr>
            <?php endif; ?>
            </tbody></table></div>
        </section>
    </main>
</div>
<script src="../scripts/script_home.js"></script>
</body>
</html>
