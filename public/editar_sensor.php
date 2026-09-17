<?php

include "../infra/conexao.php";

$id = $_GET["id"];

// Busca o sensor
$sql = "SELECT * FROM sensores WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$sensor = mysqli_fetch_assoc($resultado);

// Busca os usuários
$usuarios = mysqli_query($conexao, "SELECT * FROM sensores ORDER BY identificacao");

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar sensor</title>

    <link rel="stylesheet" href="../style/style.css">
</head>

<body>


<main>

    <h2>Editar sensor</h2>

    <form action="atualizar.php" method="POST">

        <input type="hidden" name="id" value="<?= $sensor['id'] ?>">

          <form action="salvar_sensor.php" method="POST">
       <input type="hidden" name="tipo" value="sensor">

        <label>Nome do Sensor:</label>
        <input type="text" name="nome_sensor" required
         placeholder="Ex: Sensor Velocidade A1">
        <br>
<br>
        <label>Localização:</label>
        <input type="text" name="localizacao" required
          placeholder="Ex: Estação central - Eixo B">
        <br>
<br>
<?php

echo "Tipo de Dado:";

echo "<select name='tipoDado'>";

echo "<option value='Selecione'>Selecione</option>";
echo "<option value='Velocidade'>Velocidade</option>";
echo "<option value='Temperatura'>Temperatura</option>";
echo "<option value='Pressão'>Pressão</option>";

echo "</select>";

?>
        <br>
<br>
     <label>Trem vinculado:</label>

<select name="trem_id" required>

    <option value="">Selecione um trem</option>

    <?php
    $trens = $conexao->query(  "SELECT id, identificacao   FROM trens  ORDER BY identificacao" );
   while ($trem = $trens->fetch_assoc()) {

    ?>
   <option value="<?php echo $trem['id']; ?>"> <?php echo $trem['identificacao']; ?>  </option>
 
    <?php

    }
 ?>

        </select>

        <br><br>

        <input type="submit" value="Atualizar">

    </form>

    <br>

    <a href="../index.html">
        <button type="button">Voltar para o início</button>
    </a>

</main>

</body>

</html>