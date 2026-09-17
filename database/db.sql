CREATE DATABASE sa_ferrorama;
USE sa_ferrorama;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefone VARCHAR(15),
    tipo_usuario ENUM('Administrador', 'Funcionario') NOT NULL
);

CREATE TABLE trens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identificacao VARCHAR(100) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    capacidade INT NOT NULL,
    status ENUM('Ativo', 'Inativo', 'Em Manutenção') NOT NULL
);
CREATE TABLE sensores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    identificacao VARCHAR(50) NOT NULL,
    tipo_sensor ENUM('Temperatura', 'Umidade', 'Pressao', 'Velocidade') NOT NULL,
    localizacao VARCHAR(100) NOT NULL,
    dados_adicionais VARCHAR(100),
    trem_id INT NOT NULL,
 FOREIGN KEY (trem_id) REFERENCES trens(id)
);


CREATE TABLE viagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trem_id INT NOT NULL,
    data_inicio DATETIME NOT NULL,
    data_chegada DATETIME,
    origem VARCHAR(100) NOT NULL,
    destino VARCHAR(100) NOT NULL,
    status ENUM('Em Andamento', 'Concluída', 'Cancelada') NOT NULL,
    FOREIGN KEY (trem_id) REFERENCES trens(id)
);