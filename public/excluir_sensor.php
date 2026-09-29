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
    header("Location: crud_sensor.php?erro=1");
    exit;
}

$stmt = $conexao->prepare("DELETE FROM sensores WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
header("Location: crud_sensor.php?sucesso=1");
exit;
