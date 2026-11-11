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
    $stmt = $conexao->prepare(" SELECT id, nome, email, telefone, tipo_usuario FROM usuarios WHERE nome LIKE ? OR email LIKE ? ORDER BY nome ");
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
    <title>Usuário | Ferrorama</title>
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
                    <?= escapar($flash["mensagem"]) ?></p>
            <?php endif; ?>

            <div class="planilha_dashboard">
                <div class="cabecalho-tabela">
                    <button type="button" onclick="abrirModalCadastro()">
                        <i class="bi bi-plus-circle-fill"></i>Cadastrar novo usuário</button>

                    <div class="barra-pesquisa">
                        <form method="get">
                            <input type="search" name="busca" maxlength="50" placeholder="Pesquisar por nome ou e-mail" value="<?= escapar($busca) ?>">
                            <?php if ($busca !== ""): ?>
                                <a href="crud_usuarios.php" class="btn-limpar"> Limpar pesquisa </a>
                            <?php endif; ?>
                        </form>
                    </div> 
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

                                        <button type="button" class="btn btn-sm btn-warning" onclick="abrirModalEdicao(this)" data-id="<?= (int) $usuario["id"] ?>" data-nome="<?= escapar($usuario["nome"]) ?>" data-email="<?= escapar($usuario["email"]) ?>" data-telefone="<?= escapar($usuario["telefone"] ?? "") ?>" data-perfil="<?= escapar($usuario["tipo_usuario"]) ?>">
                                            <i class="bi bi-pencil-fill">Editar</i> 
                                        </button>

                                        <form method="post" action="excluir_usuario.php" onsubmit="return confirm('Confirma a exclusão deste usuário?')">
                                            <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>" >
                                            <input type="hidden" name="id" value="<?= (int) $usuario["id"] ?>">
                                            <button class="btn-excluir" type="submit"> Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                            </tbody>
                </table>

                <div class="modal-edicao" id="modalEdicao" hidden>
                    <div class="conteudo-modal">
                        <div class="cabecalho-modal">
                            <h2>Editar usuário</h2>
                        </div>

                        <form method="post" action="editar_usuario.php">
                            <input type="hidden" name="id" id="editar_id">
                            <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>" >

                            <div class="campo-modal">
                                <label for="editar_nome">Nome completo</label>
                                <input type="text" id="editar_nome" name="nome" maxlength="100" required>
                            </div>

                            <div class="campo-modal">
                                <label for="editar_email">E-mail</label>
                                <input type="email" id="editar_email" name="email" maxlength="100" required>
                            </div>

                            <div class="campo-modal">
                                <label for="editar_telefone">Telefone</label>
                                <input type="tel" id="editar_telefone" name="telefone" inputmode="numeric" maxlength="15" required>
                            </div>

                            <div class="campo-modal">
                                <label for="editar_perfil">Perfil</label>
                                <select id="editar_perfil" name="tipo_usuario" required>
                                    <option value="Funcionario">Funcionário</option>
                                    <option value="Administrador">Administrador</option>
                                </select>
                            </div>

                            <div class="botoes-modal">
                                <button type="button" class="btn-cancelar-modal" onclick="fecharModalEdicao()"> Cancelar </button>
                                <button type="submit" class="btn-salvar-modal"> Salvar alterações </button>
                            </div>
                        </form>
                            </div>
                </div>

                
                <div class="modal-edicao" id="modalCadastro" hidden>
                    <div class="conteudo-modal">
                        <div class="cabecalho-modal">
                            <h2>Cadastrar novo usuário</h2>
                        </div>

                        <form method="post" action="cadastro_usuario.php">
                            <input type="hidden" name="token_csrf" value="<?= escapar($csrf) ?>">
                            <div class="campo-modal">
                                <label for="cadastro_nome">Nome completo</label>
                                <input type="text" id="cadastro_nome" name="nome" maxlength="100" required>
                            </div>

                            <div class="campo-modal">
                                <label for="cadastro_email">E-mail</label>
                                <input type="email" id="cadastro_email" name="email"  maxlength="100" required >
                            </div>

                            <div class="campo-modal">
                                <label for="cadastro_telefone">Telefone</label>

                                <input type="tel" id="cadastro_telefone" name="telefone" inputmode="numeric" maxlength="15" required >
                            </div>

                            <div class="campo-modal">
                                <label for="cadastro_perfil">Perfil</label>
                                <select id="cadastro_perfil" name="tipo_usuario" required>
                                    <option value="Funcionario">Funcionário</option>
                                    <option value="Administrador">Administrador</option>
                                </select>
                            </div>

                            <div class="campo-modal">
                                <label for="cadastro_senha">Senha</label>

                                <input type="password" id="cadastro_senha" name="senha"  minlength="10" maxlength="72"autocomplete="new-password" required >
                            </div>

                            <div class="botoes-modal">
                                <button type="button" class="btn-cancelar-modal" onclick="fecharModalCadastro()">Cancelar</button>
                                <button type="submit" class="btn-salvar-modal">Cadastrar usuário</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>

        // Abrir modal de edição
        function abrirModalEdicao(botao) {

            document.getElementById("editar_id").value = botao.dataset.id;
            document.getElementById("editar_nome").value = botao.dataset.nome;
            document.getElementById("editar_email").value = botao.dataset.email;
            document.getElementById("editar_telefone").value = botao.dataset.telefone;
            document.getElementById("editar_perfil").value = botao.dataset.perfil;

            document.getElementById("modalEdicao").hidden = false;

            document.body.style.overflow = "hidden";
        }

        // Fechar modal de edição
        function fecharModalEdicao() {

            document.getElementById("modalEdicao").hidden = true;

            document.body.style.overflow = "";
        }

        // Abrir modal de cadastro
        function abrirModalCadastro() {

            document.getElementById("modalCadastro").hidden = false;

            document.body.style.overflow = "hidden";
        }

        // Fechar modal de cadastro
        function fecharModalCadastro() {

            document.getElementById("modalCadastro").hidden = true;

            document.body.style.overflow = "";
        }

        // Fechar modal ao clicar fora da caixa
        document.getElementById("modalEdicao").addEventListener("click", function(event) {

            if (event.target === this) {
                fecharModalEdicao();
            }

        });

        document.getElementById("modalCadastro").addEventListener("click", function(event) {

            if (event.target === this) {
                fecharModalCadastro();
            }

        });

        // Fechar os modais com a tecla Esc
        document.addEventListener("keydown", function(event) {

            if (event.key === "Escape") {
                fecharModalEdicao();
                fecharModalCadastro();
            }

        });

    </script>

</body>
</html>
