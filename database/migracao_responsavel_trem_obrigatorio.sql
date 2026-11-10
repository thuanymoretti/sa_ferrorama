USE sa_ferrorama;

-- Execute depois de atribuir um usuário a cada trem pela tela Editar trem
-- e confirmar que a consulta abaixo não retorna linhas.
SELECT id, identificacao
FROM trens
WHERE usuario_id IS NULL;

ALTER TABLE trens
    MODIFY usuario_id INT NOT NULL;
