
<?php

require_once "../infra/auth.php";
exigir_login();

require_once "../infra/conexao.php";

$mensagem_login = "";

if (isset($_SESSION["mensagem_login"])) {
    $mensagem_login = $_SESSION["mensagem_login"];
    unset($_SESSION["mensagem_login"]);
}

// Consulta os sensores cadastrados
$sensores = $conexao->query(
    "SELECT id, nome, identificacao, localizacao, tipo_sensor
     FROM sensores ORDER BY nome"
)->fetch_all(MYSQLI_ASSOC);

// Verifica se o usuário é administrador
$ehAdministrador = $_SESSION["usuario"]["tipo_usuario"] === "Administrador";

// Consulta a quantidade de usuários por tipo
$usuarios = ["Administrador" => 0, "Funcionario" => 0];

if ($ehAdministrador) {
    $resultado = $conexao->query(
        "SELECT tipo_usuario, COUNT(*) total
         FROM usuarios
         GROUP BY tipo_usuario"
    );

    while ($usuario = $resultado->fetch_assoc()) {
        if (isset($usuarios[$usuario["tipo_usuario"]])) {
            $usuarios[$usuario["tipo_usuario"]] = (int)$usuario["total"];
        }
    }
}

// Consulta os dados dos trens
$resultado = $conexao->query(
    "SELECT COUNT(*) total,
            SUM(status = 'Ativo') ativos,
            SUM(status = 'Em Manutenção') manutencao
     FROM trens"
)->fetch_assoc();

$totalTrens = (int)$resultado["total"];
$trensAtivos = (int)$resultado["ativos"];
$manutencao = (int)$resultado["manutencao"];

// Calcula a disponibilidade dos trens
$disponibilidade = $totalTrens > 0
    ? round(($trensAtivos / $totalTrens) * 100, 1)
    : 0;

// Função para proteger a exibição de dados
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

   <link rel="stylesheet" href="../assets/style/style.css?v=20261111-cep">
</head>

