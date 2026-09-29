<?php

header("Content-Type: application/json; charset=UTF-8");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    require "../infra/conexao.php";

    $sensores = $conexao->query("SELECT COUNT(*) AS total FROM sensores");
    $trens = $conexao->query(
        "SELECT
            COUNT(*) AS total,
            COALESCE(SUM(status = 'Ativo'), 0) AS ativos,
            COALESCE(SUM(status = 'Em Manutenção'), 0) AS manutencao
         FROM trens"
    );
    $usuarios = $conexao->query(
        "SELECT
            COUNT(*) AS total,
            COALESCE(SUM(tipo_usuario = 'Administrador'), 0) AS administradores,
            COALESCE(SUM(tipo_usuario = 'Funcionario'), 0) AS funcionarios
         FROM usuarios"
    );

    $totalSensores = (int) $sensores->fetch_assoc()["total"];
    $dadosTrens = $trens->fetch_assoc();
    $dadosUsuarios = $usuarios->fetch_assoc();
    $totalTrens = (int) $dadosTrens["total"];
    $ativos = (int) $dadosTrens["ativos"];

    echo json_encode([
        "sensores" => $totalSensores,
        "trens_ativos" => $ativos,
        "trens_manutencao" => (int) $dadosTrens["manutencao"],
        "disponibilidade" => $totalTrens > 0 ? round(($ativos / $totalTrens) * 100, 1) : 0,
        "usuarios" => (int) $dadosUsuarios["total"],
        "administradores" => (int) $dadosUsuarios["administradores"],
        "funcionarios" => (int) $dadosUsuarios["funcionarios"],
    ], JSON_THROW_ON_ERROR);
} catch (Throwable $erro) {
    error_log("Falha ao carregar indicadores do dashboard: " . $erro->getMessage());
    http_response_code(500);
    echo json_encode(["erro" => "Não foi possível carregar os indicadores do painel."]);
}
