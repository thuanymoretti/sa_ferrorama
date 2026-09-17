<?php

include "../infra/conexao.php";

$id = $_POST["id"];
$nome = $_POST["nome"];
$localizacao = $_POST["localizacao"];
$ipo_sensor = $_POST["ipo_sensor"];
$trem_id = $_POST["trem_id"];

$sql = "UPDATE sensores SET nome='$nome', localizacao='$localizacao', tipo_sensor='$tipo_sensor', trem_id='$trem_id';

mysqli_query($conexao, $sql);
header("Location: ../index.html");