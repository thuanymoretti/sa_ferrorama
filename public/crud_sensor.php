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

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>


    <header class="cabecalho">

        <h2>  Bem-vind! </h2>

    </header>


    <div class="layout">


        <aside class="menu-lateral">

            <a href="home.php" class="item">  Dashboard  </a>

            <a href="crud_sensor.php" class="item ativo"> Sensores  </a>

            <a href="crud_trens.php" class="item">  Trens </a>

            <a href="crud_usuarios.php" class="item">   Cadastrados </a>

            <a href="crud_viagens.php" class="item">  Viagens </a>

        </aside>


        <main class="conteudo">


            <div class="titulo_sensores">
                <div>
                    <h1 > Sensores cadastrados  </h1>              
                </div>
            </div>


            <div class="planilha_dashboard">

 <table class="table table-borderless">

 <thead>
 <tr>
                            <th>ID</th> 
                             <th>NOME</th>
                             <th>IDENTIFICAÇÃO</th>
                             <th>LOCALIZAÇÃO</th>
                             <th>TIPO DE DADO</th>
                             <th>TREM VINCULADO</th>
                             <th>AÇÕES</th>

                        </tr>

                    </thead>


                    <body>


                        <?php while ($sensor = $stmt->fetch_assoc()) { ?>


                            <tr>

                                <td> #<?php echo $sensor['id']; ?>  </td>
                                <td> <?php echo $sensor['nome']; ?> </td>
                                <td> <?php echo $sensor['identificacao']; ?> </td>
                                <td> <?php echo $sensor['localizacao']; ?>  </td>
                                <td> <?php echo $sensor['tipo_sensor']; ?> </td>
                                <td> <?php echo $sensor['trem_id']; ?>  </td>


                               <td>
                                    <a href="editar_sensor.php?id=<?php echo $sensor['id']; ?>"> Editar </a>
                                    <a href="excluir_sensor.php?id=<?php echo $sensor['id']; ?>"> Excluir </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </body>
                </table>
                 <a href="cadastrar_sensor.php"> <button type="button">Cadastrar novo sensor </button> </a>
            </div>

        </main>
    </div>
</body>
</html>