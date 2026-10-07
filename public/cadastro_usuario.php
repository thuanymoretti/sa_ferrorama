<?php
require_once "../infra/auth.php";
exigir_login();
require_once "../infra/conexao.php";
exigir_administrador($conexao);

$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validar_token_csrf();
    $nome = trim(valor_post("nome"));
    $email = strtolower(trim(valor_post("email")));
    $telefone = preg_replace("/[^0-9]/", "", valor_post("telefone"));
    $tipo = valor_post("tipo_usuario");
    $senha = valor_post("senha");
    $nomeValido = preg_match("/^[\p{L}\p{M} .'-]{2,100}$/u", $nome) === 1;

    if (!$nomeValido || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100
        || strlen($telefone) < 8 || strlen($telefone) > 15
        || !in_array($tipo, ["Administrador", "Funcionario"], true)
        || strlen($senha) < 10 || strlen($senha) > 72) {
        $erro = "Confira nome, e-mail e telefone. A senha deve ter de 10 a 72 caracteres.";
    } else {
        try {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conexao->prepare("INSERT INTO usuarios (nome, email, telefone, tipo_usuario, senha_hash) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nome, $email, $telefone, $tipo, $hash);
            $stmt->execute();
            $stmt->close();
            definir_flash("sucesso", "Usuário cadastrado com sucesso.");
            header("Location: crud_usuarios.php");
            exit;
        } catch (mysqli_sql_exception $excecao) {
            error_log("Falha ao cadastrar usuário: " . $excecao->getMessage());
            $erro = $excecao->getCode() === 1062 ? "Este e-mail já está cadastrado." : "Não foi possível cadastrar o usuário.";
        }
    }
}
$csrf = token_csrf();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro de usuário | Ferrorama</title>
    <link rel="stylesheet" href="../assets/style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="pagina-cadastro">
    <header class="cabecalho">
        <h2> Olá, <?= escapar($_SESSION["usuario"]["nome"]) ?> </h2>
        <a class="item" href="crud_usuarios.php">Voltar à lista</a>
    </header>

    <div class="layout">
        <aside class="menu-lateral">
        <a href="home.php" class="item "><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
        <a href="crud_sensor.php" class="item"><i class="bi bi-cpu-fill"></i> Sensores</a>
        <a href="crud_trens.php" class="item"><i class="bi bi-train-front-fill"></i> Trens</a>
        <?php if ($_SESSION["usuario"]["tipo_usuario"] === "Administrador"): ?>
            <a href="crud_usuarios.php" class="item ativo"><i class="bi bi-people-fill"></i> Usuários</a>
        <?php endif; ?>
        <a href="crud_viagens.php" class="item"><i class="bi bi-calendar-check-fill"></i> Viagens</a>
    </aside>

        <main class="conteudo cadastro-conteudo">
            <section class="cadastro">
                <h1 id="titulo">Novo usuário</h1>
                <p id="sub_titulo">O perfil administrativo só pode ser definido por um administrador autenticado.</p>
                <?php if ($erro !== ""): ?><p role="alert"><?= escapar($erro) ?></p><?php endif; ?>
                <form method="post" autocomplete="off">
                    <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">

                    <div class="linha-cadastro">
                        <div class="campo-cadastro">
                            <label for="nome">Nome completo</label>
                            <input type="text" id="nome" name="nome" maxlength="100" required>
                        </div>

                        <div class="campo-cadastro">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" maxlength="100" required>
                        </div>
                    </div>

                    <div class="linha-cadastro">
                        <div class="campo-cadastro">
                            <label for="telefone">Telefone (somente números)</label>
                            <input type="tel" id="telefone" name="telefone" inputmode="numeric" maxlength="15" required>
                        </div>

                        <div class="campo-cadastro">
                            <label for="tipo_usuario">Perfil</label>
                            <select id="tipo_usuario" name="tipo_usuario" required>
                                <option value="Funcionario">Funcionário</option>
                                <option value="Administrador">Administrador</option>
                            </select>
                        </div>
                    </div>

                    <div class="campo-cadastro">
                        <label for="senha">Senha inicial (mínimo 10 caracteres)</label>
                        <input type="password" id="senha" name="senha" minlength="10" maxlength="72" autocomplete="new-password" required>
                    </div>
                    <div class="botoes-cadastro">
                        <button type="submit" class="btn-cadastrar">Cadastrar usuário</button>
                        <a href="crud_usuarios.php" class="btn-voltar">Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
