<?php
require_once "../infra/conexao.php";

$id = $_GET["id"];
$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>
            alert('Usuário excluído com sucesso!');
            window.location.href = 'crud_usuarios.php';
          </script>";

} else {
    echo "<script>
            alert('Erro ao excluir usuário!');
            window.location.href = 'crud_usuarios.php';
          </script>";
}

$stmt->close();
?>