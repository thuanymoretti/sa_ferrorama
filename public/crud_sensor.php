<?php

require_once "../infra/auth.php";
exigir_login();
require_once "../infra/conexao.php";

$stmt = $conexao->query("
    SELECT sensores.id, sensores.nome, sensores.identificacao,
           sensores.localizacao, sensores.tipo_sensor,
           trens.identificacao AS trem_identificacao
    FROM sensores
    LEFT JOIN trens ON sensores.trem_id = trens.id
    ORDER BY sensores.nome
");
$ehAdministrador = $_SESSION["usuario"]["tipo_usuario"] === "Administrador";
$csrf = token_csrf();

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

                        <h2><i class="bi bi-train-front-fill"></i> Olá, <?= escapar((string) $_SESSION["usuario"]["nome"]) ?></h2>
                        <a href="home.php" class="item">Voltar ao painel</a>

    </header>


    <div class="layout">
        <aside class="menu-lateral">
        <a href="home.php" class="item "><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
        <a href="crud_sensor.php" class="item ativo"><i class="bi bi-cpu-fill"></i> Sensores</a>
        <a href="crud_trens.php" class="item"><i class="bi bi-train-front-fill"></i> Trens</a>
        <?php if ($_SESSION["usuario"]["tipo_usuario"] === "Administrador"): ?>
            <a href="crud_usuarios.php" class="item"><i class="bi bi-people-fill"></i> Usuários</a>
        <?php endif; ?>
        <a href="crud_viagens.php" class="item"><i class="bi bi-calendar-check-fill"></i> Viagens</a>
    </aside>



        <main class="conteudo">


            <div class="titulo_sensores">
                <div>
                    <h1 > Vizualização de Sensores  </h1>              
                </div>
            </div>

     <?php if ($ehAdministrador): ?>
    <a href="cadastrar_sensor.php">
        <button type="button">
            <i class="bi bi-plus-circle-fill"></i>
            Cadastrar novo sensor
        </button>
    </a>
<?php endif; ?>

<div class="planilha_dashboard">
    <h2 class="titulo-tabela">
        <i class="bi bi-list-ul"></i>
        Sensores cadastrados
    </h2>

    <table class="table table-borderless">
         

 <thead>
 <tr>
                            <th>ID</th> 
                             <th>NOME</th>
                             <th>IDENTIFICAÇÃO</th>
                             <th>LOCALIZAÇÃO</th>
                             <th>TIPO DE SENSOR</th>
                             <th>TREM VINCULADO</th>
                             <?php if ($ehAdministrador): ?><th>AÇÕES</th><?php endif; ?>

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
                                <td> <?= $sensor['trem_identificacao'] !== null ? escapar($sensor['trem_identificacao']) : 'Sem vínculo' ?> </td>

                               <?php if ($ehAdministrador): ?>
                               <td>
                                      <a class="btn-editar" href="editar_sensor.php?id=<?= (int) $sensor['id'] ?>">  Editar </a>
                                <form method="post" action="excluir_sensor.php" onsubmit="return confirm('Confirma a exclusão deste sensor?')" style="display:inline"> 
                                <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">
                                <input type="hidden" name="id" value="<?= (int) $sensor['id'] ?>">
                                <button class="btn-excluir" type="submit">Excluir</button>
                                </form>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</body>
</html>
