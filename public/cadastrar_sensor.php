<?php

require_once "../infra/auth.php";
exigir_login();
include "../infra/conexao.php";
exigir_administrador($conexao);

$csrf = token_csrf();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Sensores</title>
    <link rel="stylesheet" href="../assets/style/style.css">

</head>
<body>

<main class="container-sensor">

<?php if (isset($_GET["sucesso"])) { ?>

    <h2 class="titulo-sensor">  Sensor cadastrado com sucesso! </h2>

    <a href="crud_sensor.php">
        <button type="button">Voltar para o início</button>
    </a>

<?php } else { ?>

    <?php if (isset($_GET["erro"])) { ?><p role="alert">Não foi possível cadastrar o sensor. Confira os dados e tente novamente.</p><?php } ?>

    <h1 class="titulo-sensor">  Cadastre um novo Sensor  </h1>

    <form action="salvar_sensor.php" method="POST" class="form-sensor">
 <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">
 <input type="hidden" name="tipo" value="sensor">

  <div class="grupo-input">
            <label>Nome do Sensor:</label>
            <input type="text" name="nome_sensor" required
                placeholder="Ex: Sensor Velocidade A1">
        </div>


        <div class="grupo-input">
            <label>Localização:</label>
            <input type="text" name="localizacao" required
                placeholder="Ex: Estação central - Eixo B">
        </div>


        <div class="grupo-input">
            <label>Tipo de Dado:</label>

            <select name="tipoDado" required>
                <option value="" selected disabled>Selecione</option>
                <option value="Velocidade">Velocidade</option>
                <option value="Temperatura">Temperatura</option>
                <option value="Pressao">Pressão</option>
                <option value="Umidade">Umidade</option>
            </select>
        </div>


        <div class="grupo-input">
            <label>Trem vinculado:</label>
            <select name="trem_id" required>
                <option value="">Selecione um trem</option>
             

                <?php
                $trens = $conexao->query(
                    "SELECT id, identificacao FROM trens ORDER BY identificacao"
                );
                while ($trem = $trens->fetch_assoc()) {
                ?>
                    <option value="<?php echo $trem['id']; ?>">
                        <?php echo $trem['identificacao']; ?>
                    </option>
                <?php  }   ?>

            </select>

        </div>


        <button type="submit"> Cadastrar  </button>

    </form>

 <a href="crud_sensor.php"> <button type="button"> Voltar para os sensores </button>  </a>


<?php } ?>

</main>

</body>

</html>
