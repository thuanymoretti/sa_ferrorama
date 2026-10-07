<?php

require_once "infra/auth.php";

if (usuario_autenticado()) {
    header("Location: public/home.php");
    exit;
}

header("Location: public/login.php");
exit;