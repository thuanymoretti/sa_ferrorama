
<?php
require_once "../infra/auth.php";
require_once "../infra/conexao.php";

exigir_login();

$erro = "";
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: crud_trens.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validar_token_csrf();

    $identificacao = trim(valor_post("identificacao"));
    $modelo = trim(valor_post("modelo"));
    $capacidadeTexto = valor_post("capacidade");
    $status = valor_post("status");

    $capacidade = filter_var(
        $capacidadeTexto,
        FILTER_VALIDATE_INT
    );

    $statusPermitidos = [
        "Ativo",
        "Inativo",
        "Em Manutenção"
    ];

    if (
        $identificacao === "" ||
        $modelo === "" ||
        $capacidade === false ||
        $capacidade < 1 ||
        !in_array($status, $statusPermitidos, true)
    ) {
        $erro = "Preencha todos os campos corretamente.";
    } else {
        $sql = "UPDATE trens
                SET identificacao = ?,
                    modelo = ?,
                    capacidade = ?,
                    status = ?
                WHERE id = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param(
            "ssisi",
            $identificacao,
            $modelo,
            $capacidade,
            $status,
            $id
        );

        if ($stmt->execute()) {
            $stmt->close();

            definir_flash(
                "sucesso",
                "Trem atualizado com sucesso!"
            );

            header("Location: crud_trens.php");
            exit;
        }

        $stmt->close();
        $erro = "Não foi possível atualizar o trem.";
    }
}

$sql = "SELECT identificacao, modelo, capacidade, status
        FROM trens
        WHERE id = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$trem = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$trem) {
    header("Location: crud_trens.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $trem["identificacao"] = $identificacao;
    $trem["modelo"] = $modelo;
    $trem["capacidade"] = $capacidadeTexto;
    $trem["status"] = $status;
}

$csrf = token_csrf();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar trem | Ferrorama</title>
    <link rel="stylesheet" href="../assets/style/style.css">
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
                        value="<?= escapar((string)$trem["identificacao"]) ?>"
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
                        value="<?= escapar((string)$trem["modelo"]) ?>"
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
                        value="<?= escapar((string)$trem["capacidade"]) ?>"
                        required
                    >
                </div>

                <div class="grupo-input">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <?php
                        $statusPermitidos = [
                            "Ativo",
                            "Inativo",
                            "Em Manutenção"
                        ];
                        ?>

                        <?php foreach ($statusPermitidos as $opcao): ?>
                            <option
                                value="<?= escapar($opcao) ?>"
                                <?= $trem["status"] === $opcao ? "selected" : "" ?>
                            >
                                <?= escapar($opcao) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

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
