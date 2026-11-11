
<?php
require_once "../infra/auth.php";
exigir_login();

require_once "../infra/conexao.php";
exigir_administrador($conexao);

$erro = "";
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: crud_trens.php");
    exit;
}

$usuarios = $conexao->query(
    "SELECT id, nome, email FROM usuarios ORDER BY nome"
)->fetch_all(MYSQLI_ASSOC);
foreach ($usuarios as &$usuario) {
    $usuario["id"] = (int) $usuario["id"];
}
unset($usuario);

// Buscar trem
$stmt = $conexao->prepare(
    "SELECT identificacao, modelo, capacidade, status, usuario_id
     FROM trens WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();
$trem = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$trem) {
    header("Location: crud_trens.php");
    exit;
}

// Sensores já vinculados
$sensoresSelecionados = [];

$stmt = $conexao->prepare(
    "SELECT id FROM sensores WHERE trem_id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultadoSensoresAtuais = $stmt->get_result();

while ($sensor = $resultadoSensoresAtuais->fetch_assoc()) {
    $sensoresSelecionados[] = (int) $sensor["id"];
}

$stmt->close();

// Processar formulário
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    validar_token_csrf();

    $identificacao = trim(valor_post("identificacao"));
    $modelo = trim(valor_post("modelo"));
    $capacidadeTexto = valor_post("capacidade");
    $status = valor_post("status");
    $usuarioId = filter_var(valor_post("usuario_id"), FILTER_VALIDATE_INT);

    $capacidade = filter_var($capacidadeTexto, FILTER_VALIDATE_INT);

    $statusPermitidos = ["Ativo", "Inativo", "Em Manutenção"];

    $listaPostada = $_POST["sensores"] ?? [];

    if (!is_array($listaPostada)) {
        $listaPostada = [];
        $erro = "A seleção de sensores é inválida.";
    }

    $sensoresSelecionados = [];

    foreach ($listaPostada as $sensorIdTexto) {

        $sensorId = filter_var($sensorIdTexto, FILTER_VALIDATE_INT);

        if (!$sensorId || $sensorId < 1) {
            $erro = "A seleção de sensores é inválida.";
            break;
        }

        $sensoresSelecionados[] = (int) $sensorId;
    }

    $sensoresSelecionados = array_values(
        array_unique($sensoresSelecionados)
    );

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
        !in_array($status, $statusPermitidos, true)
    ) {
        $erro = "Preencha todos os campos corretamente e selecione o usuário responsável.";
    }

    if ($erro === "") {

        try {

            $conexao->begin_transaction();

            // Verificar sensores selecionados
            $verificarSensor = $conexao->prepare(
                "SELECT trem_id FROM sensores WHERE id = ? FOR UPDATE"
            );

            foreach ($sensoresSelecionados as $sensorId) {

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

            // Atualizar dados do trem
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

            // Remover vínculos antigos dos sensores
            $stmt = $conexao->prepare(
                "UPDATE sensores SET trem_id = NULL WHERE trem_id = ?"
            );

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Vincular sensores selecionados
            $vincularSensor = $conexao->prepare(
                "UPDATE sensores SET trem_id = ? WHERE id = ?"
            );

            foreach ($sensoresSelecionados as $sensorId) {

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

    $trem["identificacao"] = $identificacao;
    $trem["modelo"] = $modelo;
    $trem["capacidade"] = $capacidadeTexto;
    $trem["status"] = $status;
    $trem["usuario_id"] = $usuarioId;
}

// Buscar sensores disponíveis
$stmt = $conexao->prepare(
    "SELECT s.id, s.nome, s.identificacao, s.trem_id,
            t.identificacao AS trem_identificacao
     FROM sensores s
     LEFT JOIN trens t ON t.id = s.trem_id
     ORDER BY s.nome, s.identificacao"
);

$stmt->execute();

$sensoresDisponiveis = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();

$csrf = token_csrf();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar trem | Ferrorama</title>

    <link rel="stylesheet" href="../assets/style/style.css?v=20261111-menu-uniforme">
</head>

<body class="pagina-editar-trens">

    <header class="cabecalho">
        <h2>Ferrorama</h2>

        <div class="usuario-logado">
            Olá, <?= escapar($_SESSION["usuario"]["nome"] ?? "Usuário") ?>
        </div>
    </header>

    <main class="conteudo-editar-trens">

        <section class="container-sensor">

            <h1 class="titulo-sensor">Editar trem</h1>

            <?php if ($erro !== ""): ?>

                <p class="mensagem-erro" role="alert">
                    <?= escapar($erro) ?>
                </p>

            <?php endif; ?>

            <form method="POST" class="form-sensor">

                <input
                    type="hidden"
                    name="token_csrf"
                    value="<?= escapar($csrf) ?>"
                >

                <div class="grupo-input">

                    <label for="identificacao">Identificação</label>

                    <input
                        type="text"
                        id="identificacao"
                        name="identificacao"
                        maxlength="100"
                        value="<?= escapar((string) $trem["identificacao"]) ?>"
                        required
                    >

                </div>

                <div class="grupo-input">

                    <label for="modelo">Modelo</label>

                    <input
                        type="text"
                        id="modelo"
                        name="modelo"
                        maxlength="50"
                        value="<?= escapar((string) $trem["modelo"]) ?>"
                        required
                    >

                </div>

                <div class="grupo-input">

                    <label for="capacidade">Capacidade</label>

                    <input
                        type="number"
                        id="capacidade"
                        name="capacidade"
                        min="1"
                        value="<?= escapar((string) $trem["capacidade"]) ?>"
                        required
                    >

                </div>

                <div class="grupo-input">

                    <label for="usuario_id">Usuário responsável</label>

                    <select id="usuario_id" name="usuario_id" required>
                        <option value="">Selecione um usuário</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option
                                value="<?= (int) $usuario["id"] ?>"
                                <?= (int) $trem["usuario_id"] === (int) $usuario["id"] ? "selected" : "" ?>
                            >
                                <?= escapar($usuario["nome"]) ?> (<?= escapar($usuario["email"]) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                </div>

                <div class="grupo-input">

                    <label for="status">Status</label>

                    <select id="status" name="status" required>

                        <?php foreach (["Ativo", "Inativo", "Em Manutenção"] as $opcao): ?>

                            <option
                                value="<?= escapar($opcao) ?>"
                                <?= $trem["status"] === $opcao ? "selected" : "" ?>
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
                                    <?= in_array($sensorId, $sensoresSelecionados, true) ? "checked" : "" ?>
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

                <div class="botoes-formulario">

                    <button type="submit" class="btn-salvar">
                        Salvar alterações
                    </button>

                    <a href="crud_trens.php" class="btn-cancelar">
                        Cancelar
                    </a>

                </div>

            </form>

        </section>

    </main>

</body>
</html>
