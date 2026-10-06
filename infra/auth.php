<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        "httponly" => true,
        "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
        "samesite" => "Lax",
        "path" => "/",
    ]);
    ini_set("session.use_strict_mode", "1");
    session_start();
}

function usuario_autenticado(): bool
{
    return isset($_SESSION["usuario"]["id"], $_SESSION["usuario"]["tipo_usuario"]);
}

function exigir_login(): void
{
    if (!usuario_autenticado()) {
        header("Location: login.php");
        exit;
    }
}

function exigir_administrador(mysqli $conexao): void
{
    exigir_login();

    $stmt = $conexao->prepare("SELECT tipo_usuario FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["usuario"]["id"]);
    $stmt->execute();
    $usuarioAtual = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$usuarioAtual || $usuarioAtual["tipo_usuario"] !== "Administrador") {
        unset($_SESSION["usuario"]);
        http_response_code(403);
        exit("Acesso permitido somente para administradores.");
    }

    $_SESSION["usuario"]["tipo_usuario"] = $usuarioAtual["tipo_usuario"];
}

function token_csrf(): string
{
    if (empty($_SESSION["token_csrf"])) {
        $_SESSION["token_csrf"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["token_csrf"];
}

function validar_token_csrf(): void
{
    $enviado = $_POST["token_csrf"] ?? "";
    $esperado = $_SESSION["token_csrf"] ?? "";

    if (!is_string($enviado) || !is_string($esperado) || $esperado === "" || !hash_equals($esperado, $enviado)) {
        http_response_code(403);
        exit("A solicitação expirou. Atualize a página e tente novamente.");
    }
}

function escapar(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

function valor_post(string $campo): string
{
    $valor = $_POST[$campo] ?? "";
    return is_string($valor) ? $valor : "";
}

function definir_flash(string $tipo, string $mensagem): void
{
    $_SESSION["flash"] = ["tipo" => $tipo, "mensagem" => $mensagem];
}

function obter_flash(): ?array
{
    if (!isset($_SESSION["flash"])) {
        return null;
    }

    $flash = $_SESSION["flash"];
    unset($_SESSION["flash"]);
    return $flash;
}
