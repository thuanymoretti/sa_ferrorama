<?php

require_once "../infra/auth.php";
exigir_login();

require_once "../infra/conexao.php";

$ehAdministrador = ($_SESSION["usuario"]["tipo_usuario"] ?? "") === "Administrador";

$mensagem = "";
$erro = "";
$csrf = token_csrf();
$flash = obter_flash();

if ($flash) {
    if ($flash["tipo"] === "sucesso") {
        $mensagem = $flash["mensagem"];
    } else {
        $erro = $flash["mensagem"];
    }
}


// CADASTRAR VIAGEM
if (isset($_POST["cadastrar"])) {

    exigir_administrador($conexao);
    validar_token_csrf();

    $trem_id = (int) $_POST["trem_id"];
    $data_inicio = $_POST["data_inicio"];
    $data_chegada = $_POST["data_chegada"];
    $origem = trim($_POST["origem"] ?? "");
    $destino = trim($_POST["destino"] ?? "");
    $status = $_POST["status"] ?? "";

    $statusPermitidos = ["Em Andamento", "Concluída", "Cancelada"];

    if (
        $trem_id > 0 &&
        !empty($data_inicio) &&
        !empty($data_chegada) &&
        $origem !== "" &&
        $destino !== "" &&
        in_array($status, $statusPermitidos, true)
    ) {

        $sql = "INSERT INTO viagens
                (trem_id, data_inicio, data_chegada, origem, destino, status)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "isssss",
            $trem_id,
            $data_inicio,
            $data_chegada,
            $origem,
            $destino,
            $status
        );

        if ($stmt->execute()) {
            $mensagem = "Viagem cadastrada com sucesso!";
        } else {
            $erro = "Erro ao cadastrar viagem.";
        }

        $stmt->close();

    } else {
        $erro = "Preencha todos os campos corretamente.";
    }
}


