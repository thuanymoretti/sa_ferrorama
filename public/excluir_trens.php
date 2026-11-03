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
    header("Location: crud_trens.php");
    exit;
}

$stmt = $conexao->prepare("SELECT COUNT(*) AS total FROM sensores WHERE trem_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$quantidadeSensores = (int) $stmt->get_result()->fetch_assoc()["total"];
$stmt->close();

$stmt = $conexao->prepare(
    "SELECT id, status, origem, destino FROM viagens WHERE trem_id = ? ORDER BY id LIMIT 1"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$viagemVinculada = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($quantidadeSensores > 0 || $viagemVinculada) {
    if ($viagemVinculada && $viagemVinculada["status"] === "Em Andamento") {
        $motivo = "há uma viagem em andamento (" . $viagemVinculada["origem"] . " → " . $viagemVinculada["destino"] . "). Finalize ou cancele a viagem antes de excluir o trem";
    } elseif ($quantidadeSensores > 0 && $viagemVinculada) {
        $motivo = "há sensores e viagens vinculados a ele";
    } elseif ($quantidadeSensores > 0) {
        $motivo = "há sensores vinculados a ele";
    } else {
        $motivo = "há uma viagem cadastrada para ele. Remova a viagem antes de excluir o trem";
    }

    definir_flash(
        "erro",
        "Não é possível excluir este trem porque {$motivo}."
    );
    header("Location: crud_trens.php");
    exit;
}

try {
    $stmt = $conexao->prepare("DELETE FROM trens WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $excluiu = $stmt->affected_rows > 0;
    $stmt->close();

    definir_flash(
        $excluiu ? "sucesso" : "erro",
        $excluiu ? "Trem excluído com sucesso." : "Este trem não foi encontrado."
    );
} catch (mysqli_sql_exception $erro) {
    error_log("Falha ao excluir trem {$id}: " . $erro->getMessage());
    definir_flash("erro", "Não foi possível excluir o trem. Verifique se existem registros vinculados.");
}

header("Location: crud_trens.php");
exit;
