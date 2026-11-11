<?php
require_once "../infra/auth.php";
exigir_login();
require_once "../infra/conexao.php";
exigir_administrador($conexao);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
} else {
    $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
}
if (!$id || $id < 1) {
    header("Location: crud_usuarios.php");
    exit;
}

$stmt = $conexao->prepare(
    "SELECT nome, email, telefone, tipo_usuario FROM usuarios WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$usuarioEditado = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuarioEditado) {
    header("Location: crud_usuarios.php");
    exit;
}

$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validar_token_csrf();
    $nome = trim(valor_post("nome"));
    $email = strtolower(trim(valor_post("email")));
    $telefone = preg_replace("/[^0-9]/", "", valor_post("telefone"));
    $tipo = valor_post("tipo_usuario");
    $nomeValido = preg_match("/^[\\p{L}\\p{M} .'-]{2,100}$/u", $nome) === 1;

    $usuarioEditado["nome"] = $nome;
    $usuarioEditado["email"] = $email;
    $usuarioEditado["telefone"] = $telefone;
    $usuarioEditado["tipo_usuario"] = $tipo;

    if (
        !$nomeValido || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100 ||
        strlen($telefone) < 8 || strlen($telefone) > 15 ||
        !in_array($tipo, ["Administrador", "Funcionario"], true)
    ) {
        $erro = "Confira o nome, o e-mail, o telefone e o perfil.";
    } else {
        try {
            $stmt = $conexao->prepare(
                "UPDATE usuarios SET nome = ?, email = ?, telefone = ?, tipo_usuario = ? WHERE id = ?"
            );
            $stmt->bind_param("ssssi", $nome, $email, $telefone, $tipo, $id);
            $stmt->execute();
            $stmt->close();

            definir_flash("sucesso", "Usuário atualizado com sucesso.");
            header("Location: crud_usuarios.php");
            exit;
        } catch (mysqli_sql_exception $falha) {
            error_log("Falha ao editar usuário {$id}: " . $falha->getMessage());
            $erro = $falha->getCode() === 1062
                ? "Este e-mail já está cadastrado."
                : "Não foi possível atualizar o usuário.";
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
    <title>Editar usuário | Ferrorama</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=20261111-menu-uniforme">
</head>
<body>
    <header class="cabecalho">
        <h2>Olá, <?= escapar($_SESSION["usuario"]["nome"]) ?></h2>
        <a class="item" href="crud_usuarios.php">Voltar à lista</a>
    </header>

    <div class="layout">
        <aside class="menu-lateral">
            <a href="home.php" class="item">Dashboard</a>
            <a href="crud_sensor.php" class="item">Sensores</a>
            <a href="crud_trens.php" class="item">Trens</a>
            <a href="crud_usuarios.php" class="item ativo">Usuários</a>
            <a href="crud_viagens.php" class="item">Viagens</a>
        </aside>

        <main class="conteudo">
            <section class="formulario-usuario">
                <h1>Editar usuário</h1>
                <?php if ($erro !== ""): ?><p class="mensagem-erro" role="alert"><?= escapar($erro) ?></p><?php endif; ?>

                <form method="post">
                    <input type="hidden" name="id" value="<?= (int) $id ?>">
                    <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">

                        <div class="campo-cadastro">
                            <label for="nome">Nome completo</label>
                            <input type="text" id="nome" name="nome" maxlength="100" value="<?= escapar($usuarioEditado["nome"]) ?>" required>
                        </div>
                        <div class="campo-cadastro">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" maxlength="100" value="<?= escapar($usuarioEditado["email"]) ?>" required>
                        </div>
                    </div>

                    <div class="linha-cadastro">
                        <div class="campo-cadastro">
                            <label for="telefone">Telefone (somente números)</label>
                            <input type="tel" id="telefone" name="telefone" inputmode="numeric" maxlength="15" value="<?= escapar((string) ($usuarioEditado["telefone"] ?? "")) ?>" required>
                        </div>
                        <div class="campo-cadastro">
                            <label for="tipo_usuario">Perfil</label>
                            <select id="tipo_usuario" name="tipo_usuario" required>
                                <option value="Funcionario" <?= $usuarioEditado["tipo_usuario"] === "Funcionario" ? "selected" : "" ?>>Funcionário</option>
                                <option value="Administrador" <?= $usuarioEditado["tipo_usuario"] === "Administrador" ? "selected" : "" ?>>Administrador</option>
                            </select>
                        </div>
                    </div>

                    <div class="botoes-cadastro">
                        <button type="submit" class="btn-cadastrar">Salvar alterações</button>
                        <a href="crud_usuarios.php" class="btn-voltar">Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
