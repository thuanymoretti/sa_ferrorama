
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
$identificacao = trim(valor_post("identificacao"));
$localizacao = trim(valor_post("localizacao"));
$tipo = valor_post("tipo_sensor");
$tremIdTexto = valor_post("trem_id");
$tremId = $tremIdTexto === "" ? null : filter_var($tremIdTexto, FILTER_VALIDATE_INT);

$tipos = ["Temperatura", "Umidade", "Pressao", "Velocidade", "fotoeletrico"];

if (
    !$id || $id < 1 ||
    $nome === "" || strlen($nome) > 100 ||
    $identificacao === "" || strlen($identificacao) > 50 ||
    $localizacao === "" || strlen($localizacao) > 100 ||
    !in_array($tipo, $tipos, true) ||
    ($tremId !== null && (!$tremId || $tremId < 1))
) {
    definir_flash("erro", "Não foi possível atualizar o sensor. Confira os dados.");
    header("Location: crud_sensor.php");
    exit;
}

try {
    if ($tremId !== null) {
        $verificarTrem = $conexao->prepare("SELECT id FROM trens WHERE id = ?");
        $verificarTrem->bind_param("i", $tremId);
        $verificarTrem->execute();

        $tremExiste = $verificarTrem->get_result()->num_rows > 0;
        $verificarTrem->close();

        if (!$tremExiste) {
            definir_flash("erro", "Trem selecionado inválido.");
            header("Location: crud_sensor.php");
            exit;
        }
    }

    $stmt = $conexao->prepare("UPDATE sensores SET nome = ?, identificacao = ?, localizacao = ?, tipo_sensor = ?, trem_id = ? WHERE id = ?");
    $stmt->bind_param("ssssii", $nome, $identificacao, $localizacao, $tipo, $tremId, $id);

    if ($stmt->execute()) {
        $stmt->close();

        definir_flash("sucesso", "Sensor atualizado com sucesso.");
        header("Location: crud_sensor.php");
        exit;
    } else {
        $stmt->close();

        definir_flash("erro", "Erro ao atualizar sensor.");
        header("Location: crud_sensor.php");
        exit;
    }

} catch (mysqli_sql_exception $erro) {
    error_log("Falha ao atualizar sensor: " . $erro->getMessage());

    definir_flash("erro", "Erro ao atualizar sensor.");
    header("Location: crud_sensor.php");
    exit;
}
