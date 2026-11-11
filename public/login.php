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

            $_SESSION["mensagem_login"] = "Login realizado com sucesso!";

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
    <link rel="stylesheet" href="../assets/style/style.css?v=20261111-menu-uniforme">
</head>
<body class="pagina-login">
    <main class="login-layout">
        <section class="login-visual" aria-label="Ferrorama">
            <img src="../assets/Situação%20de%20aprendizagem.png" alt="Locomotiva atravessando a paisagem ao pôr do sol">
            <div class="login-visual-conteudo">
                <span class="login-marca">FERRORAMA</span>
                <p class="login-frase">Movendo cidades,<br>conectando vidas.</p>
                <span class="login-linha"></span>
                <p class="login-contexto">Acompanhe a operação ferroviária em um só lugar.</p>
            </div>
        </section>

        <section class="login-painel">
            <form method="post" class="caixa login-formulario" autocomplete="on">
                <p class="login-sobrelinha">ÁREA DE ACESSO</p>
                <h1>Bem-vindo de volta</h1>
                <p class="login-descricao">Entre com suas credenciais para acessar o painel.</p>
                <?php if ($avisoInicial): ?><p class="login-aviso">Antes do primeiro acesso, crie o administrador pelo terminal local. Consulte o README.</p><?php endif; ?>
                <?php if ($mensagem !== ""): ?><p class="login-erro" role="alert"><?= escapar($mensagem) ?></p><?php endif; ?>
                <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">

                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" maxlength="100" autocomplete="username" placeholder="voce@exemplo.com" required>

                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" autocomplete="current-password" placeholder="Digite sua senha" required>

                <button type="submit">Entrar no painel</button>
                <p class="login-rodape">Sistema de gestão ferroviária</p>

                <a href="sobrenos.php" class="link-sobre">
    Conheça nosso projeto
    <span>→</span>
</a>

            </form>
        </section>
    </main>
</body>
</html>
