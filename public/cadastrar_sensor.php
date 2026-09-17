<?php

include "../infra/conexao.php";

$sql = "SELECT * FROM sensores ORDER BY nome";
$sensores = $conexao->query($sql);

?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Sensores</title>

</head>

<body>

<main>
<?php if (isset($_GET["sucesso"])) { ?>
    <h2>Sensor cadastrado com sucesso!</h2>
       <a href="../index.html"> <button type="button">Voltar para o início</button> </a>

<?php } else { ?>

    <h1>~ Cadastre um novo Sensor ~</h1>

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
    echo "<option value='Temperatura'>Temperatura</option>";
    $trens = $conexao->query(  "SELECT id, identificacao   FROM trens  ORDER BY identificacao" );
   while ($trem = $trens->fetch_assoc()) {

    ?>
   <option value="<?php echo $trem['id']; ?>"> <?php echo $trem['identificacao']; ?>  </option>
 
    <?php

    }

    ?>

</select>

        <br>
        <br>
        
        <button type="submit">  Cadastrar </button>
    </form>
<br>
    <a href="../index.php"> <button type="button"> Voltar para o início </button> </a>

<?php } ?>

</main>
</body>
</html>