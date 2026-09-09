<?php
declare(strict_types=1);

$administrador = 'Administrador';
$sensores = [
    ['id' => '001', 'localizacao' => 'Estação Central - Eixo A', 'tipo' => 'Velocidade', 'status' => 'Ativo'],
    ['id' => '002', 'localizacao' => 'Ponte de Ferro do Rio Paraty', 'tipo' => 'Temperatura', 'status' => 'Ativo'],
    ['id' => '003', 'localizacao' => 'Pátio Sul', 'tipo' => 'Falha', 'status' => 'Alerta'],
    ['id' => '004', 'localizacao' => 'Linha Oeste - Km 12', 'tipo' => 'Velocidade', 'status' => 'Ativo'],
    ['id' => '005', 'localizacao' => 'Estação Leste - Plataforma 2', 'tipo' => 'Temperatura', 'status' => 'Ativo'],
    ['id' => '006', 'localizacao' => 'Trecho Sul - Km 78', 'tipo' => 'Pressão', 'status' => 'Ativo'],
    ['id' => '007', 'localizacao' => 'Pátio Norte', 'tipo' => 'Energia', 'status' => 'Ativo'],
    ['id' => '008', 'localizacao' => 'Estação Jardim Azul', 'tipo' => 'Velocidade', 'status' => 'Ativo'],
    ['id' => '009', 'localizacao' => 'Trecho Central - Km 101', 'tipo' => 'Vibração', 'status' => 'Ativo'],
    ['id' => '010', 'localizacao' => 'Estação Rio Verde', 'tipo' => 'Temperatura', 'status' => 'Ativo'],
    ['id' => '011', 'localizacao' => 'Trecho Industrial - Km 56', 'tipo' => 'Energia', 'status' => 'Ativo'],
    ['id' => '012', 'localizacao' => 'Pátio Ferroviário Oeste', 'tipo' => 'Pressão', 'status' => 'Ativo'],
    ['id' => '013', 'localizacao' => 'Estação Bela Vista', 'tipo' => 'Velocidade', 'status' => 'Ativo'],
    ['id' => '014', 'localizacao' => 'Trecho Norte - Km 89', 'tipo' => 'Falha no sensor', 'status' => 'Alerta'],
    ['id' => '015', 'localizacao' => 'Estação Vale do Sol', 'tipo' => 'Superaquecimento', 'status' => 'Alerta'],
    ['id' => '016', 'localizacao' => 'Pátio Técnico Leste', 'tipo' => 'Falha elétrica', 'status' => 'Alerta'],
    ['id' => '017', 'localizacao' => 'Trecho Sul - Km 132', 'tipo' => 'Baixa pressão', 'status' => 'Alerta'],
];
$quantidadeAlertas = count(array_filter($sensores, fn(array $sensor): bool => $sensor['status'] === 'Alerta'));
function e(string $texto): string { return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Ferroma</title>
    <link rel="stylesheet" href="../assets/style/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <header class="cabecalho">
        <h2><i class="bi bi-train-front-fill"></i> Bem-vindo, <?= e($administrador) ?></h2>
        <a href="login.html" class="item" aria-label="Sair"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </header>
    <div class="layout">
        <aside class="menu-lateral" aria-label="Menu principal">
            <a href="home.php" class="item ativo"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a href="cadastrar_sensores.html" class="item"><i class="bi bi-cpu-fill"></i> Sensores</a>
            <a href="#" class="item"><i class="bi bi-file-earmark-bar-graph-fill"></i> Relatórios</a>
            <a href="tela_de_cadastro.html" class="item"><i class="bi bi-person-plus-fill"></i> Cadastrar usuários</a>
            <a href="usuarios.html" class="item"><i class="bi bi-people-fill"></i> Usuários cadastrados</a>
        </aside>
        <main class="conteudo">
            <section class="informacoes_dashboard" aria-label="Resumo do sistema">
                <div class="informacoes"><h4><i class="bi bi-cpu-fill"></i> Sensores ativos</h4><h3>148</h3></div>
                <div class="informacoes"><h4><i class="bi bi-train-front-fill"></i> Trens em operação</h4><h3>17</h3></div>
                <div class="informacoes"><h4><i class="bi bi-exclamation-triangle-fill"></i> Alertas</h4><h3><?= $quantidadeAlertas ?></h3></div>
                <div class="informacoes"><h4><i class="bi bi-check-circle-fill"></i> Disponibilidade</h4><h3>99,7%</h3></div>
            </section>
            <section class="planilha_dashboard" aria-label="Lista de sensores">
                <table class="table table-borderless">
                    <thead><tr><th>ID</th><th>LOCALIZAÇÃO</th><th>TIPO DE DADO</th><th>STATUS</th><th>AÇÕES</th></tr></thead>
                    <tbody>
                        <?php foreach ($sensores as $sensor): ?>
                            <?php $classeStatus = $sensor['status'] === 'Ativo' ? 'status-ativo' : 'status-alerta'; ?>
                            <tr>
                                <th scope="row">#<?= e($sensor['id']) ?></th>
                                <td><?= e($sensor['localizacao']) ?></td>
                                <td><?= e($sensor['tipo']) ?></td>
                                <td class="<?= $classeStatus ?>"><?= e($sensor['status']) ?></td>
                                <td>
                                    <a class="botao_dashboard" href="monitoramento_tempo_real.php?id=<?= urlencode($sensor['id']) ?>" aria-label="Monitorar sensor <?= e($sensor['id']) ?>"><i class="bi bi-eye-fill"></i></a>
                                    <a class="botao_dashboard" href="excluir_informacoes_trem.php?id=<?= urlencode($sensor['id']) ?>" aria-label="Excluir sensor <?= e($sensor['id']) ?>"><i class="bi bi-trash-fill"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