<body>

    <?php if ($mensagem_login != ""): ?>
        <script>
            alert("<?php echo $mensagem_login; ?>");
        </script>
    <?php endif; ?>

    <!-- CABEÇALHO -->
    <header class="cabecalho">

        <h2>
            Olá, <?= escapar($_SESSION["usuario"]["nome"]) ?>
        </h2>

        <form method="post" action="logout.php"
            onsubmit="return confirm('Tem certeza de que deseja sair do sistema?');">

            <input type="hidden" name="token_csrf"
                value="<?= escapar(token_csrf()) ?>">

            <button type="submit" class="item">Sair</button>

        </form>

    </header>

    <div class="layout">

        <!-- MENU LATERAL -->
        <aside class="menu-lateral">

            <a href="home.php" class="item ativo">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            <a href="crud_sensor.php" class="item">
                <i class="bi bi-cpu-fill"></i> Sensores
            </a>

            <a href="crud_trens.php" class="item">
                <i class="bi bi-train-front-fill"></i> Trens
            </a>

            <?php if ($_SESSION["usuario"]["tipo_usuario"] === "Administrador"): ?>
                <a href="crud_usuarios.php" class="item">
                    <i class="bi bi-people-fill"></i> Usuários
                </a>
            <?php endif; ?>

            <a href="crud_viagens.php" class="item">
                <i class="bi bi-calendar-check-fill"></i> Viagens
            </a>

        </aside>

        <!-- CONTEÚDO PRINCIPAL -->
        <main class="conteudo">

            <!-- APRESENTAÇÃO -->
            <section class="dashboard-apresentacao">

                <div>

                    <p class="dashboard-sobrelinha">CENTRAL DE CONTROLE</p>

                    <?php if ($ehAdministrador): ?>

                        <p class="dashboard-boas-vindas dashboard-boas-vindas-admin">
                            Bem-vindo à tela de administrador,
                            <?= e($_SESSION["usuario"]["nome"]) ?>!
                        </p>

                    <?php else: ?>

                        <p class="dashboard-boas-vindas">
                            Bem-vindo, <?= e($_SESSION["usuario"]["nome"]) ?>.
                            Acompanhe o estado da operação.
                        </p>

                    <?php endif; ?>

                </div>

                <div class="dashboard-atualizacao">

                    <span class="dashboard-ponto"></span>

                    <span id="ultima-atualizacao" aria-live="off">
                        --:--:--
                    </span>

                </div>

            </section>

            <!-- RESUMO DE USUÁRIOS -->
            <?php if ($ehAdministrador): ?>

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

                        <a class="dashboard-usuarios-link" href="crud_usuarios.php">
                            Gerenciar usuários
                        </a>

                    </div>

                </section>

            <?php endif; ?>

            <!-- CARDS DE INFORMAÇÕES -->
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

                    <strong>
                        <?= number_format($disponibilidade, 1, ",", "") ?>%
                    </strong>

                    <div class="dashboard-progresso">
                        <span style="width: <?= $disponibilidade ?>%"></span>
                    </div>

                    <small>Percentual de trens ativos</small>

                </article>

            </section>

            <!-- CLIMA E CONSULTA DE CEP -->
            <div class="dashboard-recursos">

                <!-- CARD DO CLIMA -->
                <section class="dashboard-clima" aria-labelledby="clima-titulo">

                    <div class="dashboard-clima-cabecalho">

                        <div>

                            <p class="dashboard-sobrelinha">CONDIÇÕES LOCAIS</p>

                            <h2 id="clima-titulo">
                                Clima atual em Joinville
                            </h2>

                            <p class="dashboard-clima-local">
                                Joinville, Santa Catarina
                            </p>

                        </div>

                    </div>

                    <div class="dashboard-clima-dados"
                        id="clima-dados"
                        aria-live="polite"
                        aria-atomic="true">

                        <strong class="dashboard-clima-temperatura"
                            id="clima-temperatura">
                            --°
                        </strong>

                        <div class="dashboard-clima-resumo">

                            <span id="clima-condicao">
                                Consultando o clima...
                            </span>

                            <small>
                                Sensação de
                                <span id="clima-sensacao">--°</span>
                            </small>

                        </div>

                        <div class="dashboard-clima-detalhe">

                            <span>
                                Umidade
                                <strong id="clima-umidade">--%</strong>
                            </span>

                            <span>
                                Vento
                                <strong id="clima-vento">-- km/h</strong>
                            </span>

                        </div>

                    </div>

                    <div class="dashboard-clima-rodape">

                        <small id="clima-atualizado">
                            Aguardando dados meteorológicos
                        </small>

                        <a href="https://open-meteo.com/"
                            target="_blank"
                            rel="noopener noreferrer">
                            Dados: Open-Meteo
                        </a>

                    </div>

                </section>

                <!-- CARD DA CONSULTA DE CEP -->
                <section class="dashboard-cep">

                    <div>

                        <p class="dashboard-sobrelinha">
                            CONSULTA DE ENDEREÇO
                        </p>

                        <h2>Consultar CEP</h2>

                        <p class="dashboard-cep-descricao">
                            Pesquise um CEP para encontrar o endereço correspondente.
                        </p>

                    </div>

                    <form id="form-cep">

                        <label for="cep">CEP:</label>

                        <div class="dashboard-cep-campo">

                            <input
                                type="text"
                                id="cep"
                                name="cep"
                                placeholder="Ex.: 89201-000"
                                maxlength="9"
                                inputmode="numeric"
                                required
                            >

                            <button type="submit">
                                Consultar
                            </button>

                        </div>

                    </form>

                    <div id="resultado-cep"
                        class="resultado-cep"
                        aria-live="polite">

                        <p>O endereço consultado aparecerá aqui.</p>

                    </div>

                </section>

            </div>

            <!-- TABELA DE SENSORES -->
            <section class="planilha_dashboard">

                <div class="dashboard-tabela-cabecalho">

                    <div>

                        <p class="dashboard-sobrelinha">MONITORAMENTO</p>

                        <h2>Sensores do sistema</h2>

                    </div>

                    <?php if ($ehAdministrador): ?>

                        <a href="crud_sensor.php" class="dashboard-link">
                            Gerenciar sensores
                        </a>

                    <?php endif; ?>

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

    <!-- SCRIPTS -->
    <script src="../scripts/script_home.js?v=20261109-clima-sem-emoji"></script>
    <script src="../scripts/script_cep.js"></script>

</body>
</html>
