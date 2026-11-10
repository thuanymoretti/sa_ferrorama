USE sa_ferrorama;

-- Adiciona o vínculo sem inventar responsáveis para os trens já cadastrados.
ALTER TABLE trens
    ADD COLUMN usuario_id INT NULL AFTER id;

ALTER TABLE trens
    ADD CONSTRAINT fk_trens_usuarios
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id);

-- Use a tela Editar trem para associar os registros que aparecerem abaixo.
SELECT id, identificacao
FROM trens
WHERE usuario_id IS NULL;
