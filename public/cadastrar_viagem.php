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
    $data_inicio = $_POST["data_inicio"];
    $data_chegada = $_POST["data_chegada"];
    $origem = trim($_POST["origem"] ?? "");
    $destino = trim($_POST["destino"] ?? "");
    $status = $_POST["status"] ?? "";

    $statusPermitidos = ["Em Andamento", "Concluída", "Cancelada"];

    if (
        $trem_id > 0 &&
        !empty($data_inicio) &&
        !empty($data_chegada) &&
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
            definir_flash("sucesso", "Viagem cadastrada com sucesso.");
            header("Location: crud_viagens.php");
            exit;
        } else {
            $erro = "Erro ao cadastrar viagem.";
        }

        $stmt->close();

    } else {
        $erro = "Preencha todos os campos corretamente.";
    }
}


// BUSCAR TRENS PARA O SELECT
$sqlTrens = "SELECT id, identificacao, modelo
             FROM trens
             ORDER BY identificacao";

$trens = $conexao->query($sqlTrens);


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

    <title>Cadastro de viagem | Ferrorama</title>

    <link rel="stylesheet" href="../assets/style/style.css?v=20261104-cadastro-viagem">

</head>

<body>

    <!-- CABEÇALHO -->
    <header class="cabecalho">

        <h2>Olá, <?= escapar($_SESSION["usuario"]["nome"]) ?></h2>

        <a class="item" href="crud_viagens.php">Voltar à lista</a>

    </header>


    <div class="layout">

        <!-- MENU LATERAL -->
        <aside class="menu-lateral">

            <a href="home.php" class="item">Dashboard</a>

            <a href="crud_sensor.php" class="item">Sensores</a>

            <a href="crud_trens.php" class="item">Trens</a>

            <?php if ($ehAdministrador): ?>

                <a href="crud_usuarios.php" class="item">Usuários</a>

            <?php endif; ?>

            <a href="crud_viagens.php" class="item ativo">Viagens</a>

        </aside>


        <!-- CONTEÚDO PRINCIPAL -->
        <main class="conteudo">

            <section class="formulario-usuario">

                <h1>Cadastrar viagem</h1>


                <!-- MENSAGENS -->
                <?php if ($mensagem !== ""): ?>

                    <p class="mensagem-sucesso" role="status">
                        <?= escapar($mensagem) ?>
                    </p>

                <?php endif; ?>


                <?php if ($erro !== ""): ?>

                    <p class="mensagem-erro" role="alert">
                        <?= escapar($erro) ?>
                    </p>

                <?php endif; ?>


                <?php if ($ehAdministrador): ?>

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

            <?php while ($trem = $trens->fetch_assoc()): ?>
                <option value="<?= (int) $trem["id"] ?>">
                    <?= e($trem["identificacao"]) ?> - <?= e($trem["modelo"]) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="campo-cadastro">
        <label for="status">Status da viagem</label>

        <select name="status" id="status" required>
            <option value="">Selecione o status</option>
            <option value="Em Andamento">Em andamento</option>
            <option value="Concluída">Concluída</option>
            <option value="Cancelada">Cancelada</option>
        </select>
    </div>

</div>


<!-- SEGUNDA LINHA -->
<div class="linha-cadastro">

    <div class="campo-cadastro">
        <label for="data_inicio">Data e hora de partida</label>

        <input
            type="datetime-local"
            name="data_inicio"
            id="data_inicio"
            required
        >
    </div>

    <div class="campo-cadastro">
        <label for="data_chegada">Data e hora de chegada</label>

        <input
            type="datetime-local"
            name="data_chegada"
            id="data_chegada"
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
            name="origem"
            id="origem"
            placeholder="Ex.: Estação Central"
            maxlength="100"
            required
        >
    </div>

    <div class="campo-cadastro">
        <label for="destino">Destino</label>

        <input
            type="text"
            name="destino"
            id="destino"
            placeholder="Ex.: Estação Norte"
            maxlength="100"
            required
        >
    </div>

</div>


                        <!-- BOTÕES -->
                        <div class="botoes-cadastro">

                            <button
                                type="submit"
                                name="cadastrar"
                                class="btn-cadastrar">

                                Cadastrar viagem

                            </button>

                            <a href="crud_viagens.php" class="btn-voltar">
                                Cancelar
                            </a>

                        </div>

                    </form>

                <?php endif; ?>

            </section>

        </main>

    </div>

</body>

</html>
```