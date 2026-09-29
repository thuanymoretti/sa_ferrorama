<?php

require_once "../infra/conexao.php";

$busca = isset($_GET["busca"]) ? trim($_GET["busca"]) : "";

// Se o usuário pesquisou alguma coisa
if ($busca != "") {

    $sql = "SELECT * FROM usuarios WHERE nome LIKE ? OR email LIKE ? ORDER BY nome";
    $stmt = $conexao->prepare($sql);
    $pesquisa = "%" . $busca . "%";
    $stmt->bind_param("ss", $pesquisa, $pesquisa);
    $stmt->execute();
    $resultado = $stmt->get_result();

// Se não pesquisou, mostra todos
} else {
    $sql = "SELECT * FROM usuarios ORDER BY nome";
    $resultado = $conexao->query($sql);
}
?>

<!DOCTYPE html>
<html lang="pt-br"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários cadastrados</title>        
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/style/style.css">
    <link rel="icon"  href="../assets/icons/TREM_AZUL.svg" type="image/x-icon">
</head>
<body>

    <header class="cabecalho">
        <h2>Bem vindo!!</h2>
</header>
    <div class="layout">
        <aside class="menu-lateral">
            <a href="home.php" class="item">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard </a>

            <a href="cadastrar_sensor.html" class="item">
                <i class="bi bi-cpu-fill"></i>Sensores</a>


            <a href="crud_trens.php" class="item">
                <i class="bi bi-file-earmark-bar-graph-fill"></i> Trens</a>


            <a href="crud_usuarios.php" class="item ativo">
                <i class="bi bi-people-fill"></i> Cadastrados </a>


            <a href="crud_viagens.php" class="item">
                <i class="bi bi-gear-fill"></i> Viagens</a>
        </aside>


        <main class="conteudo">

            <div class="planilha_usuarios">
                <div class="titulo_planilha">
                    <h2>Usuários cadastrados</h2>

                    <a href="cadastro_usuario.php" class="btn btn-primary">
                        <i class="bi bi-person-plus-fill"></i> Novo Usuário </a>
                </div>

                <div class="barra-pesquisa">
                    <form method="GET">
                        <input type="text" name="busca" placeholder="Pesquisar por nome ou e-mail..."
                            value="<?php echo htmlspecialchars($busca); ?>">

                        <button type="submit">
                            <i class="bi bi-search"></i> Pesquisar </button>


                        <?php if ($busca != "") { ?>
                            <a href="crud_usuarios.php" class="btn-limpar">
                                <i class="bi bi-x-lg"></i> Limpar</a>
                        <?php } ?>
                    </form>
                </div>

                <div class="tabela-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>NOME</th>
                                <th>E-MAIL</th>
                                <th>TELEFONE</th>
                                <th>TIPO DE USUÁRIO</th>
                                <th>AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            
                            while ($usuario = $resultado->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td> <?php echo htmlspecialchars($usuario['nome']); ?> </td>
                                    <td> <?php echo htmlspecialchars($usuario['email']);?> </td>         
                                    <td> <?php echo htmlspecialchars($usuario['telefone']); ?> </td>
                                    <td> <?php echo htmlspecialchars($usuario['tipo_usuario']); ?> </td>


                                    <td class="acoes">
                                        <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-fill"></i> Editar </a>


                                        <a href="excluir_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash-fill"></i>Excluir </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous">
    </script>
</body>
</html>