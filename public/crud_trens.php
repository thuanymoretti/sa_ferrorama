
<?php

require_once "../infra/auth.php";
exigir_login();

require_once "../infra/conexao.php";

$usuario = $_SESSION["usuario"];
$ehAdministrador = $usuario["tipo_usuario"] === "Administrador";

$csrf = token_csrf();
$mensagem = obter_flash();

$erroCadastro = "";
$erroEdicao = "";

$dadosCadastro = [
    "identificacao" => "",
    "modelo" => "",
    "capacidade" => "",
    "usuario_id" => "",
    "status" => ""
];

$dadosEdicao = [
    "id" => "",
    "identificacao" => "",
    "modelo" => "",
    "capacidade" => "",
    "usuario_id" => "",
    "status" => ""
];

$sensoresEdicao = [];

// Buscar usuários para os formulários
$usuarios = [];

if ($ehAdministrador) {
    $resultadoUsuarios = $conexao->query(
        "SELECT id, nome, email FROM usuarios ORDER BY nome"
    );

    while ($usuarioLista = $resultadoUsuarios->fetch_assoc()) {
        $usuarioLista["id"] = (int) $usuarioLista["id"];
        $usuarios[] = $usuarioLista;
    }
}

// Buscar sensores para o modal de edição
$sensoresDisponiveis = [];

if ($ehAdministrador) {
    $resultadoSensores = $conexao->query(
        "SELECT s.id, s.nome, s.identificacao, s.trem_id,
                t.identificacao AS trem_identificacao
         FROM sensores s
         LEFT JOIN trens t ON t.id = s.trem_id
         ORDER BY s.nome, s.identificacao"
    );

    while ($sensor = $resultadoSensores->fetch_assoc()) {
        $sensoresDisponiveis[] = $sensor;
    }
}

