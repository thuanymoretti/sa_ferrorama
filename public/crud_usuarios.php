<?php
require_once "../infra/auth.php";
exigir_login();
require_once "../infra/conexao.php";
exigir_administrador($conexao);

$buscaRecebida = $_GET["busca"] ?? "";
$busca = is_string($buscaRecebida) ? trim($buscaRecebida) : "";
if (strlen($busca) > 100) {
    $busca = substr($busca, 0, 100);
}
if ($busca !== "") {
    $stmt = $conexao->prepare("SELECT id, nome, email, telefone, tipo_usuario FROM usuarios WHERE nome LIKE ? OR email LIKE ? ORDER BY nome");
    $termo = "%" . $busca . "%";
    $stmt->bind_param("ss", $termo, $termo);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $conexao->query("SELECT id, nome, email, telefone, tipo_usuario FROM usuarios ORDER BY nome");
}
$usuarios = $resultado->fetch_all(MYSQLI_ASSOC);
$flash = obter_flash();
$csrf = token_csrf();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuários | Ferrorama</title>
    <link rel="stylesheet" href="../assets/style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <header class="cabecalho">
        <h2>Gestão de usuários</h2>
        <a href="home.php" class="item">Voltar ao painel</a>
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

        <main class="conteudo">
            <section class="planilha_usuarios">
                <div class="titulo_planilha">
                    <h1>Usuários cadastrados</h1>
                    <a href="cadastro_usuario.php" class="btn btn-primary"><i class="bi bi-person-plus-fill">
                    </i> Novo usuário</a>
                </div>
            
                <?php if ($flash): ?>
                    <p class="alert alert-<?= escapar($flash["tipo"] === "sucesso" ? "success" : "danger") ?>" role="status"><?= escapar($flash["mensagem"]) ?></p>
                    <?php endif; ?>

                <div class="barra-pesquisa">
                    <form method="get">
                        <input type="search" name="busca" maxlength="100" placeholder="Pesquisar por nome ou e-mail" value="<?= escapar($busca) ?>">
                        <button type="submit"><i class="bi bi-search">
                        </i> Pesquisar</button>
                        <?php if ($busca !== ""): ?>
                            <a href="crud_usuarios.php" class="btn-limpar">Limpar</a><?php endif; ?></form>
                </div>

                <div class="tabela-container">
                    <table class="table">
                    <thead>
                        <tr><th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Perfil</th>
                        <th>Ações</th></tr>
                    </thead>
                    <tbody>
                    <?php if (!$usuarios): ?>
                        <tr><td colspan="5" class="text-center">Nenhum usuário encontrado.</td></tr>
                    <?php else: foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= (int) $usuario["id"] ?></td>
                            <td><?= escapar($usuario["nome"]) ?></td>
                            <td><?= escapar($usuario["email"]) ?></td>
                            <td><?= escapar($usuario["telefone"] ?? "") ?></td>
                            <td><?= escapar($usuario["tipo_usuario"]) ?></td>

                            <td class="acoes">
                                <a href="editar_usuario.php?id=<?= (int) $usuario["id"] ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-fill">
                                    </i> Editar</a>

                                <form method="post" action="excluir_usuario.php" onsubmit="return confirm('Confirma a exclusão deste usuário?')">
                                    <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $usuario["id"] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash-fill"></i> Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table></div>
            </section>
        </main>
    </div>
</body>
</html>
