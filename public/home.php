
<?php

include "../infra/conexao.php";

$administrador = "Administrador";

$sql = "SELECT * FROM sensores ORDER BY nome";
$resultado = $conexao->query($sql);

$sensores = [];

if ($resultado) {
    while ($sensor = $resultado->fetch_assoc()) {
        $sensores[] = $sensor;
    }
}

$quantidadeSensores = count($sensores);

$quantidadeAlertas = 0;

function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, "UTF-8");
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Ferrorama</title>

<link rel="stylesheet" href="../assets/style/style.css">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>

<body>

<header class="cabecalho">

    <h2>

        <i class="bi bi-train-front-fill"></i>

        Bem-vindo, <?= e($administrador) ?>

    </h2>

    <a href="login.html" class="item">
        <i class="bi bi-box-arrow-right"></i>
        Sair
    </a>

</header>

    <div class="layout">
         <aside class="menu-lateral">

    <a href="home.php" class="item ativo">
        <i class="bi bi-grid-1x2-fill"></i>
        Dashboard
    </a>

    <a href="cadastrar_sensores.html" class="item">
        <i class="bi bi-cpu-fill"></i>
        Sensores
    </a>

    <a href="crud_trens.php" class="item">
        <i class="bi bi-train-front-fill"></i>
        Trens
    </a>

    <a href="crud_usuarios.php" class="item">
        <i class="bi bi-people-fill"></i>
        Cadastrados
    </a>

    <a href="crud_viagens.php" class="item">
        <i class="bi bi-calendar-check-fill"></i>
        Viagens
    </a>

</aside>



    <main class="conteudo">



        <section
            class="informacoes_dashboard"
            aria-label="Resumo do sistema"
        >



            <div class="informacoes">

                <h4>

                    <i class="bi bi-cpu-fill"></i>

                    Sensores cadastrados

                </h4>

                <h3>

                    <?= $quantidadeSensores ?>

                </h3>

            </div>



            <div class="informacoes">

                <h4>

                    <i class="bi bi-train-front-fill"></i>

                    Trens em operação

                </h4>

                <h3>

                    17

                </h3>

            </div>



            <div class="informacoes">

                <h4>

                    <i class="bi bi-exclamation-triangle-fill"></i>

                    Alertas

                </h4>

                <h3>

                    <?= $quantidadeAlertas ?>

                </h3>

            </div>



            <div class="informacoes">

                <h4>

                    <i class="bi bi-check-circle-fill"></i>

                    Disponibilidade

                </h4>

                <h3>

                    99,7%

                </h3>

            </div>


        </section>



        <section
            class="planilha_dashboard"
            aria-label="Lista de sensores"
        >

            <table class="table table-borderless">


                <thead>

                    <tr>

                        <th>ID</th>

                        <th>NOME</th>

                        <th>IDENTIFICAÇÃO</th>

                        <th>LOCALIZAÇÃO</th>

                        <th>TIPO DE DADO</th>

                        <th>AÇÕES</th>

                    </tr>

                </thead>


                <tbody>


                <?php if (count($sensores) > 0): ?>


                    <?php foreach ($sensores as $sensor): ?>


                        <tr>



                            <th scope="row">

                                #<?= e((string) $sensor["id"]) ?>

                            </th>



                            <td>

                                <?= e((string) $sensor["nome"]) ?>

                            </td>



                            <td>

                                <?= e((string) $sensor["identificacao"]) ?>

                            </td>



                            <td>

                                <?= e((string) $sensor["localizacao"]) ?>

                            </td>



                            <td>

                                <?= e((string) $sensor["tipo_sensor"]) ?>

                            </td>



                            <td>



                                <a
                                    class="botao_dashboard"
                                    href="monitoramento_tempo_real.php?id=<?= urlencode((string) $sensor["id"]) ?>"
                                    title="Visualizar sensor"
                                >

                                    <i class="bi bi-eye-fill"></i>

                                </a>



                                <a
                                    class="botao_dashboard"
                                    href="excluir_informacoes_trem.php?id=<?= urlencode((string) $sensor["id"]) ?>"
                                    title="Excluir sensor"
                                >

                                    <i class="bi bi-trash-fill"></i>

                                </a>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                <?php else: ?>



                    <tr>

                        <td
                            colspan="6"
                            class="text-center"
                        >

                            Nenhum sensor cadastrado.

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>

            </table>

        </section>


    </main>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>