// Processar cadastro ou edição
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["acao"])) {

    exigir_administrador($conexao);
    validar_token_csrf();

    $acao = $_POST["acao"];

    $identificacao = trim(valor_post("identificacao"));
    $modelo = trim(valor_post("modelo"));
    $capacidadeTexto = valor_post("capacidade");
    $capacidade = filter_var($capacidadeTexto, FILTER_VALIDATE_INT);
    $usuarioId = filter_var(valor_post("usuario_id"), FILTER_VALIDATE_INT);
    $status = valor_post("status");

    $dadosFormulario = [
        "identificacao" => $identificacao,
        "modelo" => $modelo,
        "capacidade" => $capacidadeTexto,
        "usuario_id" => $usuarioId,
        "status" => $status
    ];

    // Validar campos em comum
    $erro = "";

    if (
        $identificacao === "" ||
        strlen($identificacao) > 100 ||
        $modelo === "" ||
        strlen($modelo) > 50 ||
        $capacidade === false ||
        $capacidade < 1 ||
        $usuarioId === false ||
        $usuarioId < 1 ||
        !in_array($usuarioId, array_column($usuarios, "id"), true) ||
        !in_array($status, ["Ativo", "Inativo", "Em Manutenção"], true)
    ) {
        $erro = "Preencha todos os campos corretamente.";
    }

    // CADASTRAR TREM
    if ($acao === "cadastrar") {

        $dadosCadastro = $dadosFormulario;

        if ($erro === "") {
            try {
                $stmt = $conexao->prepare(
                    "INSERT INTO trens
                    (identificacao, modelo, capacidade, status, usuario_id)
                    VALUES (?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "ssisi",
                    $identificacao,
                    $modelo,
                    $capacidade,
                    $status,
                    $usuarioId
                );

                $stmt->execute();
                $stmt->close();

                definir_flash("sucesso", "Trem cadastrado com sucesso.");

                header("Location: crud_trens.php");
                exit;

            } catch (mysqli_sql_exception $falha) {
                error_log("Falha ao cadastrar trem: " . $falha->getMessage());
                $erro = "Não foi possível cadastrar o trem.";
            }
        }

        $erroCadastro = $erro;
    }

    // EDITAR TREM
    elseif ($acao === "editar") {

        $id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);

        $dadosEdicao = $dadosFormulario;
        $dadosEdicao["id"] = $id;

        // Validar a seleção dos sensores
        $listaPostada = $_POST["sensores"] ?? [];

        if (!is_array($listaPostada)) {
            $listaPostada = [];
            $erro = "A seleção de sensores é inválida.";
        }

        foreach ($listaPostada as $sensorIdTexto) {
            $sensorId = filter_var($sensorIdTexto, FILTER_VALIDATE_INT);

            if (!$sensorId || $sensorId < 1) {
                $erro = "A seleção de sensores é inválida.";
                break;
            }

            $sensoresEdicao[] = (int) $sensorId;
        }

        $sensoresEdicao = array_values(array_unique($sensoresEdicao));

        if (!$id || $id < 1) {
            $erro = "Não foi possível identificar o trem.";
        }

        if ($erro === "") {
            try {
                $conexao->begin_transaction();

                // Verificar se o trem existe
                $stmt = $conexao->prepare(
                    "SELECT id FROM trens WHERE id = ? FOR UPDATE"
                );

                $stmt->bind_param("i", $id);
                $stmt->execute();

                $tremExiste = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$tremExiste) {
                    throw new RuntimeException("Este trem não existe mais.");
                }

                // Verificar se os sensores selecionados existem
                $verificarSensor = $conexao->prepare(
                    "SELECT id FROM sensores WHERE id = ? FOR UPDATE"
                );

                foreach ($sensoresEdicao as $sensorId) {
                    $verificarSensor->bind_param("i", $sensorId);
                    $verificarSensor->execute();

                    $sensorAtual = $verificarSensor->get_result()->fetch_assoc();

                    if (!$sensorAtual) {
                        throw new RuntimeException(
                            "Um dos sensores escolhidos não existe mais."
                        );
                    }
                }

                $verificarSensor->close();

                // Atualizar os dados do trem
                $stmt = $conexao->prepare(
                    "UPDATE trens
                     SET identificacao = ?,
                         modelo = ?,
                         capacidade = ?,
                         status = ?,
                         usuario_id = ?
                     WHERE id = ?"
                );

                $stmt->bind_param(
                    "ssisii",
                    $identificacao,
                    $modelo,
                    $capacidade,
                    $status,
                    $usuarioId,
                    $id
                );

                $stmt->execute();
                $stmt->close();

                // Remover os vínculos antigos
                $stmt = $conexao->prepare(
                    "UPDATE sensores
                     SET trem_id = NULL
                     WHERE trem_id = ?"
                );

                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();

                // Vincular os sensores selecionados
                $vincularSensor = $conexao->prepare(
                    "UPDATE sensores
                     SET trem_id = ?
                     WHERE id = ?"
                );

                foreach ($sensoresEdicao as $sensorId) {
                    $vincularSensor->bind_param("ii", $id, $sensorId);
                    $vincularSensor->execute();
                }

                $vincularSensor->close();

                $conexao->commit();

                definir_flash(
                    "sucesso",
                    "Trem e sensores atualizados com sucesso."
                );

                header("Location: crud_trens.php");
                exit;

            } catch (Throwable $falha) {

                $conexao->rollback();

                error_log(
                    "Falha ao atualizar trem {$id}: " . $falha->getMessage()
                );

                $erro = $falha instanceof RuntimeException
                    ? $falha->getMessage()
                    : "Não foi possível atualizar o trem e seus dados.";
            }
        }

        $erroEdicao = $erro;
    }
}

// Consultar trens cadastrados
$resultado = $conexao->query(
    "SELECT
        t.id,
        t.identificacao,
        t.modelo,
        t.capacidade,
        t.status,
        t.usuario_id,
        u.nome AS usuario_responsavel,

        GROUP_CONCAT(
            DISTINCT CONCAT(s.nome, ' (', s.identificacao, ')')
            ORDER BY s.nome SEPARATOR ', '
        ) AS sensores_vinculados,

        GROUP_CONCAT(
            DISTINCT s.id ORDER BY s.id
        ) AS sensores_ids

     FROM trens t

     LEFT JOIN usuarios u ON u.id = t.usuario_id
     LEFT JOIN sensores s ON s.trem_id = t.id

     GROUP BY
        t.id,
        t.identificacao,
        t.modelo,
        t.capacidade,
        t.status,
        t.usuario_id,
        u.nome

     ORDER BY t.identificacao"
);

