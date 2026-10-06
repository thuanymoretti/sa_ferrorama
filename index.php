<?php
require_once "infra/auth.php";

if (usuario_autenticado()) {
    header("Location: public/home.php");
    exit;
}

<<<<<<< HEAD
    <title>Login</title>
    
<link rel="stylesheet" href="assets/style/style.css">

</head>

<body class="pagina-login">

    <main>

        <form>

            <div class="caixa">

                <h1>Login</h1>

                <label for="email">Email:</label>

                <input
                    type="email"
                    id="email"
                    placeholder="Digite seu email"
                >

                <label for="senha">Senha:</label>

                <input
                    type="password"
                    id="senha"
                    placeholder="Digite sua senha"
                >

                <button type="submit">Entrar</button>

                <p>
                    Não tem conta?
                    <a href="cadastro.php">Cadastre-se</a>
                </p>

            </div>

        </form>

    </main>

</body>

</html>
=======
header("Location: public/login.php");
exit;
>>>>>>> af564e55b5004dc244b36e64d678666c3f18d38b
