<?php

include "../infra/conexao.php";

$mensagem = "";
$erro = "";


if (isset($_POST["cadastrar"])) {

    $trem_id = (int) $_POST["trem_id"];
    $data_inicio = $_POST["data_inicio"];
    $data_chegada = $_POST["data_chegada"];

    if ($trem_id > 0 && !empty($data_inicio) && !empty($data_chegada)) {

        $sql = "INSERT INTO viagens 
                (trem_id, data_inicio, data_chegada)
                VALUES (?, ?, ?)";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "iss",
            $trem_id,
            $data_inicio,
            $data_chegada
        );

        if ($stmt->execute()) {
            $mensagem = "Viagem cadastrada com sucesso!";
        } else {
            $erro = "Erro ao cadastrar viagem.";
        }

        $stmt->close();

    } else {
        $erro = "Preencha todos os campos.";
    }
}



if (isset($_GET["excluir"])) {

    $id = (int) $_GET["excluir"];

    $sql = "DELETE FROM viagens WHERE id = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $mensagem = "Viagem excluída com sucesso!";
    } else {
        $erro = "Erro ao excluir viagem.";
    }

    $stmt->close();
}




$sqlTrens = "SELECT id, identificacao, modelo 
             FROM trens 
             ORDER BY identificacao";

$trens = $conexao->query($sqlTrens);



$sqlViagens = "
    SELECT 
        viagens.id,
        viagens.data_inicio,
        viagens.data_chegada,
        trens.identificacao,
        trens.modelo
    FROM viagens
    INNER JOIN trens
        ON viagens.trem_id = trens.id
    ORDER BY viagens.data_inicio DESC
";

$viagens = $conexao->query($sqlViagens);


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

    <title>Viagens | Ferrorama</title>

    <link
        rel="stylesheet"
        href="../assets/style/style.css"
    >

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


<!-- CABEÇALHO -->

<header class="cabecalho">

    <h2>

        <i class="bi bi-train-front-fill"></i>

        Ferrorama

    </h2>

    <a href="home.php" class="item">

        <i class="bi bi-grid-1x2-fill"></i>

        Dashboard

    </a>

</header>


<div class="layout">



    <aside class="menu-lateral">

        <a href="home.php" class="item">

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


        <a href="crud_viagens.php" class="item ativo">

            <i class="bi bi-calendar-check-fill"></i>

            Viagens

        </a>

    </aside>



    <main class="conteudo">


        <div class="titulo_sensores">

            <div>

                <h1 class="titulo_do_sensores">

                    <i class="bi bi-map-fill"></i>

                    Viagens dos Trens

                </h1>

                <p>

                    Cadastre e acompanhe as viagens realizadas pelos trens.

                </p>

            </div>

        </div>



        <?php if ($mensagem): ?>

            <div class="alert alert-success">

                <?= e($mensagem) ?>

            </div>

        <?php endif; ?>


        <?php if ($erro): ?>

            <div class="alert alert-danger">

                <?= e($erro) ?>

            </div>

        <?php endif; ?>



        <section class="container-sensor">

            <h2 class="titulo-sensor">

                Cadastrar nova viagem

            </h2>


            <form method="POST" class="form-sensor">


                <div class="linha-formulario">


                    <div class="grupo-input">

                        <label for="trem_id">

                            Trem

                        </label>


                        <select
                            name="trem_id"
                            id="trem_id"
                            required
                        >

                            <option value="">

                                Selecione um trem

                            </option>


                            <?php while ($trem = $trens->fetch_assoc()): ?>

                                <option value="<?= $trem["id"] ?>">

                                    <?= e($trem["identificacao"]) ?>

                                    -

                                    <?= e($trem["modelo"]) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <div class="grupo-input">

                        <label for="data_inicio">

                            Data e hora de início

                        </label>


                        <input
                            type="datetime-local"
                            name="data_inicio"
                            id="data_inicio"
                            required
                        >

                    </div>


                </div>


                <div class="linha-formulario">


                    <div class="grupo-input">

                        <label for="data_chegada">

                            Data e hora de chegada

                        </label>


                        <input
                            type="datetime-local"
                            name="data_chegada"
                            id="data_chegada"
                            required
                        >

                    </div>


                </div>


                <div class="botoes-formulario">

                    <button
                        type="submit"
                        name="cadastrar"
                        class="btn-salvar"
                    >

                        <i class="bi bi-check-lg"></i>

                        Cadastrar viagem

                    </button>

                </div>


            </form>

        </section>



        <section class="planilha_dashboard">

            <h2>

                <i class="bi bi-list-ul"></i>

                Viagens cadastradas

            </h2>


            <div class="table-responsive">

                <table class="table table-borderless">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>TREM</th>

                            <th>MODELO</th>

                            <th>INÍCIO</th>

                            <th>CHEGADA</th>

                            <th>AÇÕES</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if ($viagens && $viagens->num_rows > 0): ?>


                        <?php while ($viagem = $viagens->fetch_assoc()): ?>

                            <tr>

                                <td>

                                    #<?= $viagem["id"] ?>

                                </td>


                                <td>

                                    <?= e($viagem["identificacao"]) ?>

                                </td>


                                <td>

                                    <?= e($viagem["modelo"]) ?>

                                </td>


                                <td>

                                    <?= date(
                                        "d/m/Y H:i",
                                        strtotime($viagem["data_inicio"])
                                    ) ?>

                                </td>


                                <td>

                                    <?= date(
                                        "d/m/Y H:i",
                                        strtotime($viagem["data_chegada"])
                                    ) ?>

                                </td>


                                <td>

                                    <a
                                        class="botao_dashboard"
                                        href="?excluir=<?= $viagem["id"] ?>"
                                        onclick="return confirm('Deseja realmente excluir esta viagem?')"
                                        title="Excluir viagem"
                                    >

                                        <i class="bi bi-trash-fill"></i>

                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>


                    <?php else: ?>

                        <tr>

                            <td
                                colspan="6"
                                class="text-center"
                            >

                                Nenhuma viagem cadastrada.

                            </td>

                        </tr>

                    <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </section>



        <section class="mapa-ferrorama">

            <div class="mapa-cabecalho">

                <div>

                    <h2>

                        <i class="bi bi-geo-alt-fill"></i>

                        Locais ferroviários

                    </h2>

                    <p>

                        Encontre estações, museus ferroviários e locais relacionados a trens.

                    </p>

                </div>


                <a
                    href="https://www.google.com/maps/search/ferromodelismo/"
                    target="_blank"
                    class="botao-mapa"
                >

                    <i class="bi bi-google"></i>

                    Abrir no Google Maps

                </a>

            </div>


            <div class="mapa-links">

                <a
                    href="https://www.google.com/maps/search/museu+ferroviário/"
                    target="_blank"
                >

                    <i class="bi bi-building"></i>

                    Museus ferroviários

                </a>


                <a
                    href="https://www.google.com/maps/search/estação+ferroviária/"
                    target="_blank"
                >

                    <i class="bi bi-train-front"></i>

                    Estações ferroviárias

                </a>


                <a
                    href="https://www.google.com/maps/search/ferromodelismo/"
                    target="_blank"
                >

                    <i class="bi bi-geo-alt"></i>

                    Ferromodelismo

                </a>

            </div>

        </section>


    </main>

</div>


</body>

</html>