<?php

require_once "../infra/conexao.php";

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $telefone = $_POST["telefone"];
    $tipo_usuario = $_POST["tipo_usuario"];

    // Insere o usuário no banco
    $sql = "INSERT INTO usuarios (nome, email, telefone, tipo_usuario)
            VALUES (?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param("ssss", $nome, $email, $telefone, $tipo_usuario);

    if ($stmt->execute()) {
        echo "<script>
                alert('Usuário cadastrado com sucesso!');
                window.location.href = 'usuarios.php';
              </script>";
    } else {
        echo "<script>
                alert('Erro ao cadastrar usuário!');
              </script>";
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro de Usuários</title>

    <link rel="stylesheet" href="../assets/style/style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous">

    <link rel="icon" href="../assets/icons/TREM_AZUL.svg" type="image/x-icon">

</head>

<body class="login">

    <main>

        <div class="cadastro">

            <h2 id="titulo">Cadastro</h2>

            <p id="sub_titulo">
                Preencha os dados abaixo para realizar o registro no sistema.
            </p>

            <form class="row g-3" method="POST">

                <!-- Nome -->
                <div class="col-6">

                    <label for="nome" class="form-label">
                        Nome Completo
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="nome"
                        name="nome"
                        required
                    >

                </div>


                <!-- E-mail -->
                <div class="col-6">

                    <label for="email" class="form-label">
                        E-mail
                    </label>

                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        required
                    >

                </div>


                <!-- Telefone -->
                <div class="col-6">

                    <label for="telefone" class="form-label">
                        Telefone
                    </label>

                    <input
                        type="tel"
                        class="form-control"
                        id="telefone"
                        name="telefone"
                        required
                    >

                </div>


                <!-- Tipo de usuário -->
                <div class="col-6">

                    <label for="tipo_usuario" class="form-label">
                        Tipo de Usuário
                    </label>

                    <select
                        class="form-select"
                        id="tipo_usuario"
                        name="tipo_usuario"
                        required
                    >

                        <option value="" selected disabled>
                            Selecione o tipo
                        </option>

                        <option value="Administrador">
                            Administrador
                        </option>

                        <option value="Funcionario">
                            Funcionário
                        </option>

                    </select>

                </div>


                <!-- Botões -->
                <div class="col-12">

                    <button type="submit" class="btn btn-primary">
                        Cadastre-se
                    </button>

                    <a href="usuarios.php" class="btn btn-secondary">
                        Voltar
                    </a>

                </div>

            </form>

        </div>

    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous">
    </script>

</body>

</html>