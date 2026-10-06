<?php
include("../infra/conexao.php");

$id = $_GET['id'];

$sql = "DELETE FROM trens WHERE id = $id";

mysqli_query($conexao, $sql);

header("Location: crud_trens.php");
exit;
?>