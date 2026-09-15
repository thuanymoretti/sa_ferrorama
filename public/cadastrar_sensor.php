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
    <title>Cadastrar Animal</title>

</head>

<body>

<main>
<?php if (isset($_GET["sucesso"])) { ?>
    <h2>Sensor cadastrado com sucesso!</h2>
    <a href="../index.php">
        <button>Voltar para o início</button>
    </a>

<?php } else { ?>

    <h1>~ Cadastre um novo Sensor ~</h1>

    <form action="cadastrar.php" method="POST">
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

echo "<option value='Velocidade'>Velocidade</option>";
echo "<option value='Temperatura'>Temperatura</option>";
echo "<option value='Pressão'>Pressão</option>";

echo "</select>";

?>
        <br>
<br>
<?php

echo "Trem vinculado:";

echo "<select name='tipoDado'>";

echo "<option value='Trem 03 - Linha Vermelha'>Vermelha</option>";
echo "<option value='Trem 07 - Linha Azul'>Azul</option>";
echo "<option value='Trem 12 - Linha Verde'>Verde</option>";

echo "</select>";

?>
        <br>

        <br>
        <br>
        
        <button type="submit">
            Cadastrar
        </button>
    </form>
<br>
    <a href="../index.php">
        <button type="button">
            Voltar para o início
        </button>
    </a>

<?php } ?>

</main>
</body>
</html>