USE sa_ferrorama;

ALTER TABLE usuarios
    ADD COLUMN senha_hash VARCHAR(255) NULL AFTER tipo_usuario;
