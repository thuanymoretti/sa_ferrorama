
<?php

require_once "../infra/auth.php";
exigir_login();

require_once "../infra/conexao.php";

$ehAdministrador = ($_SESSION["usuario"]["tipo_usuario"] ?? "") === "Administrador";

$mensagem = "";
$erro = "";
$csrf = token_csrf();
$flash = obter_flash();

if ($flash) {
    if ($flash["tipo"] === "sucesso") {
        $mensagem = $flash["mensagem"];
    } else {
        $erro = $flash["mensagem"];
    }
}


// CADASTRAR VIAGEM
if (isset($_POST["cadastrar"])) {

    exigir_administrador($conexao);
    validar_token_csrf();

    $trem_id = (int) $_POST["trem_id"];
    $data_inicio = str_replace("T", " ", $_POST["data_inicio"]) . ":00";
    $data_chegada = str_replace("T", " ", $_POST["data_chegada"]) . ":00";
    $origem = trim($_POST["origem"] ?? "");
    $destino = trim($_POST["destino"] ?? "");
    $status = $_POST["status"] ?? "";

    $statusPermitidos = ["Em Andamento", "Concluída", "Cancelada"];

    if (
        $trem_id > 0 &&
        !empty($_POST["data_inicio"]) &&
        !empty($_POST["data_chegada"]) &&
        $origem !== "" &&
        $destino !== "" &&
        in_array($status, $statusPermitidos, true)
    ) {

        $sql = "INSERT INTO viagens
                (trem_id, data_inicio, data_chegada, origem, destino, status)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "isssss",
            $trem_id,
            $data_inicio,
            $data_chegada,
            $origem,
            $destino,
            $status
        );

        if ($stmt->execute()) {
            $mensagem = "Viagem cadastrada com sucesso!";
        } else {
            $erro = "Erro ao cadastrar viagem.";
        }

        $stmt->close();

    } else {
        $erro = "Preencha todos os campos corretamente.";
    }
}


// EXCLUIR VIAGEM
if (isset($_POST["excluir"])) {

    exigir_administrador($conexao);
    validar_token_csrf();

    $id = filter_var(valor_post("excluir"), FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit("Identificador de viagem inválido.");
    }

    $sql = "DELETE FROM viagens WHERE id = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $mensagem = "Viagem excluída com sucesso!";
    } else {
        $erro = "Erro ao excluir viagem.";
    }

    $stmt->close();
}


// BUSCAR VIAGENS
$sqlViagens = "
    SELECT
        viagens.id,
        viagens.trem_id,
        viagens.data_inicio,
        viagens.data_chegada,
        viagens.origem,
        viagens.destino,
        viagens.status,
        trens.identificacao,
        trens.modelo
    FROM viagens
    INNER JOIN trens ON viagens.trem_id = trens.id
    ORDER BY viagens.data_inicio DESC
";

$viagens = $conexao->query($sqlViagens);


// BUSCAR TRENS PARA OS MODAIS
$trens = $conexao->query(
    "SELECT id, identificacao, modelo
     FROM trens
     ORDER BY identificacao"
)->fetch_all(MYSQLI_ASSOC);


// FUNÇÃO PARA EXIBIR OS DADOS COM SEGURANÇA
function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, "UTF-8");
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Viagens | Ferrorama</title>

    <link rel="stylesheet" href="../assets/style/style.css">

</head>

