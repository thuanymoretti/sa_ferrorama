<?php
require_once "../infra/auth.php";
exigir_login();
require_once "../infra/conexao.php";
exigir_administrador($conexao);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Método não permitido.");
}
validar_token_csrf();

$id = filter_var(valor_post("id"), FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    definir_flash("erro", "Identificador de usuário inválido.");
    header("Location: crud_usuarios.php");
    exit;
}
if ((int) $id === (int) $_SESSION["usuario"]["id"]) {
    definir_flash("erro", "Você não pode excluir a própria conta enquanto estiver conectado.");
    header("Location: crud_usuarios.php");
    exit;
}

$stmt = $conexao->prepare("SELECT tipo_usuario FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$alvo = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$alvo) {
    definir_flash("erro", "Usuário não encontrado.");
    header("Location: crud_usuarios.php");
    exit;
}
if ($alvo["tipo_usuario"] === "Administrador") {
    $resultado = $conexao->query("SELECT COUNT(*) AS total FROM usuarios WHERE tipo_usuario = 'Administrador'");
    if ((int) $resultado->fetch_assoc()["total"] <= 1) {
        definir_flash("erro", "O sistema precisa manter pelo menos um administrador.");
        header("Location: crud_usuarios.php");
        exit;
    }
}

$stmt = $conexao->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
definir_flash("sucesso", "Usuário excluído com sucesso.");
header("Location: crud_usuarios.php");
exit;
