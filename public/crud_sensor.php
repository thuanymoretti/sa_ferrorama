<?php

require_once "../infra/auth.php";
exigir_login();
require_once "../infra/conexao.php";


$stmt = $conexao->query("SELECT id, nome, identificacao, localizacao, tipo_sensor, trem_id FROM sensores ORDER BY nome");
$csrf = token_csrf();

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

                        <h2>  Sensores do Ferrorama </h2>

    </header>


    <div class="layout">


        <aside class="menu-lateral">

            <a href="home.php" class="item">  Dashboard  </a>

            <a href="crud_sensor.php" class="item ativo"> Sensores  </a>

            <a href="crud_trens.php" class="item">  Trens </a>

            <?php if ($_SESSION["usuario"]["tipo_usuario"] === "Administrador"): ?>
                <a href="crud_usuarios.php" class="item">Cadastrados</a>
            <?php endif; ?>

            <a href="crud_viagens.php" class="item">  Viagens </a>

        </aside>


        <main class="conteudo">


            <div class="titulo_sensores">
                <div>
                    <h1 > Sensores cadastrados  </h1>              
                </div>
            </div>

            <?php if (isset($_GET["sucesso"])): ?><p role="status">Operação concluída.</p><?php endif; ?>
            <?php if (isset($_GET["erro"])): ?><p role="alert">Não foi possível concluir a operação. Verifique os dados informados.</p><?php endif; ?>


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


                    <tbody>


                        <?php while ($sensor = $stmt->fetch_assoc()) { ?>


                            <tr>

                                <td> #<?= (int) $sensor['id'] ?>  </td>
                                <td> <?= escapar($sensor['nome']) ?> </td>
                                <td> <?= escapar($sensor['identificacao']) ?> </td>
                                <td> <?= escapar($sensor['localizacao']) ?>  </td>
                                <td> <?= escapar($sensor['tipo_sensor']) ?> </td>
                                <td> <?= (int) $sensor['trem_id'] ?>  </td>


                               <td>
                                    <?php if ($_SESSION["usuario"]["tipo_usuario"] === "Administrador"): ?>
                                        <a href="editar_sensor.php?id=<?= (int) $sensor['id'] ?>">Editar</a>
                                        <form method="post" action="excluir_sensor.php" onsubmit="return confirm('Confirma a exclusão deste sensor?')" style="display:inline">
                                            <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>"><input type="hidden" name="id" value="<?= (int) $sensor['id'] ?>">
                                            <button type="submit">Excluir</button>
                                        </form>
                                    <?php else: ?>—<?php endif; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                 <?php if ($_SESSION["usuario"]["tipo_usuario"] === "Administrador"): ?><a href="cadastrar_sensor.php"><button type="button">Cadastrar novo sensor</button></a><?php endif; ?>
            </div>

        </main>
    </div>
</body>
</html>