if (
    $mensagem &&
    str_contains($mensagem["mensagem"], "sensores ou viagens vinculados")
) {
    $mensagem["mensagem"] = "A exclusão foi bloqueada porque este trem ainda possui uma viagem vinculada. Confira a tela Viagens.";
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Trens cadastrados | Ferrorama</title>

    <link rel="stylesheet" href="../assets/style/style.css">

    <style>
        /* Fundo escurecido do modal */
        .modal-trens {
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            padding: 0;
            border: none;
            border-radius: 12px;
            background: #2d394d;
            color: white;
            overflow-y: auto;
        }

        .modal-trens::backdrop {
            background: rgba(0, 0, 0, 0.75);
        }

        .modal-trens-conteudo {
            padding: 25px;
        }

        .modal-trens h2 {
            margin: 0 0 15px;
            color: white;
            font-size: 22px;
        }

        .modal-trens .grupo-input {
            margin-bottom: 15px;
        }

        .modal-trens label {
            display: block;
            margin-bottom: 5px;
            color: white;
            font-weight: 600;
        }

        .modal-trens input:not([type="checkbox"]),
        .modal-trens select {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 7px;
            background: #f5f0e7;
            color: #263246;
        }

        .modal-trens .lista-sensores-vinculados {
            max-height: 180px;
            overflow-y: auto;
            padding: 12px;
            border: 1px solid #657187;
            border-radius: 7px;
        }

        .modal-trens .lista-sensores-vinculados legend {
            padding: 0 5px;
            color: white;
            font-weight: bold;
        }

        .modal-trens .lista-sensores-vinculados p {
            font-size: 13px;
            color: #e0e4eb;
        }

        .modal-trens .opcao-sensor-vinculado {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin: 10px 0;
            font-size: 13px;
            font-weight: normal;
        }

        .modal-trens .opcao-sensor-vinculado input {
            margin-top: 3px;
        }

        .modal-trens-acoes {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 25px;
        }

        .modal-trens-acoes button {
            padding: 10px 18px;
            border: none;
            border-radius: 7px;
            color: white;
            cursor: pointer;
        }

        .modal-trens .btn-cancelar-modal {
            background: #a51d1d;
        }

        .modal-trens .btn-salvar-modal {
            background: #438b50;
        }

        .modal-trens .mensagem-erro {
            margin-bottom: 15px;
        }

        .btn-abrir-modal {
            cursor: pointer;
        }

        @media (max-width: 600px) {
            .modal-trens {
                width: 95%;
            }

            .modal-trens-conteudo {
                padding: 18px;
            }
        }
    </style>

</head>

<body>

    <header class="cabecalho">

        <h2>Olá, <?= escapar((string) $usuario["nome"]) ?></h2>

        <a href="home.php" class="item">Voltar ao painel</a>

    </header>

    <div class="layout">

        <aside class="menu-lateral">

            <a href="home.php" class="item">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            <a href="crud_sensor.php" class="item">
                <i class="bi bi-cpu-fill"></i> Sensores
            </a>

            <a href="crud_trens.php" class="item ativo">
                <i class="bi bi-train-front-fill"></i> Trens
            </a>

            <?php if ($ehAdministrador): ?>
                <a href="crud_usuarios.php" class="item">
                    <i class="bi bi-people-fill"></i> Usuários
                </a>
            <?php endif; ?>

            <a href="crud_viagens.php" class="item">
                <i class="bi bi-calendar-check-fill"></i> Viagens
            </a>

        </aside>

        <main class="conteudo">

            <div class="titulo_sensores">
                <h1>Visualização de Trens</h1>
            </div>

            <?php if ($mensagem): ?>

                <p class="mensagem-<?= escapar($mensagem["tipo"]) ?>" role="status">
                    <?= escapar($mensagem["mensagem"]) ?>
                </p>

            <?php endif; ?>

            <div class="planilha_dashboard">

                <div class="cabecalho-tabela">

                    <h2 class="titulo-tabela">
                        <i class="bi bi-list-ul"></i>
                        Trens cadastrados
                    </h2>

                    <?php if ($ehAdministrador): ?>

                        <button type="button" id="abrir-cadastro" class="btn-abrir-modal">
                            <i class="bi bi-plus-circle-fill"></i>
                            Cadastrar novo trem
                        </button>

                    <?php endif; ?>

                </div>

                <table class="table table-borderless tabela-trens">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>IDENTIFICAÇÃO</th>
                            <th>MODELO</th>
                            <th>CAPACIDADE</th>
                            <th>USUÁRIO RESPONSÁVEL</th>
                            <th>STATUS</th>
                            <th>SENSORES VINCULADOS</th>

                            <?php if ($ehAdministrador): ?>
                                <th>AÇÕES</th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($resultado->num_rows > 0): ?>

                            <?php while ($trem = $resultado->fetch_assoc()): ?>

                                <?php
                                $idsSensores = [];

                                if (!empty($trem["sensores_ids"])) {
                                    $idsSensores = array_map(
                                        "intval",
                                        explode(",", $trem["sensores_ids"])
                                    );
                                }
                                ?>

                                <tr>

                                    <td>#<?= (int) $trem["id"] ?></td>

                                    <td><?= escapar($trem["identificacao"]) ?></td>

                                    <td><?= escapar($trem["modelo"]) ?></td>

                                    <td><?= (int) $trem["capacidade"] ?></td>

                                    <td>
                                        <?= $trem["usuario_responsavel"] !== null
                                            ? escapar($trem["usuario_responsavel"])
                                            : "Sem responsável" ?>
                                    </td>

                                    <td><?= escapar($trem["status"]) ?></td>

                                    <td>
                                        <?= $trem["sensores_vinculados"] !== null
                                            ? escapar($trem["sensores_vinculados"])
                                            : "—" ?>
                                    </td>

                                    <?php if ($ehAdministrador): ?>

                                        <td class="acoes">

                                            <button
                                                type="button"
                                                class="btn-editar btn-abrir-edicao"
                                                data-id="<?= (int) $trem["id"] ?>"
                                                data-identificacao="<?= escapar($trem["identificacao"]) ?>"
                                                data-modelo="<?= escapar($trem["modelo"]) ?>"
                                                data-capacidade="<?= (int) $trem["capacidade"] ?>"
                                                data-status="<?= escapar($trem["status"]) ?>"
                                                data-usuario="<?= (int) $trem["usuario_id"] ?>"
                                                data-sensores="<?= escapar(json_encode($idsSensores)) ?>"
                                            >
                                                Editar
                                            </button>

                                            <form
                                                method="post"
                                                action="excluir_trens.php"
                                                onsubmit="return confirm('Confirma a exclusão deste trem?')"
                                                style="display:inline"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="token_csrf"
                                                    value="<?= escapar($csrf) ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $trem["id"] ?>"
                                                >

                                                <button class="btn-excluir" type="submit">
                                                    Excluir
                                                </button>

                                            </form>

                                        </td>

                                    <?php endif; ?>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="<?= $ehAdministrador ? 8 : 7 ?>" class="text-center">
                                    Nenhum trem cadastrado.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </main>

    </div>

    <?php if ($ehAdministrador): ?>

        <!-- MODAL DE CADASTRO -->
        <dialog id="modal-cadastro-trem" class="modal-trens">

            <div class="modal-trens-conteudo">

                <h2>Cadastrar trem</h2>

                <?php if ($erroCadastro !== ""): ?>
                    <p class="mensagem-erro" role="alert">
                        <?= escapar($erroCadastro) ?>
                    </p>
                <?php endif; ?>

                <form method="post">

                    <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">
                    <input type="hidden" name="acao" value="cadastrar">

                    <div class="grupo-input">
                        <label for="cad-identificacao">Identificação</label>
                        <input
                            type="text"
                            id="cad-identificacao"
                            name="identificacao"
                            maxlength="100"
                            value="<?= escapar((string) $dadosCadastro["identificacao"]) ?>"
                            required
                        >
                    </div>

                    <div class="grupo-input">
                        <label for="cad-modelo">Modelo</label>
                        <input
                            type="text"
                            id="cad-modelo"
                            name="modelo"
                            maxlength="50"
                            value="<?= escapar((string) $dadosCadastro["modelo"]) ?>"
                            required
                        >
                    </div>

                    <div class="grupo-input">
                        <label for="cad-capacidade">Capacidade</label>
                        <input
                            type="number"
                            id="cad-capacidade"
                            name="capacidade"
                            min="1"
                            value="<?= escapar((string) $dadosCadastro["capacidade"]) ?>"
                            required
                        >
                    </div>

                    <div class="grupo-input">
                        <label for="cad-usuario">Usuário responsável</label>

                        <select id="cad-usuario" name="usuario_id" required>
                            <option value="">Selecione um usuário</option>

                            <?php foreach ($usuarios as $usuarioOpcao): ?>
                                <option
                                    value="<?= (int) $usuarioOpcao["id"] ?>"
                                    <?= (string) $dadosCadastro["usuario_id"] === (string) $usuarioOpcao["id"] ? "selected" : "" ?>
                                >
                                    <?= escapar($usuarioOpcao["nome"]) ?>
                                    (<?= escapar($usuarioOpcao["email"]) ?>)
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="grupo-input">
                        <label for="cad-status">Status</label>

                        <select id="cad-status" name="status" required>
                            <option value="">Selecione</option>

                            <?php foreach (["Ativo", "Inativo", "Em Manutenção"] as $opcao): ?>
                                <option
                                    value="<?= escapar($opcao) ?>"
                                    <?= $dadosCadastro["status"] === $opcao ? "selected" : "" ?>
                                >
                                    <?= escapar($opcao) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="modal-trens-acoes">

                        <button
                            type="button"
                            class="btn-cancelar-modal fechar-modal"
                        >
                            Cancelar
                        </button>

                        <button type="submit" class="btn-salvar-modal">
                            Cadastrar
                        </button>

                    </div>

                </form>

            </div>

        </dialog>

        <!-- MODAL DE EDIÇÃO -->
        <dialog id="modal-editar-trem" class="modal-trens">

            <div class="modal-trens-conteudo">

                <h2>Editar trem</h2>

                <?php if ($erroEdicao !== ""): ?>
                    <p class="mensagem-erro" role="alert">
                        <?= escapar($erroEdicao) ?>
                    </p>
                <?php endif; ?>

                <form method="post" id="form-editar-trem">

                    <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">
                    <input type="hidden" name="acao" value="editar">
                    <input type="hidden" name="id" id="edit-id" value="<?= escapar((string) $dadosEdicao["id"]) ?>">

                    <div class="grupo-input">
                        <label for="edit-identificacao">Identificação</label>
                        <input
                            type="text"
                            id="edit-identificacao"
                            name="identificacao"
                            maxlength="100"
                            value="<?= escapar((string) $dadosEdicao["identificacao"]) ?>"
                            required
                        >
                    </div>

                    <div class="grupo-input">
                        <label for="edit-modelo">Modelo</label>
                        <input
                            type="text"
                            id="edit-modelo"
                            name="modelo"
                            maxlength="50"
                            value="<?= escapar((string) $dadosEdicao["modelo"]) ?>"
                            required
                        >
                    </div>

                    <div class="grupo-input">
                        <label for="edit-capacidade">Capacidade</label>
                        <input
                            type="number"
                            id="edit-capacidade"
                            name="capacidade"
                            min="1"
                            value="<?= escapar((string) $dadosEdicao["capacidade"]) ?>"
                            required
                        >
                    </div>

                    <div class="grupo-input">
                        <label for="edit-usuario">Usuário responsável</label>

                        <select id="edit-usuario" name="usuario_id" required>
                            <option value="">Selecione um usuário</option>

                            <?php foreach ($usuarios as $usuarioOpcao): ?>
                                <option
                                    value="<?= (int) $usuarioOpcao["id"] ?>"
                                    <?= (string) $dadosEdicao["usuario_id"] === (string) $usuarioOpcao["id"] ? "selected" : "" ?>
                                >
                                    <?= escapar($usuarioOpcao["nome"]) ?>
                                    (<?= escapar($usuarioOpcao["email"]) ?>)
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="grupo-input">
                        <label for="edit-status">Status</label>

                        <select id="edit-status" name="status" required>
                            <?php foreach (["Ativo", "Inativo", "Em Manutenção"] as $opcao): ?>
                                <option
                                    value="<?= escapar($opcao) ?>"
                                    <?= $dadosEdicao["status"] === $opcao ? "selected" : "" ?>
                                >
                                    <?= escapar($opcao) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <fieldset class="grupo-input lista-sensores-vinculados">

                        <legend>Sensores vinculados</legend>

                        <p>
                            Marque os sensores que devem ficar neste trem.
                            Um sensor selecionado que esteja em outro trem será
                            transferido para cá; os sensores desmarcados ficarão
                            sem vínculo.
                        </p>

                        <?php if ($sensoresDisponiveis): ?>

                            <?php foreach ($sensoresDisponiveis as $sensor): ?>

                                <?php $sensorId = (int) $sensor["id"]; ?>

                                <label class="opcao-sensor-vinculado">

                                    <input
                                        type="checkbox"
                                        name="sensores[]"
                                        value="<?= $sensorId ?>"
                                        <?= in_array($sensorId, $sensoresEdicao, true) ? "checked" : "" ?>
                                    >

                                    <span>
                                        <?= escapar($sensor["nome"]) ?>
                                        (<?= escapar($sensor["identificacao"]) ?>)
                                        —
                                        <?= $sensor["trem_identificacao"] === null
                                            ? "Sem vínculo"
                                            : "Vinculado a " . escapar($sensor["trem_identificacao"]) ?>
                                    </span>

                                </label>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <p>Nenhum sensor cadastrado.</p>

                        <?php endif; ?>

                    </fieldset>

                    <div class="modal-trens-acoes">

                        <button
                            type="button"
                            class="btn-cancelar-modal fechar-modal"
                        >
                            Cancelar
                        </button>

                        <button type="submit" class="btn-salvar-modal">
                            Salvar alterações
                        </button>

                    </div>

                </form>

            </div>

        </dialog>

    <?php endif; ?>

    <script>
        const modalCadastro = document.getElementById("modal-cadastro-trem");
        const modalEdicao = document.getElementById("modal-editar-trem");

        // Abrir modal de cadastro
        const botaoCadastro = document.getElementById("abrir-cadastro");

        if (botaoCadastro) {
            botaoCadastro.addEventListener("click", function () {
                modalCadastro.showModal();
            });
        }

        // Fechar os modais pelo botão Cancelar
        document.querySelectorAll(".fechar-modal").forEach(function (botao) {
            botao.addEventListener("click", function () {
                botao.closest("dialog").close();
            });
        });

        // Abrir modal de edição com os dados do trem selecionado
        document.querySelectorAll(".btn-abrir-edicao").forEach(function (botao) {

            botao.addEventListener("click", function () {

                document.getElementById("edit-id").value = botao.dataset.id;
                document.getElementById("edit-identificacao").value = botao.dataset.identificacao;
                document.getElementById("edit-modelo").value = botao.dataset.modelo;
                document.getElementById("edit-capacidade").value = botao.dataset.capacidade;
                document.getElementById("edit-status").value = botao.dataset.status;
                document.getElementById("edit-usuario").value = botao.dataset.usuario;

                const sensoresSelecionados = JSON.parse(botao.dataset.sensores || "[]");

                document.querySelectorAll(
                    '#form-editar-trem input[name="sensores[]"]'
                ).forEach(function (checkbox) {

                    checkbox.checked = sensoresSelecionados.includes(
                        Number(checkbox.value)
                    );

                });

                modalEdicao.showModal();

            });

        });

        // Reabrir o modal se o formulário apresentar erro
        <?php if ($erroCadastro !== ""): ?>
            modalCadastro.showModal();
        <?php endif; ?>

        <?php if ($erroEdicao !== ""): ?>
            modalEdicao.showModal();
        <?php endif; ?>
    </script>

</body>
</html>
