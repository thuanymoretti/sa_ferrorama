<?php
include "../infra/conexao.php";

$id = $_GET["id"];

// Busca o usuário pelo ID
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $telefone = $_POST["telefone"];
    $tipo_usuario = $_POST["tipo_usuario"];

    // Atualiza o usuário
    $sql = "UPDATE usuarios SET nome = ?, email = ?, telefone = ?, tipo_usuario = ?WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssssi", $nome, $email, $telefone, $tipo_usuario, $id );

    if ($stmt->execute()) {
        echo "<script>
                alert('Usuário atualizado com sucesso!');
                window.location.href = 'crud_usuarios.php';
              </script>";
    } else {
        echo "<script>
                alert('Erro ao atualizar usuário!');
              </script>";
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/style/style.css">
    <link rel="icon" href="../assets/icons/TREM_AZUL.svg" type="image/x-icon">
</head>


<body class="pagina-cadastro">
    <header class="cabecalho">
        <h2>Bem vindo!!</h2>
    </header>
   
    <div class="layout">
        <aside class="menu-lateral">
            <a href="home.php" class="item">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard </a>

            <a href="crud_sensor.php" class="item">
                <i class="bi bi-cpu-fill"></i> Sensores </a>

            <a href="crud_trens.php" class="item">
                <i class="bi bi-file-earmark-bar-graph-fill"></i> Trens </a>

            <a href="crud_usuarios.php" class="item ativo">
                <i class="bi bi-people-fill"></i> Cadastrados </a>

            <a href="crud_viagens.php" class="item">
                <i class="bi bi-gear-fill"></i> Viagens </a>
        </aside>


        <main class="conteudo cadastro-conteudo">
            <div class="cadastro">
                <h2 id="titulo"> Editar Usuário </h2>

                <p id="sub_titulo"> Preencha os dados abaixo para realizar a atualização no sistema.</p>
                <form method="POST">
                    <div class="linha-cadastro">
                        <div class="campo-cadastro">
                            <label for="nome"> Nome Completo </label>
                            <input type="text" id="nome" name="nome" 
                            value="<?php echo htmlspecialchars($usuario['nome']);  ?>" 
                            required>
                        </div>

                        <div class="campo-cadastro">
                            <label for="email"> E-mail </label>

                            <input type="email" id="email" name="email"
                                value="<?php echo htmlspecialchars($usuario['email']); ?>"
                                required>
                        </div>
                    </div>

                    <div class="linha-cadastro">
                        <div class="campo-cadastro">
                            <label for="telefone">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" 
                            value="<?php echo htmlspecialchars($usuario['telefone']); ?>"
                                required>
                        </div>


                        <div class="campo-cadastro">
                            <label for="tipo_usuario"> Tipo de Usuário </label>

                            <select id="tipo_usuario" name="tipo_usuario" required>
                                <option value="" disabled> Selecione o tipo </option>
                                <option
                                    value="Administrador"
                                    <?php
                                    if ($usuario['tipo_usuario'] == 'Administrador') {
                                        echo 'selected';
                                         }?>>
                                    Administrador
                                </option>

                                <option
                                    value="Funcionario"
                                    <?php
                                    if ($usuario['tipo_usuario'] == 'Funcionario') {
                                        echo 'selected';
                                    } ?>>
                                    Funcionário
                                </option>
                            </select>
                        </div>
                    </div>


                    <div class="botoes-cadastro">
                        <button type="submit" class="btn-cadastrar">
                            <i class="bi bi-check-circle-fill"></i>
                             Atualizar
                        </button>

                        <a href="crud_usuarios.php" class="btn-voltar">
                            Voltar
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>
</html>