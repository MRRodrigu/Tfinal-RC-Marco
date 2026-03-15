CREATE TABLE utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'normal') NOT NULL DEFAULT 'normal',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    cor VARCHAR(7) NOT NULL,
    letras VARCHAR(3) NOT NULL
) ENGINE=InnoDB;

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
) ENGINE=InnoDB;

CREATE TABLE fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    local_id INT NOT NULL,
    ficheiro VARCHAR(255) NOT NULL,
    legenda VARCHAR(255),
    FOREIGN KEY (local_id) REFERENCES locais(id) ON DELETE CASCADE
) ENGINE=InnoDB;
