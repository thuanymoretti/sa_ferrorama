<?php

include("../infra/conexao.php");

if (isset($_POST['cadastrar'])) {

    $identificacao = $_POST['identificacao'];
    $modelo = $_POST['modelo'];
    $capacidade = $_POST['capacidade'];
    $status = $_POST['status'];

    $sql = "INSERT INTO trens
            (identificacao, modelo, capacidade, status)
            VALUES
            ('$identificacao', '$modelo', '$capacidade', '$status')";

    mysqli_query($conexao, $sql);

    header("Location: crud_trens.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Trem</title>

    <link rel="stylesheet" href="../assets/style/style.css">

</head>

<body>

    <div class="container-sensor">

        <h1 class="titulo-sensor">Cadastrar Trem</h1>

        <form method="POST" class="form-sensor">

            <div class="grupo-input">

                <label for="identificacao">
                    Identificação
                </label>

                <input
                    type="text"
                    id="identificacao"
                    name="identificacao"
                    required
                >

            </div>

            <div class="grupo-input">

                <label for="modelo">
                    Modelo
                </label>

                <input
                    type="text"
                    id="modelo"
                    name="modelo"
                    required
                >

            </div>

            <div class="grupo-input">

                <label for="capacidade">
                    Capacidade
                </label>

                <input
                    type="number"
                    id="capacidade"
                    name="capacidade"
                    min="1"
                    required
                >

            </div>

            <div class="grupo-input">

                <label for="status">
                    Status
                </label>

                <select id="status" name="status" required>

                    <option value="">Selecione</option>

                    <option value="Ativo">
                        Ativo
                    </option>

                    <option value="Inativo">
                        Inativo
                    </option>

                    <option value="Em Manutenção">
                        Em Manutenção
                    </option>

                </select>

            </div>

            <button
                type="submit"
                name="cadastrar"
                class="btn-salvar">
                Cadastrar
            </button>

        </form>

        <a href="crud_trens.php">
            Voltar
        </a>

    </div>

</body>

</html>