<body>

    <!-- CABEÇALHO -->
    <header class="cabecalho">

        <h2>Olá, <?= escapar($_SESSION["usuario"]["nome"]) ?></h2>

        <a href="home.php" class="item">Voltar ao painel</a>

    </header>


    <div class="layout">

        <!-- MENU LATERAL -->
        <aside class="menu-lateral">

            <a href="home.php" class="item">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            <a href="crud_sensor.php" class="item">
                <i class="bi bi-cpu-fill"></i> Sensores
            </a>

            <a href="crud_trens.php" class="item">
                <i class="bi bi-train-front-fill"></i> Trens
            </a>

            <?php if ($ehAdministrador): ?>

                <a href="crud_usuarios.php" class="item">
                    <i class="bi bi-people-fill"></i> Usuários
                </a>

            <?php endif; ?>

            <a href="crud_viagens.php" class="item ativo">
                <i class="bi bi-calendar-check-fill"></i> Viagens
            </a>

        </aside>


        <!-- CONTEÚDO PRINCIPAL -->
        <main class="conteudo">

            <div class="titulo_sensores">

                <div>

                    <h1>
                        <i class="bi bi-map-fill"></i> Viagens dos Trens
                    </h1>

                    <p>
                        <?= $ehAdministrador
                            ? "Cadastre e acompanhe as viagens realizadas pelos trens."
                            : "Acompanhe as viagens realizadas pelos trens." ?>
                    </p>

                </div>

            </div>


            <!-- MENSAGENS -->
            <?php if ($mensagem !== ""): ?>

                <p class="mensagem-sucesso">
                    <?= e($mensagem) ?>
                </p>

            <?php endif; ?>


            <?php if ($erro !== ""): ?>

                <p class="mensagem-erro">
                    <?= e($erro) ?>
                </p>

            <?php endif; ?>


            <!-- TABELA DE VIAGENS -->
            <section class="planilha_dashboard">

                <div class="cabecalho-tabela">

                    <h2>
                        <i class="bi bi-list-ul"></i>
                        Viagens cadastradas
                    </h2>

                    <?php if ($ehAdministrador): ?>

                        <button type="button" onclick="abrirModalCadastro()">
                            <i class="bi bi-plus-circle-fill"></i>
                            Cadastrar nova viagem
                        </button>

                    <?php endif; ?>

                </div>


                <div class="table-responsive">

                    <table class="table table-borderless">

                        <thead>

                            <tr>

                                <th>ID</th>
                                <th>TREM</th>
                                <th>MODELO</th>
                                <th>ORIGEM</th>
                                <th>DESTINO</th>
                                <th>INÍCIO</th>
                                <th>CHEGADA</th>
                                <th>STATUS</th>

                                <?php if ($ehAdministrador): ?>
                                    <th>AÇÕES</th>
                                <?php endif; ?>

                            </tr>

                        </thead>


                        <tbody>

                            <?php if ($viagens && $viagens->num_rows > 0): ?>

                                <?php while ($viagem = $viagens->fetch_assoc()): ?>

                                    <tr>

                                        <td>
                                            #<?= (int) $viagem["id"] ?>
                                        </td>

                                        <td>
                                            <?= e($viagem["identificacao"]) ?>
                                        </td>

                                        <td>
                                            <?= e($viagem["modelo"]) ?>
                                        </td>

                                        <td>
                                            <?= e($viagem["origem"]) ?>
                                        </td>

                                        <td>
                                            <?= e($viagem["destino"]) ?>
                                        </td>

                                        <td>
                                            <?= date("d/m/Y H:i", strtotime($viagem["data_inicio"])) ?>
                                        </td>

                                        <td>
                                            <?= date("d/m/Y H:i", strtotime($viagem["data_chegada"])) ?>
                                        </td>

                                        <td>
                                            <?= e($viagem["status"]) ?>
                                        </td>


                                        <?php if ($ehAdministrador): ?>

                                            <td class="acoes">

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-warning"
                                                    onclick="abrirModalEdicao(this)"
                                                    data-id="<?= (int) $viagem["id"] ?>"
                                                    data-trem="<?= (int) $viagem["trem_id"] ?>"
                                                    data-inicio="<?= date("Y-m-d\TH:i", strtotime($viagem["data_inicio"])) ?>"
                                                    data-chegada="<?= date("Y-m-d\TH:i", strtotime($viagem["data_chegada"])) ?>"
                                                    data-origem="<?= e($viagem["origem"]) ?>"
                                                    data-destino="<?= e($viagem["destino"]) ?>"
                                                    data-status="<?= e($viagem["status"]) ?>">

                                                    <i class="bi bi-pencil-fill"></i> Editar

                                                </button>


                                                <form
                                                    method="post"
                                                    onsubmit="return confirm('Deseja realmente excluir esta viagem?')"
                                                    style="display:inline">

                                                    <input
                                                        type="hidden"
                                                        name="token_csrf"
                                                        value="<?= escapar($csrf) ?>">

                                                    <button
                                                        type="submit"
                                                        name="excluir"
                                                        value="<?= (int) $viagem["id"] ?>"
                                                        class="btn-excluir">

                                                        Excluir

                                                    </button>

                                                </form>

                                            </td>

                                        <?php endif; ?>

                                    </tr>

                                <?php endwhile; ?>


                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="<?= $ehAdministrador ? 9 : 8 ?>"
                                        class="text-center">

                                        Nenhuma viagem cadastrada.

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>


                <!-- MODAIS -->
                <?php if ($ehAdministrador): ?>


                    <!-- MODAL DE EDIÇÃO -->
                    <div class="modal-edicao" id="modalEdicao" hidden>

                        <div class="conteudo-modal">

                            <div class="cabecalho-modal">
                                <h2>Editar viagem</h2>
                            </div>

                            <form method="post" id="formEditarViagem">

                                <input
                                    type="hidden"
                                    name="token_csrf"
                                    value="<?= escapar($csrf) ?>">

                                <div class="campo-modal">

                                    <label for="editar_trem">Trem</label>

                                    <select name="trem_id" id="editar_trem" required>

                                        <option value="">Selecione um trem</option>

                                        <?php foreach ($trens as $trem): ?>

                                            <option value="<?= (int) $trem["id"] ?>">
                                                <?= e($trem["identificacao"] . " — " . $trem["modelo"]) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>


                                <div class="campo-modal">

                                    <label for="editar_status">Status da viagem</label>

                                    <select name="status" id="editar_status" required>

                                        <option value="Em Andamento">Em Andamento</option>
                                        <option value="Concluída">Concluída</option>
                                        <option value="Cancelada">Cancelada</option>

                                    </select>

                                </div>


                                <div class="campo-modal">

                                    <label for="editar_inicio">Data e hora de partida</label>

                                    <input
                                        type="datetime-local"
                                        name="data_inicio"
                                        id="editar_inicio"
                                        required>

                                </div>


                                <div class="campo-modal">

                                    <label for="editar_chegada">Data e hora de chegada</label>

                                    <input
                                        type="datetime-local"
                                        name="data_chegada"
                                        id="editar_chegada"
                                        required>

                                </div>


                                <div class="campo-modal">

                                    <label for="editar_origem">Origem</label>

                                    <input
                                        type="text"
                                        name="origem"
                                        id="editar_origem"
                                        maxlength="100"
                                        required>

                                </div>


                                <div class="campo-modal">

                                    <label for="editar_destino">Destino</label>

                                    <input
                                        type="text"
                                        name="destino"
                                        id="editar_destino"
                                        maxlength="100"
                                        required>

                                </div>


                                <div class="botoes-modal">

                                    <button
                                        type="button"
                                        class="btn-cancelar-modal"
                                        onclick="fecharModalEdicao()">

                                        Cancelar

                                    </button>

                                    <button
                                        type="submit"
                                        class="btn-salvar-modal">

                                        Salvar alterações

                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>


                    <!-- MODAL DE CADASTRO -->
                    <div class="modal-edicao" id="modalCadastro" hidden>

                        <div class="conteudo-modal">

                            <div class="cabecalho-modal">
                                <h2>Cadastrar nova viagem</h2>
                            </div>

                            <form method="post" action="crud_viagens.php">

                                <input
                                    type="hidden"
                                    name="token_csrf"
                                    value="<?= escapar($csrf) ?>">

                                <div class="campo-modal">

                                    <label for="cadastro_trem">Trem</label>

                                    <select name="trem_id" id="cadastro_trem" required>

                                        <option value="">Selecione um trem</option>

                                        <?php foreach ($trens as $trem): ?>

                                            <option value="<?= (int) $trem["id"] ?>">
                                                <?= e($trem["identificacao"] . " — " . $trem["modelo"]) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>


                                <div class="campo-modal">

                                    <label for="cadastro_status">Status da viagem</label>

                                    <select name="status" id="cadastro_status" required>

                                        <option value="">Selecione o status</option>
                                        <option value="Em Andamento">Em Andamento</option>
                                        <option value="Concluída">Concluída</option>
                                        <option value="Cancelada">Cancelada</option>

                                    </select>

                                </div>


                                <div class="campo-modal">

                                    <label for="cadastro_inicio">Data e hora de partida</label>

                                    <input
                                        type="datetime-local"
                                        name="data_inicio"
                                        id="cadastro_inicio"
                                        required>

                                </div>


                                <div class="campo-modal">

                                    <label for="cadastro_chegada">Data e hora de chegada</label>

                                    <input
                                        type="datetime-local"
                                        name="data_chegada"
                                        id="cadastro_chegada"
                                        required>

                                </div>


                                <div class="campo-modal">

                                    <label for="cadastro_origem">Origem</label>

                                    <input
                                        type="text"
                                        name="origem"
                                        id="cadastro_origem"
                                        maxlength="100"
                                        required>

                                </div>


                                <div class="campo-modal">

                                    <label for="cadastro_destino">Destino</label>

                                    <input
                                        type="text"
                                        name="destino"
                                        id="cadastro_destino"
                                        maxlength="100"
                                        required>

                                </div>


                                <div class="botoes-modal">

                                    <button
                                        type="button"
                                        class="btn-cancelar-modal"
                                        onclick="fecharModalCadastro()">

                                        Cancelar

                                    </button>

                                    <button
                                        type="submit"
                                        name="cadastrar"
                                        value="1"
                                        class="btn-salvar-modal">

                                        Cadastrar viagem

                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                <?php endif; ?>

            </section>


            <!-- LOCAIS FERROVIÁRIOS -->
            <section class="mapa-ferrorama">

                <div class="mapa-cabecalho">

                    <div>

                        <h2>
                            <i class="bi bi-geo-alt-fill"></i>
                            Locais ferroviários
                        </h2>

                        <p>
                            Encontre estações, museus ferroviários e locais relacionados a trens.
                        </p>

                    </div>


                    <a
                        href="https://www.google.com/maps/search/ferromodelismo/"
                        target="_blank"
                        class="botao-mapa">

                        <i class="bi bi-google"></i>
                        Abrir no Google Maps

                    </a>

                </div>


                <div class="mapa-links">

                    <a
                        href="https://www.google.com/maps/search/museu+ferroviário/"
                        target="_blank">

                        <i class="bi bi-building"></i>
                        Museus ferroviários

                    </a>


                    <a
                        href="https://www.google.com/maps/search/estação+ferroviária/"
                        target="_blank">

                        <i class="bi bi-train-front"></i>
                        Estações ferroviárias

                    </a>


                    <a
                        href="https://www.google.com/maps/search/ferromodelismo/"
                        target="_blank">

                        <i class="bi bi-geo-alt"></i>
                        Ferromodelismo

                    </a>

                </div>

            </section>

        </main>

    </div>


    <!-- JAVASCRIPT DOS MODAIS -->
    <script>

        // ABRIR MODAL DE CADASTRO
        function abrirModalCadastro() {
            document.getElementById("modalCadastro").hidden = false;
            document.body.style.overflow = "hidden";
        }


        // FECHAR MODAL DE CADASTRO
        function fecharModalCadastro() {
            document.getElementById("modalCadastro").hidden = true;
            document.body.style.overflow = "";
        }


        // ABRIR MODAL DE EDIÇÃO
        function abrirModalEdicao(botao) {

            document.getElementById("editar_trem").value = botao.dataset.trem;
            document.getElementById("editar_inicio").value = botao.dataset.inicio;
            document.getElementById("editar_chegada").value = botao.dataset.chegada;
            document.getElementById("editar_origem").value = botao.dataset.origem;
            document.getElementById("editar_destino").value = botao.dataset.destino;
            document.getElementById("editar_status").value = botao.dataset.status;

            document.getElementById("formEditarViagem").action =
                "editar_viagem.php?id=" + botao.dataset.id;

            document.getElementById("modalEdicao").hidden = false;
            document.body.style.overflow = "hidden";
        }


        // FECHAR MODAL DE EDIÇÃO
        function fecharModalEdicao() {
            document.getElementById("modalEdicao").hidden = true;
            document.body.style.overflow = "";
        }


        // FECHAR AO CLICAR FORA
        document.getElementById("modalCadastro")?.addEventListener("click", function(event) {

            if (event.target === this) {
                fecharModalCadastro();
            }

        });


        document.getElementById("modalEdicao")?.addEventListener("click", function(event) {

            if (event.target === this) {
                fecharModalEdicao();
            }

        });


        // FECHAR COM ESC
        document.addEventListener("keydown", function(event) {

            if (event.key === "Escape") {
                fecharModalCadastro();
                fecharModalEdicao();
            }

        });

    </script>

</body>

</html>
