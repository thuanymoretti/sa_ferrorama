<?php
require_once "../infra/auth.php";
exigir_login();
require_once "../infra/conexao.php";
exigir_administrador($conexao);

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header("Location: crud_viagens.php");
    exit;
}

$stmt = $conexao->prepare(
    "SELECT id, trem_id, data_inicio, data_chegada, origem, destino, status
     FROM viagens WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$viagem = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$viagem) {
    header("Location: crud_viagens.php");
    exit;
}

$erro = "";
$statusPermitidos = ["Em Andamento", "Concluída", "Cancelada"];

function converterDataHoraLocal(string $valor): string|false
{
    $data = DateTime::createFromFormat("!Y-m-d\\TH:i", $valor);
    $problemas = DateTime::getLastErrors();
    if (
        !$data ||
        ($problemas && ($problemas["warning_count"] > 0 || $problemas["error_count"] > 0)) ||
        $data->format("Y-m-d\\TH:i") !== $valor
    ) {
        return false;
    }

    return $data->format("Y-m-d H:i:s");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validar_token_csrf();

    $tremId = filter_var(valor_post("trem_id"), FILTER_VALIDATE_INT);
    $inicioLocal = valor_post("data_inicio");
    $chegadaLocal = valor_post("data_chegada");
    $origem = trim(valor_post("origem"));
    $destino = trim(valor_post("destino"));
    $status = valor_post("status");
    $inicio = converterDataHoraLocal($inicioLocal);
    $chegada = converterDataHoraLocal($chegadaLocal);

    $viagem["trem_id"] = $tremId ?: "";
    $viagem["data_inicio"] = $inicioLocal;
    $viagem["data_chegada"] = $chegadaLocal;
    $viagem["origem"] = $origem;
    $viagem["destino"] = $destino;
    $viagem["status"] = $status;

    if (
        !$tremId || $tremId < 1 ||
        $inicio === false || $chegada === false ||
        $origem === "" || strlen($origem) > 100 ||
        $destino === "" || strlen($destino) > 100 ||
        !in_array($status, $statusPermitidos, true)
    ) {
        $erro = "Preencha todos os campos corretamente.";
    } else {
        $stmt = $conexao->prepare("SELECT id FROM trens WHERE id = ?");
        $stmt->bind_param("i", $tremId);
        $stmt->execute();
        $tremExiste = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if (!$tremExiste) {
            $erro = "Selecione um trem válido.";
        } else {
            try {
                $stmt = $conexao->prepare(
                    "UPDATE viagens
                     SET trem_id = ?, data_inicio = ?, data_chegada = ?,
                         origem = ?, destino = ?, status = ?
                     WHERE id = ?"
                );
                $stmt->bind_param(
                    "isssssi",
                    $tremId,
                    $inicio,
                    $chegada,
                    $origem,
                    $destino,
                    $status,
                    $id
                );
                $stmt->execute();
                $stmt->close();

                definir_flash("sucesso", "Viagem atualizada com sucesso.");
                header("Location: crud_viagens.php");
                exit;
            } catch (mysqli_sql_exception $falha) {
                error_log("Falha ao editar viagem {$id}: " . $falha->getMessage());
                $erro = "Não foi possível atualizar a viagem.";
            }
        }
    }
}

$trens = $conexao->query(
    "SELECT id, identificacao, modelo FROM trens ORDER BY identificacao"
)->fetch_all(MYSQLI_ASSOC);
$csrf = token_csrf();

function valorDataHoraInput(?string $valor): string
{
    if (!$valor) {
        return "";
    }
    $data = DateTime::createFromFormat("Y-m-d H:i:s", $valor);
    return $data ? $data->format("Y-m-d\\TH:i") : "";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar viagem | Ferrorama</title>
    <link rel="stylesheet" href="../assets/style/style.css">
</head>
<body class="pagina-editar-trens">
    <header class="cabecalho">
        <h2>Editar viagem</h2>
        <a href="crud_viagens.php" class="item">Voltar às viagens</a>
    </header>

    <main class="conteudo-editar-trens">
        <section class="container-sensor">
            <h1 class="titulo-sensor">Editar viagem #<?= (int) $viagem["id"] ?></h1>

            <?php if ($erro !== ""): ?>
                <p class="mensagem-erro" role="alert"><?= escapar($erro) ?></p>
            <?php endif; ?>

            <form method="post" class="form-sensor">
                <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">

                <div class="grupo-input">
                    <label for="trem_id">Trem</label>
                    <select id="trem_id" name="trem_id" required>
                        <?php foreach ($trens as $trem): ?>
                            <option value="<?= (int) $trem["id"] ?>" <?= (int) $viagem["trem_id"] === (int) $trem["id"] ? "selected" : "" ?>>
                                <?= escapar($trem["identificacao"] . " — " . $trem["modelo"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grupo-input">
                    <label for="data_inicio">Data e hora de início</label>
                    <input type="datetime-local" id="data_inicio" name="data_inicio" value="<?= escapar(valorDataHoraInput($viagem["data_inicio"])) ?>" required>
                </div>

                <div class="grupo-input">
                    <label for="data_chegada">Data e hora de chegada</label>
                    <input type="datetime-local" id="data_chegada" name="data_chegada" value="<?= escapar(valorDataHoraInput($viagem["data_chegada"])) ?>" required>
                </div>

                <div class="grupo-input">
                    <label for="origem">Origem</label>
                    <input type="text" id="origem" name="origem" maxlength="100" value="<?= escapar($viagem["origem"]) ?>" required>
                </div>

                <div class="grupo-input">
                    <label for="destino">Destino</label>
                    <input type="text" id="destino" name="destino" maxlength="100" value="<?= escapar($viagem["destino"]) ?>" required>
                </div>

                <div class="grupo-input">
                    <label for="status">Status da viagem</label>
                    <select id="status" name="status" required>
                        <?php foreach ($statusPermitidos as $opcao): ?>
                            <option value="<?= escapar($opcao) ?>" <?= $viagem["status"] === $opcao ? "selected" : "" ?>><?= escapar($opcao) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="botoes-formulario">
                    <button type="submit" class="btn-salvar">Salvar alterações</button>
                    <a href="crud_viagens.php" class="btn-cancelar">Cancelar</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
