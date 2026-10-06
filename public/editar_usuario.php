<?php
require_once "../infra/conexao.php";
$id = $_GET["id"];
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $telefone = $_POST["telefone"];
    $tipo_usuario = $_POST["tipo_usuario"];

    $sql = "UPDATE usuarios SET nome = ?, email = ?, telefone = ?, tipo_usuario = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssssi", $nome, $email, $telefone, $tipo_usuario, $id);


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
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar usuário | Ferrorama</title>
    <link rel="stylesheet" href="../assets/style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="pagina-cadastro">
    <header class="cabecalho">
        <h2>Editar usuário</h2>
        <a class="item" href="crud_usuarios.php">Voltar à lista</a>
    </header>

   <div class="layout">
        <aside class="menu-lateral">
        <a href="home.php" class="item "><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
        <a href="crud_sensor.php" class="item"><i class="bi bi-cpu-fill"></i> Sensores</a>
        <a href="crud_trens.php" class="item"><i class="bi bi-train-front-fill"></i> Trens</a>
        <?php if ($_SESSION["usuario"]["tipo_usuario"] === "Administrador"): ?>
            <a href="crud_usuarios.php" class="item ativo"><i class="bi bi-people-fill"></i> Cadastrados</a>
        <?php endif; ?>
        <a href="crud_viagens.php" class="item"><i class="bi bi-calendar-check-fill"></i> Viagens</a>
    </aside>

        <main class="conteudo cadastro-conteudo">
            <section class="cadastro">
                <h1 id="titulo">Editar cadastro</h1>
                <?php if ($erro !== ""): ?><p role="alert"><?= escapar($erro) ?></p><?php endif; ?>

                <form method="post" autocomplete="off">
                    <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">
                    <div class="linha-cadastro">
                        <div class="campo-cadastro">
                            <label for="nome">Nome</label>
                            <input type="text" id="nome" name="nome" maxlength="100" value="<?= escapar($usuario["nome"]) ?>" required></div>
                        <div class="campo-cadastro"><label for="email">E-mail</label><input type="email" id="email" name="email" maxlength="100" value="<?= escapar($usuario["email"]) ?>" required></div>
                    </div>

                    <div class="linha-cadastro">
                        <div class="campo-cadastro">
                            <label for="telefone">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" maxlength="15" value="<?= escapar($usuario["telefone"] ?? "") ?>" required>
                        </div>
                        
                        <div class="campo-cadastro">
                            <label for="tipo_usuario">Perfil</label>
                            <?php if ((int) $id === (int) $_SESSION["usuario"]["id"]): ?>
                                <input type="hidden" name="tipo_usuario" value="<?= escapar($usuario["tipo_usuario"]) ?>">
                                <input type="text" id="tipo_usuario" value="<?= escapar($usuario["tipo_usuario"]) ?>" disabled>
                            <?php else: ?>
                                <select id="tipo_usuario" name="tipo_usuario" required>
                                    <option value="Funcionario" <?= $usuario["tipo_usuario"] === "Funcionario" ? "selected" : "" ?>>Funcionário</option>
                                    <option value="Administrador" <?= $usuario["tipo_usuario"] === "Administrador" ? "selected" : "" ?>>Administrador</option></select>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="campo-cadastro">
                        <label for="senha">Nova senha (deixe em branco para manter)</label>
                    <input type="password" id="senha" name="senha" minlength="10" maxlength="72" autocomplete="new-password">
                </div>

                    <div class="botoes-cadastro">
                        <button type="submit" class="btn-cadastrar">Salvar alterações</button>
                        <a href="crud_usuarios.php" class="btn-voltar">Cancelar</a></div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
