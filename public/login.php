<?php
require_once "../infra/auth.php";
require_once "../infra/conexao.php";

if (usuario_autenticado()) {
    header("Location: home.php");
    exit;
}

$mensagem = "";
$schemaOk = true;
try {
    $administradores = $conexao->query("SELECT COUNT(*) AS total FROM usuarios WHERE tipo_usuario = 'Administrador' AND senha_hash IS NOT NULL AND senha_hash <> ''");
    $avisoInicial = (int) $administradores->fetch_assoc()["total"] === 0;
} catch (mysqli_sql_exception $erroBanco) {
    error_log("Falha ao verificar autenticação: " . $erroBanco->getMessage());
    http_response_code(503);
    $schemaOk = false;
    $avisoInicial = false;
    $mensagem = "Atualize o banco com database/migracao_autenticacao.sql antes de entrar.";
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && $schemaOk) {
    validar_token_csrf();
    $email = strtolower(trim(valor_post("email")));
    $senha = valor_post("senha");

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conexao->prepare("SELECT id, nome, email, senha_hash, tipo_usuario FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($usuario && !empty($usuario["senha_hash"]) && password_verify($senha, $usuario["senha_hash"])) {
            if (password_needs_rehash($usuario["senha_hash"], PASSWORD_DEFAULT)) {
                $novoHash = password_hash($senha, PASSWORD_DEFAULT);
                $atualizar = $conexao->prepare("UPDATE usuarios SET senha_hash = ? WHERE id = ?");
                $atualizar->bind_param("si", $novoHash, $usuario["id"]);
                $atualizar->execute();
                $atualizar->close();
            }

            session_regenerate_id(true);
            unset($_SESSION["token_csrf"]);
            $_SESSION["usuario"] = [
                "id" => (int) $usuario["id"],
                "nome" => $usuario["nome"],
                "tipo_usuario" => $usuario["tipo_usuario"],
            ];
            header("Location: home.php");
            exit;
        }
    }

    $mensagem = "E-mail ou senha inválidos.";
}

$csrf = token_csrf();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar | Ferrorama</title>
    <link rel="stylesheet" href="../assets/style/style.css">
</head>
<body class="pagina-login">
    <main>
        <form method="post" class="caixa" autocomplete="on">
            <h1>Entrar no Ferrorama</h1>
            <?php if ($avisoInicial): ?><p>Antes do primeiro acesso, crie o administrador pelo terminal local. Consulte o README.</p><?php endif; ?>
            <?php if ($mensagem !== ""): ?><p role="alert"><?= escapar($mensagem) ?></p><?php endif; ?>
            <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" maxlength="100" autocomplete="username" required>
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" autocomplete="current-password" required>
            <button type="submit">Entrar</button>
        </form>
    </main>
</body>
</html>
