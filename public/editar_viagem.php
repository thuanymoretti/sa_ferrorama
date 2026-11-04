```php
<?php

require_once "../infra/auth.php";
exigir_login();

require_once "../infra/conexao.php";
exigir_administrador($conexao);


// VERIFICAR O ID DA VIAGEM
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: crud_viagens.php");
    exit;
}


// BUSCAR VIAGEM
$stmt = $conexao->prepare(
    "SELECT viagens.id, viagens.trem_id, viagens.data_inicio,
            viagens.data_chegada, viagens.origem, viagens.destino,
            viagens.status, trens.identificacao AS nome_trem
     FROM viagens
     INNER JOIN trens ON viagens.trem_id = trens.id
     WHERE viagens.id = ?"
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


// CONVERTER DATA E HORA
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


// FORMATAR DATA PARA O INPUT
function valorDataHoraInput(?string $valor): string
{
    if (!$valor) {
        return "";
    }

    $data = DateTime::createFromFormat("Y-m-d H:i:s", $valor);

    if (!$data) {
        $data = DateTime::createFromFormat("Y-m-d\\TH:i", $valor);
    }

    return $data ? $data->format("Y-m-d\\TH:i") : "";
}


// ATUALIZAR VIAGEM
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


    // MANTER OS VALORES PREENCHIDOS
    $viagem["trem_id"] = $tremId ?: "";
    $viagem["data_inicio"] = $inicioLocal;
    $viagem["data_chegada"] = $chegadaLocal;
    $viagem["origem"] = $origem;
    $viagem["destino"] = $destino;
    $viagem["status"] = $status;


    // VALIDAR CAMPOS
    if (
        !$tremId || $tremId < 1 ||
        $inicio === false || $chegada === false ||
        $origem === "" || strlen($origem) > 100 ||
        $destino === "" || strlen($destino) > 100 ||
        !in_array($status, $statusPermitidos, true)
    ) {

        $erro = "Preencha todos os campos corretamente.";

    } else {

        // VERIFICAR SE O TREM EXISTE
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


// BUSCAR TRENS
$trens = $conexao->query(
    "SELECT id, identificacao, modelo
     FROM trens
     ORDER BY identificacao"
)->fetch_all(MYSQLI_ASSOC);


// TOKEN CSRF
$csrf = token_csrf();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar viagem | Ferrorama</title>

    <link rel="stylesheet" href="../assets/style/style.css?v=20261104-editar-viagem">

</head>

<body>

    <!-- CABEÇALHO -->
    <header class="cabecalho">

        <h2>Olá, <?= escapar($_SESSION["usuario"]["nome"]) ?></h2>

        <a href="crud_viagens.php" class="item">Voltar às viagens</a>

    </header>


    <div class="layout">

        <!-- MENU LATERAL -->
        <aside class="menu-lateral">

            <a href="home.php" class="item">Dashboard</a>

            <a href="crud_sensor.php" class="item">Sensores</a>

            <a href="crud_trens.php" class="item">Trens</a>

            <?php if ($_SESSION["usuario"]["tipo_usuario"] === "Administrador"): ?>

                <a href="crud_usuarios.php" class="item">Usuários</a>

            <?php endif; ?>

            <a href="crud_viagens.php" class="item ativo">Viagens</a>

        </aside>


        <!-- CONTEÚDO PRINCIPAL -->
        <main class="conteudo">

            <section class="formulario-usuario">

                <h1 class="titulo-sensor"> Editar viagem - <?= escapar($viagem["nome_trem"]) ?> </h1>


                <!-- MENSAGEM DE ERRO -->
                <?php if ($erro !== ""): ?>

                    <p class="mensagem-erro" role="alert">
                        <?= escapar($erro) ?>
                    </p>

                <?php endif; ?>


                <form method="post" autocomplete="off">

                    <input
                        type="hidden"
                        name="token_csrf"
                        value="<?= escapar($csrf) ?>"
                    >


                    <!-- PRIMEIRA LINHA -->
                    <div class="linha-cadastro">

                        <div class="campo-cadastro">

                            <label for="trem_id">Trem</label>

                            <select name="trem_id" id="trem_id" required>

                                <option value="">Selecione um trem</option>

                                <?php foreach ($trens as $trem): ?>

                                    <option
                                        value="<?= (int) $trem["id"] ?>"
                                        <?= (int) $viagem["trem_id"] === (int) $trem["id"] ? "selected" : "" ?>
                                    >

                                        <?= escapar($trem["identificacao"] . " — " . $trem["modelo"]) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="campo-cadastro">

                            <label for="status">Status da viagem</label>

                            <select name="status" id="status" required>

                                <option value="">Selecione o status</option>

                                <?php foreach ($statusPermitidos as $opcao): ?>

                                    <option
                                        value="<?= escapar($opcao) ?>"
                                        <?= $viagem["status"] === $opcao ? "selected" : "" ?>
                                    >

                                        <?= escapar($opcao) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>


                    <!-- SEGUNDA LINHA -->
                    <div class="linha-cadastro">

                        <div class="campo-cadastro">

                            <label for="data_inicio">Data e hora de partida</label>

                            <input
                                type="datetime-local"
                                id="data_inicio"
                                name="data_inicio"
                                value="<?= escapar(valorDataHoraInput($viagem["data_inicio"])) ?>"
                                required
                            >

                        </div>


                        <div class="campo-cadastro">

                            <label for="data_chegada">Data e hora de chegada</label>

                            <input
                                type="datetime-local"
                                id="data_chegada"
                                name="data_chegada"
                                value="<?= escapar(valorDataHoraInput($viagem["data_chegada"])) ?>"
                                required
                            >

                        </div>

                    </div>


                    <!-- TERCEIRA LINHA -->
                    <div class="linha-cadastro">

                        <div class="campo-cadastro">

                            <label for="origem">Origem</label>

                            <input
                                type="text"
                                id="origem"
                                name="origem"
                                maxlength="100"
                                value="<?= escapar($viagem["origem"]) ?>"
                                required
                            >

                        </div>


                        <div class="campo-cadastro">

                            <label for="destino">Destino</label>

                            <input
                                type="text"
                                id="destino"
                                name="destino"
                                maxlength="100"
                                value="<?= escapar($viagem["destino"]) ?>"
                                required
                            >

                        </div>

                    </div>


                    <!-- BOTÕES -->
                    <div class="botoes-cadastro">

                        <button type="submit" class="btn-cadastrar">
                            Salvar alterações
                        </button>

                        <a href="crud_viagens.php" class="btn-voltar">
                            Cancelar
                        </a>

                    </div>

                </form>

            </section>

        </main>

    </div>

</body>

</html>
```