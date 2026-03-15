/* ============================================================================
 * @file create_geo_dados.sql
 * @brief Script para criar e popular a base de dados "geo_dados".
 *
 * Este script:
 * 1. Limpa e reinicia a base de dados geo_dados.
 * 2. Cria tabelas: utilizadores, categorias, locais e fotos.
 * 3. Insere utilizadores de exemplo.
 * 4. Insere categorias com cores e letras para ícones.
 * 5. Insere dois locais pré-preenchidos na zona de Sesimbra.
 *
 * @note Apaga qualquer base de dados existente com o mesmo nome.
 * ============================================================================
 */

-- =====================================================
-- BASE DE DADOS
-- =====================================================

DROP DATABASE IF EXISTS geo_dados;

CREATE DATABASE geo_dados
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE geo_dados;

-- =====================================================
-- TABELA: UTILIZADORES
-- =====================================================

/**
 * @brief Tabela de utilizadores do sistema.
 *
 * Campos:
 * - id: chave primária auto-increment
 * - nome: nome do utilizador
 * - email: email único
 * - senha: hash da password
 * - tipo: admin ou normal
 * - criado_em: timestamp da criação
 */
CREATE TABLE utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'normal') NOT NULL DEFAULT 'normal',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- TABELA: CATEGORIAS
-- =====================================================

/**
 * @brief Tabela de categorias de locais.
 *
 * Cada categoria possui um nome, uma cor e letras para ícone.
 */
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    cor VARCHAR(7) NOT NULL,
    letras VARCHAR(3) NOT NULL
);

-- =====================================================
-- TABELA: LOCAIS
-- =====================================================

/**
 * @brief Tabela de locais.
 *
 * Armazena informação geográfica e de contacto de cada local.
 * Não contém coluna para foto, pois as fotos estão na tabela fotos.
 */
CREATE TABLE locais (
    id INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(150) NOT NULL,
    categoria_id INT NOT NULL,
    criado_por INT NULL,

    pais VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,

    morada TEXT,
    telefone VARCHAR(50),
    email VARCHAR(100),
    website VARCHAR(150),
    descricao TEXT,

    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,

    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    FOREIGN KEY (criado_por) REFERENCES utilizadores(id)
);

-- =====================================================
-- TABELA: FOTOS
-- =====================================================

/**
 * @brief Tabela de fotos dos locais.
 *
 * Relação 1-n: um local pode ter várias fotos.
 * - local_id: chave estrangeira para o local
 * - ficheiro: nome do ficheiro
 * - legenda: descrição da foto
 *
 * @note ON DELETE CASCADE garante que se um local for eliminado,
 *       as fotos associadas também são apagadas automaticamente.
 */
CREATE TABLE fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    local_id INT NOT NULL,
    ficheiro VARCHAR(255) NOT NULL,
    legenda VARCHAR(255),
    FOREIGN KEY (local_id) REFERENCES locais(id)
        ON DELETE CASCADE
);

-- =====================================================
-- INSERÇÃO DE CATEGORIAS
-- =====================================================

/**
 * @brief Insere categorias com cores e letras.
 */
INSERT INTO categorias (nome, cor, letras) VALUES
('Aeroporto', '#3498db', 'A'),
('Autoridade Policial', '#2c3e50', 'AP'),
('Hospital', '#e74c3c', 'H'),
('Centro de Saúde', '#e67e22', 'CS'),
('Bombeiros', '#c0392b', 'B'),
('Proteção Civil', '#8e44ad', 'PC'),
('Hotel', '#2ecc71', 'H'),
('Alojamento Local', '#1abc9c', 'AL'),
('Restaurante', '#d35400', 'R'),
('Café / Bar', '#6f4e37', 'CB'),
('Museu', '#9b59b6', 'M'),
('Sala de Espetáculos', '#34495e', 'SE'),
('Câmara Municipal', '#2980b9', 'CM'),
('Junta de Freguesia', '#16a085', 'JF'),
('Base Militar', '#7f8c8d', 'BM'),
('Porto / Marina', '#1f618d', 'PM'),
('Órgãos de Comunicação Social', '#566573', 'OCS'),
('Escola', '#f1c40f', 'E'),
('Universidade', '#273746', 'U'),
('Farmácia', '#27ae60', 'F'),
('Supermercado', '#f39c12', 'S'),
('Outros', '#7f7f7f', '?');

-- =====================================================
-- UTILIZADORES DE EXEMPLO
-- =====================================================

/**
 * @brief Insere utilizadores de exemplo
 */
INSERT INTO utilizadores (nome, email, senha, tipo) VALUES
(
  'Eduardo Pinto',
  'eduardo@exemplo.pt',
  '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFxrC1c7ZB5w7xjvR1Gx5h5zvX6E0K9S',
  'admin'
),
(
  'Ana Silva',
  'ana@exemplo.pt',
  '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFxrC1c7ZB5w7xjvR1Gx5h5zvX6E0K9S',
  'normal'
);

-- =====================================================
-- LOCAIS PRÉ-PREENCHIDOS – SESIMBRA
-- =====================================================

/**
 * @brief Insere dois locais de exemplo em Sesimbra
 */
INSERT INTO locais
(nome, categoria_id, criado_por, pais, cidade, morada, telefone, descricao, latitude, longitude)
VALUES
(
  'Câmara Municipal de Sesimbra',
  (SELECT id FROM categorias WHERE nome = 'Câmara Municipal'),
  1,
  'Portugal',
  'Sesimbra',
  'Rua Amélia Frade, 2970-635 Sesimbra',
  '212288500',
  'Edifício da Câmara Municipal de Sesimbra.',
  38.44404000,
  -9.10143000
),
(
  'Bombeiros Voluntários de Sesimbra',
  (SELECT id FROM categorias WHERE nome = 'Bombeiros'),
  1,
  'Portugal',
  'Sesimbra',
  'Rua 4 de Maio, 2970-657 Sesimbra',
  '212289130',
  'Corpo de bombeiros voluntários do concelho de Sesimbra.',
  38.44489000,
  -9.10391000
);
