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

$stmt = $conexao->prepare(
    "SELECT
        (SELECT COUNT(*) FROM sensores WHERE trem_id = ?) AS sensores,
        (SELECT COUNT(*) FROM viagens WHERE trem_id = ?) AS viagens"
);
$stmt->bind_param("ii", $id, $id);
$stmt->execute();
$referencias = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ((int) $referencias["sensores"] > 0 || (int) $referencias["viagens"] > 0) {
    definir_flash(
        "erro",
        "Não é possível excluir este trem porque há sensores ou viagens vinculados a ele."
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
