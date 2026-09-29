USE sa_ferrorama;

ALTER TABLE usuarios
    ADD COLUMN senha_hash VARCHAR(255) NULL AFTER tipo_usuario;


--abra um terminal na pasta do projeto e execute
--C:\xampp\php\php.exe scripts\criar_administrador.php
--atrves do CMD voce vai criar seu usuario administrador, que vai ser usado para logar no sistema, minha senha é 13245678910