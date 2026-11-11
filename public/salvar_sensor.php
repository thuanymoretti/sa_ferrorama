
<?php

require_once "../infra/auth.php";
exigir_login();
include "../infra/conexao.php";
exigir_administrador($conexao);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Método não permitido.");
}

validar_token_csrf();

$nome = trim(valor_post("nome_sensor"));
$identificacao = trim(valor_post("identificacao"));
$localizacao = trim(valor_post("localizacao"));
$tipo = valor_post("tipoDado");
$tremId = filter_var(valor_post("trem_id"), FILTER_VALIDATE_INT);

$tipos = ["Temperatura", "fotoeletrico", "Pressao", "Velocidade"];

if (
    $nome === "" || strlen($nome) > 100 ||
    $identificacao === "" || strlen($identificacao) > 50 ||
    $localizacao === "" || strlen($localizacao) > 100 ||
    !in_array($tipo, $tipos, true) || !$tremId || $tremId < 1
) {
    definir_flash("erro", "Não foi possível cadastrar o sensor. Confira os dados.");
    header("Location: cadastrar_sensor.php");
    exit;
}

try {
    $verificarTrem = $conexao->prepare("SELECT id FROM trens WHERE id = ?");
    $verificarTrem->bind_param("i", $tremId);
    $verificarTrem->execute();

    $tremExiste = $verificarTrem->get_result()->num_rows > 0;
    $verificarTrem->close();

    if (!$tremExiste) {
        definir_flash("erro", "Trem selecionado inválido.");
        header("Location: cadastrar_sensor.php");
        exit;
    }

    $stmt = $conexao->prepare("INSERT INTO sensores (nome, identificacao, tipo_sensor, localizacao, trem_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nome, $identificacao, $tipo, $localizacao, $tremId);

    if ($stmt->execute()) {
        $stmt->close();

        definir_flash("sucesso", "Sensor cadastrado com sucesso.");
        header("Location: crud_sensor.php");
        exit;
    } else {
        $stmt->close();

        definir_flash("erro", "Erro ao cadastrar sensor.");
        header("Location: cadastrar_sensor.php");
        exit;
    }

} catch (mysqli_sql_exception $erro) {
    error_log("Falha ao cadastrar sensor: " . $erro->getMessage());

    definir_flash("erro", "Erro ao cadastrar sensor.");
    header("Location: cadastrar_sensor.php");
    exit;
}
