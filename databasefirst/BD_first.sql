CREATE DATABASE IF NOT EXISTS clinica_simples;
USE clinica_simples;


CREATE TABLE IF NOT EXISTS especialidades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS medicos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  crm VARCHAR(20) UNIQUE,
  especialidade_id INT,
  FOREIGN KEY (especialidade_id) REFERENCES especialidades(id)
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS pacientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  cpf CHAR(11) UNIQUE,
  data_nascimento DATE
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS consultas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  paciente_id INT NOT NULL,
  medico_id INT NOT NULL,
  data_hora DATETIME NOT NULL,
  FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
  FOREIGN KEY (medico_id) REFERENCES medicos(id)
) ENGINE=InnoDB;


INSERT INTO especialidades (nome) VALUES ('Clínico Geral'), ('Cardiologia');

INSERT INTO medicos (nome, crm, especialidade_id) VALUES 
('Dr. Pedro Alvares', 'CRM12345', 1);

INSERT INTO pacientes (nome, cpf, data_nascimento) VALUES 
('Maria da Silva', '11122233344', '1985-06-15');

INSERT INTO consultas (paciente_id, medico_id, data_hora) VALUES 
(1, 1, '2025-10-30 10:00:00');