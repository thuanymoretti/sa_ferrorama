<?php

header("Content-Type: application/json; charset=UTF-8");
include "../infra/conexao.php";

$sensores = $conexao->query("SELECT COUNT(*) AS total FROM sensores");
$trens = $conexao->query(
    "SELECT
        COUNT(*) AS total,
        SUM(status = 'Ativo') AS ativos,
        SUM(status = 'Em Manutenção') AS manutencao
     FROM trens"
);

$totalSensores = $sensores ? (int) $sensores->fetch_assoc()["total"] : 0;
$dadosTrens = $trens ? $trens->fetch_assoc() : ["total" => 0, "ativos" => 0, "manutencao" => 0];
$totalTrens = (int) $dadosTrens["total"];
$ativos = (int) $dadosTrens["ativos"];

echo json_encode([
    "sensores" => $totalSensores,
    "trens_ativos" => $ativos,
    "trens_manutencao" => (int) $dadosTrens["manutencao"],
    "disponibilidade" => $totalTrens > 0 ? round(($ativos / $totalTrens) * 100, 1) : 0,
]);
