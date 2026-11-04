<?php

require_once "../infra/auth.php";
exigir_login();

require_once "../infra/conexao.php";
exigir_administrador($conexao);

// Pesquisa de usuários
$buscaRecebida = $_GET["busca"] ?? "";
$busca = is_string($buscaRecebida) ? trim($buscaRecebida) : "";

if (strlen($busca) > 100) {
    $busca = substr($busca, 0, 100);
}

// Consulta ao banco
if ($busca !== "") {

    $stmt = $conexao->prepare(" SELECT id, nome, email, telefone, tipo_usuario FROM usuarios  WHERE nome LIKE ? OR email LIKE ? ORDER BY nome");
    $termo = "%" . $busca . "%";
    $stmt->bind_param("ss", $termo, $termo);
    $stmt->execute();
    $resultado = $stmt->get_result();

} else {

    $resultado = $conexao->query(" SELECT id, nome, email, telefone, tipo_usuario FROM usuarios ORDER BY nome ");
}

$usuarios = $resultado->fetch_all(MYSQLI_ASSOC);
$flash = obter_flash();
$csrf = token_csrf();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários cadastrados</title>
    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body>

    <header class="cabecalho">
        <h2>Olá, <?= escapar($_SESSION["usuario"]["nome"]) ?></h2>
        <a href="home.php" class="item">Voltar ao painel</a>
    </header>

    <div class="layout">
        <aside class="menu-lateral">
            <a href="home.php" class="item">Dashboard</a>
            <a href="crud_sensor.php" class="item">Sensores</a>
            <a href="crud_trens.php" class="item">Trens</a>
            <a href="crud_usuarios.php" class="item ativo">Usuários</a>
            <a href="crud_viagens.php" class="item">Viagens</a>
</aside>

        <main class="conteudo">
            <div class="titulo_sensores">
                <div>
                    <h1>Usuários cadastrados</h1>
                </div>

            </div>

            <?php if ($flash): ?>
                <p class="<?= $flash["tipo"] === "sucesso" ? "mensagem-sucesso" : "mensagem-erro" ?>" role="status">
                    <?= escapar($flash["mensagem"]) ?>
                </p>
            <?php endif; ?>

            <div class="planilha_dashboard">
                <div class="barra-pesquisa">
                    <form method="get">
                        <input type="search" name="busca" maxlength="100" placeholder="Pesquisar por nome ou e-mail" value="<?= escapar($busca) ?>">
                        <?php if ($busca !== ""): ?>

                            <a href="crud_usuarios.php" class="btn-limpar">Limpar pesquisa</a>
                        <?php endif; ?>
                    </form>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NOME</th>
                            <th>E-MAIL</th>
                            <th>TELEFONE</th>
                            <th>PERFIL</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (!$usuarios): ?>

                            <tr>
                                <td colspan="6">Nenhum usuário encontrado.</td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($usuarios as $usuario): ?>

                                <tr>
                                    <td>#<?= (int) $usuario["id"] ?></td>
                                    <td><?= escapar($usuario["nome"]) ?></td>
                                    <td><?= escapar($usuario["email"]) ?></td>
                                    <td><?= escapar($usuario["telefone"] ?? "") ?></td>
                                    <td><?= escapar($usuario["tipo_usuario"]) ?></td>
                                    
                                    <td class="acoes">
                                        <a class="btn-editar" href="editar_usuario.php?id=<?= (int) $usuario["id"] ?>"> Editar </a>
                                        <form method="post"action="excluir_usuario.php" onsubmit="return confirm('Confirma a exclusão deste usuário?')" >
                                            <input type="hidden" name="token_csrf"  value="<?= escapar($csrf) ?>" >
                                            <input type="hidden" name="id" value="<?= (int) $usuario["id"] ?>" >
                                            <button class="btn-excluir" type="submit"> Excluir </button>
                                        </form>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <a href="cadastro_usuario.php">
                    <button type="button">Cadastrar novo usuário</button>
                </a>

            </div>
        </main>
    </div>
</body>
</html>