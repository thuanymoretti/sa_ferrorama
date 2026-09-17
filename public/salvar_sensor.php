<?php

include "../infra/conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST["nome_sensor"];
    $localizacao = $_POST["localizacao"];
    $tipo = $_POST["tipoDado"];
    $trem_id = $_POST["trem_id"];

    $identificacao = $nome;

    $sql = "INSERT INTO sensores 
            (nome, identificacao, tipo_sensor, localizacao, trem_id)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(  "ssssi",  $nome, $identificacao,  $tipo,  $localizacao, $trem_id  );

    if ($stmt->execute()) {

        header("Location: cadastrar_sensor.php?sucesso=1");
        exit;

    } else {

        echo "Erro ao salvar sensor: " . $stmt->error;
    }

    $stmt->close();
}

$conexao->close();

?>