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
$nome = trim(valor_post("nome"));
$localizacao = trim(valor_post("localizacao"));
$tipo = valor_post("tipo_sensor");
$tremId = filter_var(valor_post("trem_id"), FILTER_VALIDATE_INT);
$tipos = ["Temperatura", "Umidade", "Pressao", "Velocidade"];
if (!$id || $id < 1 || $nome === "" || strlen($nome) > 100 || $localizacao === "" || strlen($localizacao) > 100
    || !in_array($tipo, $tipos, true) || !$tremId || $tremId < 1) {
    header("Location: crud_sensor.php?erro=1");
    exit;
}

try {
    $verificarTrem = $conexao->prepare("SELECT id FROM trens WHERE id = ?");
    $verificarTrem->bind_param("i", $tremId);
    $verificarTrem->execute();
    $tremExiste = $verificarTrem->get_result()->num_rows > 0;
    $verificarTrem->close();
    if (!$tremExiste) {
        header("Location: crud_sensor.php?erro=1");
        exit;
    }

    $identificacao = $nome;
    $stmt = $conexao->prepare("UPDATE sensores SET nome = ?, identificacao = ?, localizacao = ?, tipo_sensor = ?, trem_id = ? WHERE id = ?");
    $stmt->bind_param("ssssii", $nome, $identificacao, $localizacao, $tipo, $tremId, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: crud_sensor.php?sucesso=1");
    exit;
} catch (mysqli_sql_exception $erro) {
    error_log("Falha ao atualizar sensor: " . $erro->getMessage());
    header("Location: crud_sensor.php?erro=1");
    exit;
}
