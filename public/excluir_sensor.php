<?php

include "../infra/conexao.php";

$id = $_GET["id"];

$sql = "DELETE FROM sensores WHERE id = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: crud_sensor.php");
exit();

?>