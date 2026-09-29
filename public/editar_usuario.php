<?php
require_once "../infra/auth.php";
exigir_login();
require_once "../infra/conexao.php";
exigir_administrador($conexao);

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit("Identificador inválido.");
}

$stmt = $conexao->prepare("SELECT id, nome, email, telefone, tipo_usuario FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$usuario) {
    http_response_code(404);
    exit("Usuário não encontrado.");
}

$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validar_token_csrf();
    $nome = trim(valor_post("nome"));
    $email = strtolower(trim(valor_post("email")));
    $telefone = preg_replace("/[^0-9]/", "", valor_post("telefone"));
    $tipo = valor_post("tipo_usuario");
    $senha = valor_post("senha");
    $nomeValido = preg_match("/^[\p{L}\p{M} .'-]{2,100}$/u", $nome) === 1;

    if ((int) $id === (int) $_SESSION["usuario"]["id"]) {
        $tipo = $usuario["tipo_usuario"];
    }

    if (!$nomeValido || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100
        || strlen($telefone) < 8 || strlen($telefone) > 15
        || !in_array($tipo, ["Administrador", "Funcionario"], true)
        || ($senha !== "" && (strlen($senha) < 10 || strlen($senha) > 72))) {
        $erro = "Confira o nome, o e-mail, o telefone e a senha informados.";
    } else {
        try {
            if ($usuario["tipo_usuario"] === "Administrador" && $tipo !== "Administrador") {
                $administradores = $conexao->query("SELECT COUNT(*) AS total FROM usuarios WHERE tipo_usuario = 'Administrador'");
                if ((int) $administradores->fetch_assoc()["total"] <= 1) {
                    throw new DomainException("O sistema precisa manter pelo menos um administrador.");
                }
            }

            if ($senha !== "") {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $atualizar = $conexao->prepare("UPDATE usuarios SET nome = ?, email = ?, telefone = ?, tipo_usuario = ?, senha_hash = ? WHERE id = ?");
                $atualizar->bind_param("sssssi", $nome, $email, $telefone, $tipo, $hash, $id);
            } else {
                $atualizar = $conexao->prepare("UPDATE usuarios SET nome = ?, email = ?, telefone = ?, tipo_usuario = ? WHERE id = ?");
                $atualizar->bind_param("ssssi", $nome, $email, $telefone, $tipo, $id);
            }
            $atualizar->execute();
            $atualizar->close();
            if ((int) $id === (int) $_SESSION["usuario"]["id"]) {
                $_SESSION["usuario"]["nome"] = $nome;
            }
            definir_flash("sucesso", "Usuário atualizado com sucesso.");
            header("Location: crud_usuarios.php");
            exit;
        } catch (DomainException $excecao) {
            $erro = $excecao->getMessage();
        } catch (mysqli_sql_exception $excecao) {
            error_log("Falha ao atualizar usuário: " . $excecao->getMessage());
            $erro = $excecao->getCode() === 1062 ? "Este e-mail já está cadastrado." : "Não foi possível atualizar o usuário.";
        }
    }
    $usuario["nome"] = $nome;
    $usuario["email"] = $email;
    $usuario["telefone"] = $telefone;
    $usuario["tipo_usuario"] = $tipo;
}

$csrf = token_csrf();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar usuário | Ferrorama</title>
    <link rel="stylesheet" href="../assets/style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="pagina-cadastro">
    <header class="cabecalho"><h2>Editar usuário</h2><a class="item" href="crud_usuarios.php">Voltar à lista</a></header>
    <div class="layout">
        <aside class="menu-lateral"><a href="home.php" class="item">Dashboard</a><a href="crud_usuarios.php" class="item ativo">Usuários</a></aside>
        <main class="conteudo cadastro-conteudo">
            <section class="cadastro">
                <h1 id="titulo">Editar cadastro</h1>
                <?php if ($erro !== ""): ?><p role="alert"><?= escapar($erro) ?></p><?php endif; ?>
                <form method="post" autocomplete="off">
                    <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">
                    <div class="linha-cadastro">
                        <div class="campo-cadastro"><label for="nome">Nome</label><input type="text" id="nome" name="nome" maxlength="100" value="<?= escapar($usuario["nome"]) ?>" required></div>
                        <div class="campo-cadastro"><label for="email">E-mail</label><input type="email" id="email" name="email" maxlength="100" value="<?= escapar($usuario["email"]) ?>" required></div>
                    </div>
                    <div class="linha-cadastro">
                        <div class="campo-cadastro"><label for="telefone">Telefone</label><input type="tel" id="telefone" name="telefone" maxlength="15" value="<?= escapar($usuario["telefone"] ?? "") ?>" required></div>
                        <div class="campo-cadastro"><label for="tipo_usuario">Perfil</label>
                            <?php if ((int) $id === (int) $_SESSION["usuario"]["id"]): ?>
                                <input type="hidden" name="tipo_usuario" value="<?= escapar($usuario["tipo_usuario"]) ?>"><input type="text" id="tipo_usuario" value="<?= escapar($usuario["tipo_usuario"]) ?>" disabled>
                            <?php else: ?>
                                <select id="tipo_usuario" name="tipo_usuario" required><option value="Funcionario" <?= $usuario["tipo_usuario"] === "Funcionario" ? "selected" : "" ?>>Funcionário</option><option value="Administrador" <?= $usuario["tipo_usuario"] === "Administrador" ? "selected" : "" ?>>Administrador</option></select>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="campo-cadastro"><label for="senha">Nova senha (deixe em branco para manter)</label><input type="password" id="senha" name="senha" minlength="10" maxlength="72" autocomplete="new-password"></div>
                    <div class="botoes-cadastro"><button type="submit" class="btn-cadastrar">Salvar alterações</button><a href="crud_usuarios.php" class="btn-voltar">Cancelar</a></div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
