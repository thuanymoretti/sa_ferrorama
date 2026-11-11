<?php
require_once "../infra/auth.php";
exigir_login();
require_once "../infra/conexao.php";
$stmt = $conexao->query("
    SELECT sensores.id, sensores.nome, sensores.identificacao,
           sensores.localizacao, sensores.tipo_sensor, sensores.trem_id,
           trens.identificacao AS trem_identificacao
    FROM sensores
    LEFT JOIN trens ON sensores.trem_id = trens.id
    ORDER BY sensores.nome
");

$ehAdministrador = $_SESSION["usuario"]["tipo_usuario"] === "Administrador";
$csrf = token_csrf();
$mensagem = "";
$erro = "";
$flash = obter_flash();

if ($flash) {
    if ($flash["tipo"] === "sucesso") {
        $mensagem = $flash["mensagem"];
    } else {
        $erro = $flash["mensagem"];
    }
}

$trens = $conexao->query("
    SELECT id, identificacao
    FROM trens
    ORDER BY identificacao
")->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensores cadastrados</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=20261111-menu-uniforme">
</head>
<body>
    <header class="cabecalho">

        <h2>
            <i class="bi bi-train-front-fill"></i>
            Olá, <?= escapar((string) $_SESSION["usuario"]["nome"]) ?>
        </h2>
        <a href="home.php" class="item">Voltar ao painel</a>
    </header>

    <div class="layout">
        <aside class="menu-lateral">

            <a href="home.php" class="item">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            <a href="crud_sensor.php" class="item ativo">
                <i class="bi bi-cpu-fill"></i> Sensores
            </a>

            <a href="crud_trens.php" class="item">
                <i class="bi bi-train-front-fill"></i> Trens
            </a>

            <?php if ($ehAdministrador): ?>
                <a href="crud_usuarios.php" class="item">
                    <i class="bi bi-people-fill"></i> Usuários
                </a>
            <?php endif; ?>

            <a href="crud_viagens.php" class="item">
                <i class="bi bi-calendar-check-fill"></i> Viagens
            </a>

        </aside>

        <main class="conteudo">

            <div class="titulo_sensores">
                <div>
                    <h1>Visualização de Sensores</h1>
                </div>
            </div>

            <?php if ($mensagem !== ""): ?>

                <p class="mensagem-sucesso" role="status"> <?= escapar($mensagem) ?>  </p>

            <?php endif; ?>


            <?php if ($erro !== ""): ?>
                <p class="mensagem-erro" role="alert">  <?= escapar($erro) ?> </p>

            <?php endif; ?>


            <div class="planilha_dashboard">
                <div class="cabecalho-tabela">
                    <h2 class="titulo-tabela"> <i class="bi bi-list-ul"></i>  Sensores cadastrados </h2>
                    <?php if ($ehAdministrador): ?>
                        <button type="button" onclick="abrirModalCadastro()">
                            <i class="bi bi-plus-circle-fill"></i>  Cadastrar novo sensor </button>
                    <?php endif; ?>
                </div>

                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NOME</th>
                            <th>IDENTIFICAÇÃO</th>
                            <th>LOCALIZAÇÃO</th>
                            <th>TIPO DE SENSOR</th>
                            <th>TREM VINCULADO</th>
                            <?php if ($ehAdministrador): ?>
                            <th>AÇÕES</th> <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($sensor = $stmt->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= (int) $sensor["id"] ?></td>
                                <td><?= escapar($sensor["nome"]) ?></td>
                                <td><?= escapar($sensor["identificacao"]) ?></td>
                                <td><?= escapar($sensor["localizacao"]) ?></td>
                                <td><?= escapar($sensor["tipo_sensor"]) ?></td>
                                <td> <?= $sensor["trem_identificacao"] !== null ? escapar($sensor["trem_identificacao"]) : "Sem vínculo" ?>
                                </td>

                                <?php if ($ehAdministrador): ?>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn-editar"
                                            onclick="abrirModalEdicao(this)"
                                            data-id="<?= (int) $sensor["id"] ?>"
                                            data-nome="<?= escapar($sensor["nome"]) ?>"
                                            data-identificacao="<?= escapar($sensor["identificacao"]) ?>"
                                            data-localizacao="<?= escapar($sensor["localizacao"]) ?>"
                                            data-tipo="<?= escapar($sensor["tipo_sensor"]) ?>"
                                            data-trem="<?= (int) ($sensor["trem_id"] ?? 0) ?>" >Editar </button>

                                        <form
                                            method="post"
                                            action="excluir_sensor.php"
                                            onsubmit="return confirm('Confirma a exclusão deste sensor?')"
                                            style="display:inline" >
                                            <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>" >
                                            <input type="hidden"  name="id"  value="<?= (int) $sensor["id"] ?>"  >
                                            <button class="btn-excluir" type="submit">  Excluir </button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <?php if ($ehAdministrador): ?>
        <div class="modal-edicao" id="modalCadastro" hidden>
            <div class="conteudo-modal">
                <div class="cabecalho-modal">
                    <h2>Cadastrar novo sensor</h2>
                </div>
                <form action="salvar_sensor.php" method="POST">
                    <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>" >
                    <input type="hidden" name="tipo" value="sensor">

                    <div class="campo-modal">
                        <label for="cad_nome">Nome do sensor:</label>
                        <input
                            type="text"  id="cad_nome" name="nome_sensor"  maxlength="100"  placeholder="Ex: Sensor Velocidade A1"  required >
                    </div>

                    <div class="campo-modal">
                        <label for="cad_identificacao">Identificação:</label>
                        <input
                            type="text"  id="cad_identificacao"  name="identificacao" maxlength="100" placeholder="Ex: SV-A1" required >
                    </div>

                    <div class="campo-modal">
                        <label for="cad_localizacao">Localização:</label>
                        <input  type="text"  id="cad_localizacao"  name="localizacao" maxlength="100" placeholder="Ex: Estação central - Eixo B"  required >
                    </div>

                    <div class="campo-modal">
                        <label for="cad_tipo">Tipo de sensor:</label>
                        <select id="cad_tipo" name="tipoDado" required>
                            <option value="" selected disabled>Selecione</option>
                            <option value="Velocidade">Velocidade</option>
                            <option value="Temperatura">Temperatura</option>
                            <option value="Pressao">Pressão</option>
                            <option value="fotoeletrico">Fotoelétrico</option>
                        </select>
                    </div>
                    <div class="campo-modal">

                        <label for="cad_trem">Trem vinculado:</label>
                        <select id="cad_trem" name="trem_id" required>
                            <option value="" selected disabled>Selecione um trem</option>
                            <?php foreach ($trens as $trem): ?>
                                <option value="<?= (int) $trem["id"] ?>">
                                    <?= escapar($trem["identificacao"]) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>
                    <div class="botoes-modal">

                        <button  type="submit"  class="btn-salvar-modal" > Cadastrar sensor </button>
                        <button type="button" class="btn-cancelar-modal" onclick="fecharModalCadastro()" > Cancelar </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal-edicao" id="modalEdicao" hidden>
    <div class="conteudo-modal">
             <div class="cabecalho-modal">
                    <h2>Editar sensor</h2>
                </div>

                <form action="atualizar_sensor.php" method="POST">

                    <input type="hidden" name="token_csrf"  value="<?= escapar($csrf) ?>"  >
                    <input type="hidden" name="id" id="edit_id">
                    <div class="campo-modal">
                        <label for="edit_nome">Nome do sensor:</label>
                        <input type="text"  id="edit_nome"  name="nome"   maxlength="100"   required  >
                    </div>

                    <div class="campo-modal">
                        <label for="edit_identificacao">Identificação:</label>
                        <input    type="text"  id="edit_identificacao" name="identificacao"  maxlength="100"  required  >

                    </div>


                    <div class="campo-modal">
                        <label for="edit_localizacao">Localização:</label>
                        <input  type="text"   id="edit_localizacao"  name="localizacao"  maxlength="100"   required >
                    </div>


                    <div class="campo-modal">
                        <label for="edit_tipo">Tipo de sensor:</label>
                        <select id="edit_tipo" name="tipo_sensor" required>
                            <option value="Velocidade">Velocidade</option>
                            <option value="Temperatura">Temperatura</option>
                            <option value="Pressao">Pressão</option>
                            <option value="fotoeletrico">Fotoelétrico</option>
                        </select>
                    </div>


                    <div class="campo-modal">
                        <label for="edit_trem">Trem vinculado:</label>
                        <select id="edit_trem" name="trem_id">
                            <option value="">Sem vínculo</option>
                            <?php foreach ($trens as $trem): ?>
                                <option value="<?= (int) $trem["id"] ?>">
                                    <?= escapar($trem["identificacao"]) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="botoes-modal">
                        <button type="button" class="btn-cancelar-modal" onclick="fecharModalEdicao()"> Cancelar </button>
                        <button type="submit" class="btn-salvar-modal" >  Salvar alterações </button>
                    </div>
                </form>
            </div>
        </div>

    <?php endif; ?>
    <script>
        function abrirModalCadastro() {
            const modal = document.getElementById("modalCadastro");
            modal.hidden = false;
            document.body.style.overflow = "hidden";
        }
        function fecharModalCadastro() {
            document.getElementById("modalCadastro").hidden = true;
            document.body.style.overflow = "";
        }
        function abrirModalEdicao(botao) {
            const modal = document.getElementById("modalEdicao");

            document.getElementById("edit_id").value = botao.dataset.id;
            document.getElementById("edit_nome").value = botao.dataset.nome;
            document.getElementById("edit_identificacao").value = botao.dataset.identificacao;
            document.getElementById("edit_localizacao").value = botao.dataset.localizacao;
            document.getElementById("edit_tipo").value = botao.dataset.tipo;
            document.getElementById("edit_trem").value = botao.dataset.trem;
            modal.hidden = false;
            document.body.style.overflow = "hidden";
        }

        function fecharModalEdicao() {
            document.getElementById("modalEdicao").hidden = true;
            document.body.style.overflow = "";
        }

        document.querySelectorAll(".modal-edicao").forEach(function(modal) {
            modal.addEventListener("click", function(event) {
                if (event.target === modal) {
                    modal.hidden = true;  document.body.style.overflow = "";
                }
            });
        });
        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
            fecharModalCadastro(); 
            fecharModalEdicao();  }
 });
    </script>
</body>
</html>