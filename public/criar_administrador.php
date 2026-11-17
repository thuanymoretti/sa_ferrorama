<?php
if (PHP_SAPI !== "cli") {
    http_response_code(404);
    exit;
}

require_once __DIR__ . "/../infra/conexao.php";
$transacaoAtiva = false;

function lerEntrada(string $rotulo): string
{
    fwrite(STDOUT, $rotulo);
    $entrada = fgets(STDIN);
    return trim($entrada === false ? "" : $entrada);
}

try {
    $existente = $conexao->query("SELECT id FROM usuarios WHERE tipo_usuario = 'Administrador' AND senha_hash IS NOT NULL AND senha_hash <> '' LIMIT 1");
    if ($existente->num_rows > 0) {
        fwrite(STDERR, "Já existe um administrador com senha. A configuração inicial foi encerrada.\n");
        exit(1);
    }

    $nome = lerEntrada("Nome do administrador: ");
    $email = strtolower(lerEntrada("E-mail: "));
    $telefone = preg_replace("/[^0-9]/", "", lerEntrada("Telefone (8 a 15 dígitos): "));
    $senha = lerEntrada("Senha (10 a 72 caracteres; entrada visível no terminal): ");
    $nomeValido = preg_match("/^[\p{L}\p{M} .'-]{2,100}$/u", $nome) === 1;

    if (!$nomeValido || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100
        || strlen($telefone) < 8 || strlen($telefone) > 15 || strlen($senha) < 10 || strlen($senha) > 72) {
        fwrite(STDERR, "Dados inválidos. Nenhum usuário foi criado.\n");
        exit(1);
    }

    $conexao->begin_transaction();
    $transacaoAtiva = true;
    $existente = $conexao->query("SELECT id FROM usuarios WHERE tipo_usuario = 'Administrador' AND senha_hash IS NOT NULL AND senha_hash <> '' FOR UPDATE");
    if ($existente->num_rows > 0) {
        $conexao->rollback();
        fwrite(STDERR, "Outro administrador foi configurado enquanto os dados eram informados. Nenhum usuário foi criado.\n");
        exit(1);
    }

    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $tipo = "Administrador";
    $stmt = $conexao->prepare("INSERT INTO usuarios (nome, email, telefone, tipo_usuario, senha_hash) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nome, $email, $telefone, $tipo, $hash);
    $stmt->execute();
    $stmt->close();
    $conexao->commit();
    $transacaoAtiva = false;
    fwrite(STDOUT, "Administrador criado. Entre pelo navegador com o e-mail e a senha cadastrados.\n");
} catch (mysqli_sql_exception $erro) {
    if ($transacaoAtiva) {
        $conexao->rollback();
    }
    fwrite(STDERR, $erro->getCode() === 1062 ? "Este e-mail já está cadastrado.\n" : "Não foi possível criar o administrador. Verifique a conexão e o esquema do banco.\n");
    exit(1);
}
