<?php
require_once "../infra/auth.php";
exigir_login();
require_once "../infra/conexao.php";
exigir_administrador($conexao);

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit("Identificador inválido.");
}
$stmt = $conexao->prepare("SELECT id, nome, identificacao, localizacao, tipo_sensor, trem_id FROM sensores WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$sensor = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$sensor) {
    http_response_code(404);
    exit("Sensor não encontrado.");
}
$trens = $conexao->query("SELECT id, identificacao FROM trens ORDER BY identificacao")->fetch_all(MYSQLI_ASSOC);
$csrf = token_csrf();
$tipos = ["Temperatura", "Umidade", "Pressao", "Velocidade"];
?>
<!doctype html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Editar sensor | Ferrorama</title><link rel="stylesheet" href="../assets/style/style.css"></head>
<body class="pagina-cadastro">
    <header class="cabecalho"><h2>Editar sensor</h2><a href="crud_sensor.php" class="item">Voltar aos sensores</a></header>
    <main class="conteudo cadastro-conteudo"><section class="cadastro">
        <h1 id="titulo">Atualizar sensor</h1>
        <form action="atualizar_sensor.php" method="post">
            <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>"><input type="hidden" name="id" value="<?= (int) $sensor["id"] ?>">
            <div class="campo-cadastro"><label for="nome">Nome</label><input id="nome" name="nome" maxlength="100" value="<?= escapar($sensor["nome"]) ?>" required></div>
            <div class="campo-cadastro"><label for="identificacao">Identificação</label><input id="identificacao" name="identificacao" maxlength="100" value="<?= escapar($sensor["identificacao"]) ?>" required></div>
            <div class="campo-cadastro"><label for="localizacao">Localização</label><input id="localizacao" name="localizacao" maxlength="100" value="<?= escapar($sensor["localizacao"]) ?>" required></div>
            <div class="campo-cadastro"><label for="tipo_sensor">Tipo de Sensor</label><select id="tipo_sensor" name="tipo_sensor" required><?php foreach ($tipos as $tipo): ?><option value="<?= escapar($tipo) ?>" <?= $tipo === $sensor["tipo_sensor"] ? "selected" : "" ?>><?= escapar($tipo) ?></option><?php endforeach; ?></select></div>
            <div class="campo-cadastro"><label for="trem_id">Trem vinculado</label><select id="trem_id" name="trem_id" required><?php foreach ($trens as $trem): ?><option value="<?= (int) $trem["id"] ?>" <?= (int) $trem["id"] === (int) $sensor["trem_id"] ? "selected" : "" ?>><?= escapar($trem["identificacao"]) ?></option><?php endforeach; ?></select></div>
            <div class="botoes-cadastro"><button class="btn-cadastrar" type="submit">Salvar alterações</button><a href="crud_sensor.php" class="btn-voltar">Cancelar</a></div>
        </form>
    </section></main>
</body>
</html>
