<?php
require_once "../infra/conexao.php";


$sql = "SELECT * FROM sensores";
$stmt = $conexao->query($sql);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sensores cadastrados</title>

    <link rel="stylesheet" href="../assets/style/style.css">

</head>

<body>


    <header class="cabecalho">

        <h2>   Bem vindo!!  </h2>
<div class="layout">
            <aside class="menu-lateral">
                <a href="home.php" class="item ativo"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
                <a href="cadastrar_sensor.html" class="item"><i class="bi bi-cpu-fill"></i> Sensores</a>
                <a href="crud_trens.php" class="item"><i class="bi bi-file-earmark-bar-graph-fill"></i> Trens</a>
                <a href="crud_usuarios.php" class="item"><i class="bi bi-people-fill"></i> Cadastrados</a>
                <a href="crud_viagens.php" class="item"><i class="bi bi-gear-fill"></i> Viagens</a>
            </aside>
    </header>
 <table class="table table-borderless">

                    <thead>

                        <tr>
                            <th>NOME</th>
                            <th>LOCALIZAÇÃO</th>
                            <th>TIPO DE DADO</th>
                            <th>TREM VINCULADO</th>
                            <th>AÇÕES</th>
        
                        </tr>
                    </thead>

                    <tbody>
                        <div>
                        <?php while ($sensor = $stmt->fetch_assoc()) { ?>

                            <tr>
                                <td> <?php echo $sensor['nome']; ?></td>
                                <td> <?php echo $sensor['localizacao']; ?> </td>
                                <td> <?php echo $sensor['tipo_sensor']; ?> </td>
                                <td> <?php echo $sensor['trem_id']; ?> </td>
                            </td>

                                <td>
                                    <a href="editar_sensor.php?id=<?php echo $sensor['id']; ?>"> Editar </a>

                                    <a href="excluir_sensor.php?id=<?php echo $sensor['id']; ?>"> Excluir </a>
                                </td>
                            </tr>

                            
                            <?php } ?>
                            <br>
                            <br>
                           <a href="cadastrar_sensor.php" class="btn btn-primery"> Cadastrar novo sensor</a>

                    </div>
0