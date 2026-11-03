-- Permite remover o vínculo de um sensor com um trem durante a edição.
ALTER TABLE sensores
    MODIFY trem_id INT NULL;
