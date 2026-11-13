

DROP DATABASE IF EXISTS ferrovias;
CREATE DATABASE ferrovias CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ferrovias;


CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    login VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    papel ENUM('administrador', 'maquinista', 'cliente') NOT NULL DEFAULT 'cliente',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE trens (
    id_trem INT AUTO_INCREMENT PRIMARY KEY,
    prefixo_trem VARCHAR(50) NOT NULL UNIQUE,
    modelo_trem VARCHAR(100) NOT NULL,
    ano_fabricacao YEAR NULL,
    capacidade_toneladas DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    situacao_trem ENUM('ativo', 'manutencao', 'inativo') NOT NULL DEFAULT 'ativo',
    data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE sensores (
    id_sensor INT AUTO_INCREMENT PRIMARY KEY,
    fk_id_trem INT NOT NULL,
    codigo_sensor VARCHAR(50) NOT NULL UNIQUE,
    tipo_sensor VARCHAR(50) NOT NULL,
    limite_critico DECIMAL(10,2) NOT NULL,
    status_sensor ENUM('ativo', 'inativo', 'falha') NOT NULL DEFAULT 'ativo',
    FOREIGN KEY (fk_id_trem) REFERENCES trens(id_trem) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE leitura_sensor (
    id_leitura INT AUTO_INCREMENT PRIMARY KEY,
    fk_id_trem INT NOT NULL,
    fk_id_sensor INT NULL,
    velocidade_kmh DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    consumo_litros_hora DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    temperatura_motor_c DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    vibracao_mm_s DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fk_id_trem) REFERENCES trens(id_trem) ON DELETE CASCADE,
    FOREIGN KEY (fk_id_sensor) REFERENCES sensores(id_sensor) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX ix_leituras_trem ON leitura_sensor (fk_id_trem);
CREATE INDEX ix_leituras_data ON leitura_sensor (data_hora);


CREATE TABLE alertas (
    id_alerta INT AUTO_INCREMENT PRIMARY KEY,
    id_leitura INT NOT NULL,
    tipo VARCHAR(40) NOT NULL DEFAULT 'Operacional',
    severidade ENUM('atencao', 'critico') NOT NULL DEFAULT 'atencao',
    mensagem VARCHAR(255) NOT NULL,
    resolvido TINYINT(1) NOT NULL DEFAULT 0,
    data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_leitura) REFERENCES leitura_sensor(id_leitura) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX ix_alertas_severidade ON alertas (severidade);


CREATE TABLE rotas (
    id_rota INT AUTO_INCREMENT PRIMARY KEY,
    id_trem INT NOT NULL,
    origem VARCHAR(100) NOT NULL,
    destino VARCHAR(100) NOT NULL,
    data_viagem DATE NOT NULL,
    hora_partida TIME NOT NULL,
    hora_chegada TIME DEFAULT NULL,
    status ENUM('programada', 'em andamento', 'concluida', 'cancelada') NOT NULL DEFAULT 'programada',
    FOREIGN KEY (id_trem) REFERENCES trens(id_trem) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX ix_rotas_trem ON rotas (id_trem);
CREATE INDEX ix_rotas_status ON rotas (status);
CREATE INDEX ix_rotas_data ON rotas (data_viagem);


CREATE TABLE relatorios_emitidos (
    id_relatorio INT AUTO_INCREMENT PRIMARY KEY,
    fk_id_usuario INT NOT NULL,
    tipo_relatorio VARCHAR(50) NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    total_registros INT NOT NULL DEFAULT 0,
    data_emissao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fk_id_usuario) REFERENCES usuario(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO usuario (id, nome, login, senha, papel, ativo) VALUES
(1, 'Administrador Geral', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 1),
(2, 'Ricardo Pereira', 'pereira@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 1),
(3, 'Lucas Boeing', 'boeing@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'maquinista', 1),
(4, 'Carlos Maquinista', 'maquinista', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'maquinista', 1),
(5, 'Plínio Silva', 'silva_plinio@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente', 1),
(6, 'Ana Passageira', 'cliente', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente', 1);

INSERT INTO trens (id_trem, prefixo_trem, modelo_trem, ano_fabricacao, capacidade_toneladas, situacao_trem) VALUES
(1, 'GTT-001', 'Locomotiva Eletro-Diesel GE A22', 2018, 120.50, 'ativo'),
(2, 'GTT-002', 'Expresso Bala Siemens Velaro', 2022, 85.00, 'ativo'),
(3, 'GTT-003', 'Locomotiva de Carga EMD SD70', 2015, 250.00, 'manutencao'),
(4, 'GTT-004', 'Trem Regional Alstom Coradia', 2020, 110.00, 'inativo');

INSERT INTO sensores (id_sensor, fk_id_trem, codigo_sensor, tipo_sensor, limite_critico, status_sensor) VALUES
(1, 1, 'SENS-001-VEL', 'Velocidade', 110.00, 'ativo'),
(2, 1, 'SENS-001-TMP', 'Temperatura', 90.00, 'ativo'),
(3, 2, 'SENS-002-VEL', 'Velocidade', 180.00, 'ativo'),
(4, 2, 'SENS-002-TMP', 'Temperatura', 90.00, 'ativo'),
(5, 3, 'SENS-003-CON', 'Consumo', 250.00, 'falha'),
(6, 4, 'SENS-004-VEL', 'Velocidade', 100.00, 'inativo');

INSERT INTO leitura_sensor (id_leitura, fk_id_trem, fk_id_sensor, velocidade_kmh, consumo_litros_hora, temperatura_motor_c, vibracao_mm_s, data_hora) VALUES
(1, 1, 1, 72.50, 45.20, 75.00, 1.45, NOW() - INTERVAL 4 HOUR),
(2, 1, 2, 95.00, 58.40, 96.50, 4.80, NOW() - INTERVAL 2 HOUR),
(3, 2, 3, 145.00, 82.10, 78.00, 2.10, NOW() - INTERVAL 1 HOUR),
(4, 2, 4, 78.00, 64.00, 92.30, 3.95, NOW() - INTERVAL 35 MINUTE),
(5, 3, 5, 0.00, 0.00, 32.00, 0.00, NOW() - INTERVAL 15 MINUTE),
(6, 1, 1, 68.00, 41.90, 74.20, 1.30, NOW() - INTERVAL 5 MINUTE);

INSERT INTO alertas (id_alerta, id_leitura, tipo, severidade, mensagem, resolvido, data_hora) VALUES
(1, 2, 'Temperatura', 'critico', 'Superaquecimento detectado no motor principal (96,5°C)', 0, NOW() - INTERVAL 2 HOUR),
(2, 3, 'Velocidade', 'atencao', 'Velocidade operacional elevada registrada (145,0 km/h)', 0, NOW() - INTERVAL 1 HOUR),
(3, 4, 'Temperatura', 'critico', 'Temperatura do motor acima do limite seguro (92,3°C)', 0, NOW() - INTERVAL 35 MINUTE);

INSERT INTO rotas (id_rota, id_trem, origem, destino, data_viagem, hora_partida, hora_chegada, status) VALUES
(1, 1, 'Estação Central - Joinville', 'Estação Serra - Curitiba', CURDATE(), '06:30:00', '09:45:00', 'concluida'),
(2, 2, 'Estação Central - Joinville', 'Estação Litoral - Florianópolis', CURDATE(), '08:00:00', NULL, 'em andamento'),
(3, 1, 'Estação Serra - Curitiba', 'Estação Porto - Paranaguá', CURDATE(), '13:30:00', '16:15:00', 'programada'),
(4, 2, 'Estação Litoral - Florianópolis', 'Estação Central - Joinville', CURDATE(), '18:00:00', '20:30:00', 'programada'),
(5, 3, 'Pátio de Cargas - Mafra', 'Terminal de Cargas - Itajaí', CURDATE() - INTERVAL 1 DAY, '06:00:00', '12:00:00', 'concluida'),
(6, 4, 'Estação Central - Joinville', 'Estação Vale - Blumenau', CURDATE() - INTERVAL 1 DAY, '15:00:00', NULL, 'cancelada'),
(7, 1, 'Estação Central - Joinville', 'Estação Serra - Curitiba', CURDATE() + INTERVAL 1 DAY, '07:30:00', '11:00:00', 'programada');

INSERT INTO relatorios_emitidos (id_relatorio, fk_id_usuario, tipo_relatorio, titulo, data_inicio, data_fim, total_registros, data_emissao) VALUES
(1, 3, 'desempenho', 'Relatório de Desempenho (Velocidade Média e Máxima)', CURDATE() - INTERVAL 7 DAY, CURDATE(), 3, NOW() - INTERVAL 1 DAY),
(2, 1, 'manutencao', 'Relatório de Manutenção e Ocorrências de Alertas', CURDATE() - INTERVAL 30 DAY, CURDATE(), 3, NOW() - INTERVAL 3 HOUR);