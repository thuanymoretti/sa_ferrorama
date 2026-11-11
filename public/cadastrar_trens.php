
<?php
require_once "../infra/auth.php";
exigir_login();

require_once "../infra/conexao.php";
exigir_administrador($conexao);

$erro = "";

// Buscar usuários cadastrados
$usuarios = [];

$resultadoUsuarios = $conexao->query(
    "SELECT id, nome, email FROM usuarios ORDER BY nome"
);

while ($usuario = $resultadoUsuarios->fetch_assoc()) {
    $usuario["id"] = (int) $usuario["id"];
    $usuarios[] = $usuario;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validar_token_csrf();

    $identificacao = trim(valor_post("identificacao"));
    $modelo = trim(valor_post("modelo"));
    $capacidade = filter_var(valor_post("capacidade"), FILTER_VALIDATE_INT);
    $usuario_id = filter_var(valor_post("usuario_id"), FILTER_VALIDATE_INT);
    $status = valor_post("status");

    if (
        $identificacao === "" || strlen($identificacao) > 100 ||
        $modelo === "" || strlen($modelo) > 50 ||
        $capacidade === false || $capacidade < 1 ||
        $usuario_id === false || $usuario_id < 1 ||
        !in_array($usuario_id, array_column($usuarios, "id"), true) ||
        !in_array($status, ["Ativo", "Inativo", "Em Manutenção"], true)
    ) {
        $erro = "Preencha todos os campos corretamente.";
    } else {
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
                $usuario_id
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
}

$csrf = token_csrf();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar trem | Ferrorama</title>

    <link rel="stylesheet" href="../assets/style/style.css?v=20261111-menu-uniforme">
</head>


<body class="pagina-editar-trens">

    <header class="cabecalho">
        <h2>Cadastrar trem</h2>
        <a href="crud_trens.php" class="item">Voltar aos trens</a>
    </header>

    <main class="conteudo-editar-trens">

        <section class="container-sensor">

            <h1 class="titulo-sensor">Novo trem</h1>

            <?php if ($erro !== ""): ?>
                <p class="mensagem-erro" role="alert">
                    <?= escapar($erro) ?>
                </p>
            <?php endif; ?>

            <form method="post" class="form-sensor">

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
                        value="<?= escapar(valor_post("identificacao")) ?>"
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
                        value="<?= escapar(valor_post("modelo")) ?>"
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
                        value="<?= escapar(valor_post("capacidade")) ?>"
                        required
                    >
                </div>

                <div class="grupo-input">
                    <label for="usuario_id">Usuário responsável</label>

                    <select id="usuario_id" name="usuario_id" required>

                        <option value="">Selecione um usuário</option>

                        <?php foreach ($usuarios as $usuario): ?>

                            <option
                                value="<?= escapar($usuario["id"]) ?>"
                                <?= (string) valor_post("usuario_id") === (string) $usuario["id"] ? "selected" : "" ?>
                            >
                                <?= escapar($usuario["nome"]) ?>
                                (<?= escapar($usuario["email"]) ?>)
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="grupo-input">
                    <label for="status">Status</label>

                    <select id="status" name="status" required>

                        <option value="">Selecione</option>

                        <?php foreach (["Ativo", "Inativo", "Em Manutenção"] as $opcao): ?>

                            <option
                                value="<?= escapar($opcao) ?>"
                                <?= valor_post("status") === $opcao ? "selected" : "" ?>
                            >
                                <?= escapar($opcao) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="botoes-formulario">

                    <button type="submit" class="btn-salvar">
                        Cadastrar
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