// EXCLUIR VIAGEM
if (isset($_POST["excluir"])) {

    exigir_administrador($conexao);
    validar_token_csrf();

    $id = filter_var(valor_post("excluir"), FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit("Identificador de viagem inválido.");
    }

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


// BUSCAR VIAGENS
$sqlViagens = "
    SELECT
        viagens.id,
        viagens.data_inicio,
        viagens.data_chegada,
        viagens.origem,
        viagens.destino,
        viagens.status,
        trens.identificacao,
        trens.modelo
    FROM viagens
    INNER JOIN trens ON viagens.trem_id = trens.id
    ORDER BY viagens.data_inicio DESC
";

$viagens = $conexao->query($sqlViagens);


// FUNÇÃO PARA EXIBIR OS DADOS COM SEGURANÇA
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

    <link rel="stylesheet" href="../assets/style/style.css">

</head>

<body>

    <!-- CABEÇALHO -->
    <header class="cabecalho">

        <h2>Olá, <?= escapar($_SESSION["usuario"]["nome"]) ?></h2>

        <a href="home.php" class="item">Voltar ao painel</a>

    </header>


    <div class="layout">

        <!-- MENU LATERAL -->
        <aside class="menu-lateral">

            <a href="home.php" class="item">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            <a href="crud_sensor.php" class="item">
                <i class="bi bi-cpu-fill"></i> Sensores
            </a>

            <a href="crud_trens.php" class="item">
                <i class="bi bi-train-front-fill"></i> Trens
            </a>

            <?php if ($ehAdministrador): ?>

                <a href="crud_usuarios.php" class="item">
                    <i class="bi bi-people-fill"></i> Usuários
                </a>

            <?php endif; ?>

            <a href="crud_viagens.php" class="item ativo">
                <i class="bi bi-calendar-check-fill"></i> Viagens
            </a>

        </aside>


        <!-- CONTEÚDO PRINCIPAL -->
        <main class="conteudo">

            <div class="titulo_sensores">

                <div>

                    <h1>
                        <i class="bi bi-map-fill"></i> Viagens dos Trens </h1>

                    <p>
                        <?= $ehAdministrador
                            ? "Cadastre e acompanhe as viagens realizadas pelos trens."
                            : "Acompanhe as viagens realizadas pelos trens." ?>
                    </p>

                </div>

            </div>


            <!-- MENSAGENS -->
            <?php if ($mensagem !== ""): ?>

                <p class="mensagem-sucesso">
                    <?= e($mensagem) ?>
                </p>

            <?php endif; ?>


            <?php if ($erro !== ""): ?>

                <p class="mensagem-erro">
                    <?= e($erro) ?>
                </p>

            <?php endif; ?>


            <!-- BOTÃO DE CADASTRO -->
            <?php if ($ehAdministrador): ?>

                <a href="cadastrar_viagem.php">
                    <button type="button">
                        <i class="bi bi-plus-circle-fill"></i>
                        Cadastrar nova viagem
                    </button>
                </a>

            <?php endif; ?>
            
            
            <!-- TABELA DE VIAGENS -->
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
                                <th>ORIGEM</th>
                                <th>DESTINO</th>
                                <th>INÍCIO</th>
                                <th>CHEGADA</th>
                                <th>STATUS</th>
                                
                                <?php if ($ehAdministrador): ?>
                                    <th>AÇÕES</th>
                                    <?php endif; ?>
                                    
                                </tr>
                                
                            </thead>
                            
                            
                            <tbody>
                                
                                <?php if ($viagens && $viagens->num_rows > 0): ?>
                                    
                                    <?php while ($viagem = $viagens->fetch_assoc()): ?>
                                        
                                        <tr>
                                            
                                            <td>
                                                #<?= (int) $viagem["id"] ?>
                                            </td>
                                            
                                            <td>
                                                <?= e($viagem["identificacao"]) ?>
                                            </td>
                                            
                                            <td>
                                                <?= e($viagem["modelo"]) ?>
                                            </td>
                                            
                                            <td>
                                                <?= e($viagem["origem"]) ?>
                                            </td>
                                            
                                            <td>
                                                <?= e($viagem["destino"]) ?>
                                            </td>
                                            
                                            <td>
                                                <?= date("d/m/Y H:i", strtotime($viagem["data_inicio"])) ?>
                                            </td>
                                            
                                            <td>
                                                <?= date("d/m/Y H:i", strtotime($viagem["data_chegada"])) ?>
                                            </td>
                                            
                                            <td>
                                                <?= e($viagem["status"]) ?>
                                            </td>
                                            
                                            
                                            <?php if ($ehAdministrador): ?>
                                                
                                                <td class="acoes">
                                                    
                                                    <a
                                                    class="btn-editar"
                                                    href="editar_viagem.php?id=<?= (int) $viagem["id"] ?>">
                                                    Editar
                                                </a>
                                                
                                                <form
                                                method="post"
                                                onsubmit="return confirm('Deseja realmente excluir esta viagem?')"
                                                style="display:inline">
                                                
                                                <input
                                                type="hidden"
                                                name="token_csrf"
                                                value="<?= escapar($csrf) ?>">
                                                
                                                <button
                                                type="submit"
                                                name="excluir"
                                                value="<?= (int) $viagem["id"] ?>"
                                                class="btn-excluir">
                                                Excluir
                                            </button>
                                            
                                        </form>
                                        
                                    </td>
                                    
                                    <?php endif; ?>
                                    
                                </tr>
                                
                                <?php endwhile; ?>
                                
                                
                                <?php else: ?>
                                    
                                    <tr>
                                        
                                        <td
                                        colspan="<?= $ehAdministrador ? 9 : 8 ?>"
                                        class="text-center">
                                        
                                        Nenhuma viagem cadastrada.
                                        
                                    </td>
                                    
                                </tr>
                                
                                <?php endif; ?>
                                
                            </tbody>
                            
                        </table>

                        
                    </div>
                    
                </section>
                
                
                <!-- LOCAIS FERROVIÁRIOS -->
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
                        class="botao-mapa">
                        
                        <i class="bi bi-google"></i>
                        Abrir no Google Maps
                        
                    </a>
                    
                </div>
                
                
                <div class="mapa-links">
                    
                    <a
                    href="https://www.google.com/maps/search/museu+ferroviário/"
                    target="_blank">
                    
                    <i class="bi bi-building"></i>
                    Museus ferroviários
                    
                </a>
                
                
                <a
                href="https://www.google.com/maps/search/estação+ferroviária/"
                target="_blank">
                
                <i class="bi bi-train-front"></i>
                Estações ferroviárias
                
            </a>


                    <a
                        href="https://www.google.com/maps/search/ferromodelismo/"
                        target="_blank">

                        <i class="bi bi-geo-alt"></i>
                        Ferromodelismo

                    </a>

                </div>

            </section>

        </main>

    </div>

</body>

</html>
```